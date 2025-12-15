<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\Tier;        // Pastikan Model Tier diimport
use App\Models\Transaction; // Pastikan Model Transaction diimport

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Query Dasar
        $query = Invoice::where('user_id', $user->id);

        // --- TAMBAHAN LOGIC FILTER TANGGAL ---
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        // -------------------------------------

        $invoices = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // Agar parameter filter tetap ada saat ganti halaman

        // Stats (Tetap sama)
        $stats = [
            'unpaid' => Invoice::where('user_id', $user->id)->where('status', 'unpaid')->count(),
            'unpaid_amount' => Invoice::where('user_id', $user->id)->where('status', 'unpaid')->sum('amount'),
            'paid_total' => Invoice::where('user_id', $user->id)->where('status', 'paid')->sum('amount'),
        ];

        return view('client.invoices.index', compact('invoices', 'stats'));
    }

    public function show(Invoice $invoice)
    {
        if ($invoice->user_id !== Auth::id()) abort(403);
        return view('client.invoices.show', compact('invoice'));
    }

    /**
     * LOGIC UTAMA: Ditekan dari tombol "Simulate Payment" di show.blade.php
     */
    public function simulatePayment(Invoice $invoice)
    {
        // 1. Validasi
        if ($invoice->user_id !== Auth::id()) abort(403);
        if ($invoice->status == 'paid') return back()->with('error', 'Invoice ini sudah lunas.');

        try {
            DB::beginTransaction();

            // 2. Ubah Status Invoice jadi PAID
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => 'manual_simulation'
            ]);

            // 3. Ambil jumlah token dari deskripsi (Parsing angka)
            // Contoh Deskripsi: "Top Up 50 Toratix (TX)..." -> Mengambil angka 50
            preg_match('/\d+/', $invoice->description, $matches);
            $tokenAmount = isset($matches[0]) ? (int)$matches[0] : 0;

            $message = 'Pembayaran Berhasil! Status invoice menjadi Paid.';

            if ($tokenAmount > 0) {
                /** @var \App\Models\User $user */
                $user = Auth::user();
                $wallet = $user->wallet;

                // 4. Tambah Token ke Saldo Wallet
                $wallet->increment('balance', $tokenAmount);
                $wallet->increment('total_purchased', $tokenAmount);

                $message .= " Saldo bertambah +{$tokenAmount} TX.";

                // --- 5. LOGIC AUTO UPGRADE TIER (Sesuai Screenshot Supabase) ---
                // Refresh data wallet untuk mendapatkan total_purchased terbaru
                $currentTotal = $wallet->fresh()->total_purchased;

                // Cari tier yang sesuai dengan total pembelian saat ini
                // Logic: min_toratix <= total_purchased <= max_toratix
                $targetTier = Tier::where('min_toratix', '<=', $currentTotal)
                    ->where('max_toratix', '>=', $currentTotal)
                    ->orderBy('min_toratix', 'desc') // Ambil tier tertinggi yang cocok
                    ->first();

                // Jika ketemu tier baru yang lebih tinggi dari tier sekarang
                if ($targetTier && $targetTier->id != $wallet->current_tier_id) {
                    $wallet->update(['current_tier_id' => $targetTier->id]);
                    $message .= " Selamat! Level Anda naik ke " . $targetTier->name . ".";
                }

                // 6. Catat Riwayat Transaksi (History)
                Transaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'topup',
                    'amount' => $tokenAmount,
                    'description' => 'Top Up Success via Invoice #' . $invoice->invoice_number,
                    'reference_id' => $invoice->id
                ]);
            }

            DB::commit();
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
