<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Models\TokenPrice;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Invoice::with('user')->orderBy('created_at', 'desc');

        // Filter Search (Invoice Number / Client Name)
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

        // Statistik Header
        $totalRevenue = Invoice::where('status', 'paid')->sum('amount');
        $unpaidAmount = Invoice::where('status', 'unpaid')->sum('amount');
        $countUnpaid = Invoice::where('status', 'unpaid')->count();

        return view('admin.invoices.index', compact('invoices', 'totalRevenue', 'unpaidAmount', 'countUnpaid'));
    }

    public function show($id)
    {
        $invoice = Invoice::with(['user', 'user.wallet'])->findOrFail($id);
        return view('admin.invoices.show', compact('invoice'));
    }

    // Fitur Manual Confirm Payment
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

            // 2. LOGIC PENENTUAN JUMLAH TOKEN (DYNAMIC PRICING)
            $tokenAmount = 0;

            // Ambil semua data harga, urutkan dari qty terkecil (eceran) ke terbesar (grosir)
            // Pastikan Anda sudah membuat Model 'TokenPrice' yang terhubung ke tabel 'token_prices'
            $prices = DB::table('token_prices')->orderBy('min_qty', 'asc')->get();

            // A. Cek Logika Tier (Diskon Grosir)
            // Kita cek apakah Amount Invoice cocok dengan hasil perkalian (Qty * Harga Tier)
            foreach ($prices as $tier) {
                if ($tier->price_per_token > 0) {
                    $calculatedQty = $invoice->amount / $tier->price_per_token;

                    // Cek apakah hasilnya bilangan bulat (artinya cocok dengan harga tier ini)
                    // Menggunakan epsilon 0.01 untuk toleransi koma floating point
                    if (abs($calculatedQty - round($calculatedQty)) < 0.01) {
                        $rounded = round($calculatedQty);
                        // Cek apakah qty masuk dalam range tier tersebut (min_qty s/d max_qty)
                        if ($rounded >= $tier->min_qty && $rounded <= $tier->max_qty) {
                            $tokenAmount = $rounded;
                            break; // Ketemu! Keluar dari loop.
                        }
                    }
                }
            }

            // B. Fallback (Jika tidak cocok dengan tier manapun)
            // Misal admin buat invoice manual Rp 123.456 yang tidak pas dengan harga paket.
            // Maka kita bagi dengan harga ECERAN (Tier 1 / termurah) sebagai default.
            if ($tokenAmount == 0 && $prices->isNotEmpty()) {
                $basePrice = $prices->first()->price_per_token; // Ambil harga tier pertama
                if ($basePrice > 0) {
                    $tokenAmount = floor($invoice->amount / $basePrice);
                }
            }

            // 3. Eksekusi Top Up ke Wallet
            if ($tokenAmount > 0) {
                $wallet = Wallet::firstOrCreate(['user_id' => $invoice->user_id]);

                $wallet->increment('balance', $tokenAmount);
                $wallet->increment('total_purchased', $tokenAmount);

                // Catat Transaksi
                Transaction::create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'wallet_id' => $wallet->id,
                    'type' => 'topup', // Sesuai database (lowercase)
                    'amount' => $tokenAmount,
                    'description' => 'Top Up Success via Invoice #' . $invoice->invoice_number,
                    'reference_id' => $invoice->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return back()->with('success', 'Invoice lunas! Token telah ditambahkan ke user.');
    }
}
