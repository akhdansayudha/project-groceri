<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Transaction; // Menggunakan model Transaction dari vektora.sql
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            return response(['message' => 'Invalid notification'], 400);
        }

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraud = $notif->fraud_status;

        // Cari Invoice berdasarkan order_id (invoice_number)
        $invoice = Invoice::where('invoice_number', $orderId)->first();

        if (!$invoice) {
            return response(['message' => 'Invoice not found'], 404);
        }

        // Jika sudah paid, abaikan (idempotency)
        if ($invoice->status == 'paid') {
            return response(['message' => 'Already paid'], 200);
        }

        // Logika Status Midtrans
        if ($transaction == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $invoice->update(['status' => 'pending']);
                } else {
                    $this->makePaid($invoice);
                }
            }
        } else if ($transaction == 'settlement') {
            $this->makePaid($invoice);
        } else if ($transaction == 'pending') {
            $invoice->update(['status' => 'pending']);
        } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
            $invoice->update(['status' => 'failed']);
        }

        return response(['message' => 'Notification processed'], 200);
    }

    private function makePaid(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            // 1. Update Invoice
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => 'midtrans_auto'
            ]);

            // 2. Parse Jumlah Token dari Deskripsi Invoice
            // Format deskripsi: "Top Up 100 Toratix..."
            preg_match('/Top Up (\d+) Toratix/', $invoice->description, $matches);
            $tokenAmount = isset($matches[1]) ? (int)$matches[1] : 0;

            if ($tokenAmount > 0) {
                // 3. Tambah Saldo ke Wallet User
                $wallet = Wallet::firstOrCreate(['user_id' => $invoice->user_id]);

                $wallet->increment('balance', $tokenAmount);
                $wallet->increment('total_purchased', $tokenAmount); // Update lifetime purchased untuk Tier

                // 4. Catat Transaksi di Tabel Transactions
                Transaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'topup',
                    'amount' => $tokenAmount,
                    'description' => 'Top Up via Midtrans #' . $invoice->invoice_number,
                    'reference_id' => $invoice->id, // Menyimpan UUID invoice
                    'created_at' => now()
                ]);
            }
        });
    }
}
