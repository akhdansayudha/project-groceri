<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Models\TokenPrice;
use App\Models\Tier;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        Invoice::whereIn('status', ['unpaid', 'pending'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->update(['status' => 'cancelled']);

        $search = $request->query('search');
        $status = $request->query('status');

        $query = Invoice::with('user')->orderBy('created_at', 'desc');

        // Filter Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'ilike', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('full_name', 'ilike', "%{$search}%");
                    });
            });
        }

        // Filter Status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $invoices = $query->paginate(10)->withQueryString();

        // Statistik Header (Updated)
        $totalRevenue = Invoice::where('status', 'paid')->sum('amount');
        $unpaidAmount = Invoice::whereIn('status', ['unpaid', 'pending'])->sum('amount');
        $countUnpaid = Invoice::whereIn('status', ['unpaid', 'pending'])->count();
        $countCancelled = Invoice::where('status', 'cancelled')->count(); // <-- NEW

        return view('admin.invoices.index', compact('invoices', 'totalRevenue', 'unpaidAmount', 'countUnpaid', 'countCancelled'));
    }

    public function show($id)
    {
        $invoice = Invoice::with(['user', 'user.wallet'])->findOrFail($id);

        // --- LOGIC AUTO CANCEL JIKA EXPIRED (Sama seperti Client) ---
        if (in_array($invoice->status, ['unpaid', 'pending']) && $invoice->due_date && now()->greaterThan($invoice->due_date)) {
            $invoice->update(['status' => 'cancelled']);
        }

        return view('admin.invoices.show', compact('invoice'));
    }

    public function cancel($id)
    {
        $invoice = Invoice::findOrFail($id);

        if (!in_array($invoice->status, ['unpaid', 'pending'])) {
            return back()->with('error', 'Invoice tidak dapat dibatalkan (Status: ' . $invoice->status . ').');
        }

        $invoice->update(['status' => 'cancelled']);

        return back()->with('success', 'Invoice berhasil dibatalkan.');
    }

    public function markAsPaid($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status == 'paid') {
            return back()->with('error', 'Invoice ini sudah lunas.');
        }

        DB::transaction(function () use ($invoice) {
            // 1. Update Invoice
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => 'manual_admin',
            ]);

            // 2. Logic Token (Hitung konversi)
            $tokenAmount = 0;
            $prices = DB::table('token_prices')->orderBy('min_qty', 'asc')->get();

            foreach ($prices as $tier) {
                if ($tier->price_per_token > 0) {
                    $calculatedQty = $invoice->amount / $tier->price_per_token;
                    if (abs($calculatedQty - round($calculatedQty)) < 0.01) {
                        $rounded = round($calculatedQty);
                        if ($rounded >= $tier->min_qty && $rounded <= $tier->max_qty) {
                            $tokenAmount = $rounded;
                            break;
                        }
                    }
                }
            }

            if ($tokenAmount == 0 && $prices->isNotEmpty()) {
                $basePrice = $prices->first()->price_per_token;
                if ($basePrice > 0) {
                    $tokenAmount = floor($invoice->amount / $basePrice);
                }
            }

            // 3. Top Up Wallet & UPDATE TIER
            if ($tokenAmount > 0) {
                $wallet = Wallet::firstOrCreate(['user_id' => $invoice->user_id]);

                // Tambah saldo
                $wallet->increment('balance', $tokenAmount);
                $wallet->increment('total_purchased', $tokenAmount);

                // --- LOGIC UPDATE TIER (BARU DITAMBAHKAN) ---
                // Ambil total pembelian terbaru
                $currentTotal = $wallet->fresh()->total_purchased;

                // Cari Tier yang sesuai
                $newTier = Tier::where('min_toratix', '<=', $currentTotal)
                    ->where('max_toratix', '>=', $currentTotal)
                    ->orderBy('min_toratix', 'desc')
                    ->first();

                // Jika tier ditemukan dan berbeda dengan sekarang, update!
                if ($newTier && $wallet->current_tier_id !== $newTier->id) {
                    $wallet->update(['current_tier_id' => $newTier->id]);
                }
                // ---------------------------------------------

                // Catat Transaksi
                Transaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'topup',
                    'amount' => $tokenAmount,
                    'description' => 'Top Up Success via Invoice #' . $invoice->invoice_number,
                    'reference_id' => $invoice->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return back()->with('success', 'Invoice lunas! Token ditambahkan & Tier diperbarui.');
    }
}
