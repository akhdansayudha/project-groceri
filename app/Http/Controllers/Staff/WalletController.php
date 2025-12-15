<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\StaffPayout; // Pastikan model ini ada (sesuai skema database)

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil Rate Payout dari Agency Settings
        $agencySetting = DB::table('agency_settings')->first();
        $rate = $agencySetting->payout_rate_per_token ?? 8000; // Default 8000 jika setting kosong

        // 2. Ambil History Transaksi (Token Flow)
        $transactions = Transaction::where('wallet_id', $user->wallet->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'trans_page');

        // 3. Ambil History Payout Requests (Uang Flow)
        $payouts = DB::table('staff_payouts')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'payout_page');

        // 4. Statistik
        $totalWithdrawn = DB::table('staff_payouts')
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount_currency');

        $pendingPayout = DB::table('staff_payouts')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount_currency');

        return view('staff.finance.earnings', compact(
            'user',
            'rate',
            'transactions',
            'payouts',
            'totalWithdrawn',
            'pendingPayout'
        ));
    }

    public function requestPayout(Request $request)
    {
        $user = Auth::user();
        $agencySetting = DB::table('agency_settings')->first();
        $rate = $agencySetting->payout_rate_per_token ?? 8000;

        // Validasi
        $request->validate([
            'token_amount' => 'required|integer|min:1',
        ]);

        $tokenAmount = $request->token_amount;

        // Cek Saldo
        if ($user->wallet->balance < $tokenAmount) {
            return back()->with('error', 'Insufficient token balance.');
        }

        // Cek Data Bank User
        if (empty($user->bank_account) || empty($user->bank_name)) {
            return back()->with('error', 'Please update your Bank Account details in Settings before requesting a payout.');
        }

        try {
            DB::beginTransaction();

            // 1. Kurangi Saldo Wallet
            $user->wallet->decrement('balance', $tokenAmount);

            // 2. Hitung Nominal Rupiah
            $amountCurrency = $tokenAmount * $rate;

            // 3. Buat Record Payout Request
            $payoutId = DB::table('staff_payouts')->insertGetId([
                'user_id' => $user->id,
                'amount_token' => $tokenAmount,
                'amount_currency' => $amountCurrency,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Catat Transaksi Wallet (Type: payout)
            Transaction::create([
                'wallet_id' => $user->wallet->id,
                'type' => 'payout', // Pastikan enum 'payout' ada di DB atau gunakan string
                'amount' => -$tokenAmount, // Nilai negatif karena pengurangan
                'description' => "Payout Request #PY-{$payoutId}",
                'reference_id' => null // Atau ID Payout jika tipe datanya UUID (di schema staff_payouts id-nya BigInt, jadi sesuaikan)
            ]);

            DB::commit();
            return back()->with('success', 'Payout request submitted successfully! Waiting for admin approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process payout: ' . $e->getMessage());
        }
    }
}
