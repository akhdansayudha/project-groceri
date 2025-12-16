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
            ->where('status', 'approved')
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

        $request->validate([
            'token_amount' => 'required|integer|min:1',
        ]);

        $tokenAmount = $request->token_amount;

        if ($user->wallet->balance < $tokenAmount) {
            return back()->with('error', 'Token balance is insufficient.');
        }

        if (empty($user->bank_account) || empty($user->bank_name)) {
            return back()->with('error', 'Please update your Bank Account details first.');
        }

        try {
            DB::beginTransaction();

            // 1. KURANGI SALDO (Balance otomatis jadi 0 jika ditarik semua)
            $user->wallet->decrement('balance', $tokenAmount);

            // 2. Hitung Rupiah
            $amountCurrency = $tokenAmount * $rate;

            // 3. Simpan ke staff_payouts
            $payoutId = DB::table('staff_payouts')->insertGetId([
                'user_id' => $user->id,
                'amount_token' => $tokenAmount,
                'amount_currency' => $amountCurrency,
                'status' => 'pending',
                'bank_name' => $user->bank_name,
                'bank_account' => $user->bank_account,
                'bank_holder' => $user->bank_holder,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Catat Transaksi Wallet (Type: payout)
            // Note: reference_id dikosongkan karena staff_payouts pakai BigInt, Transactions pakai UUID
            Transaction::create([
                'wallet_id' => $user->wallet->id,
                'type' => 'payout',
                'amount' => -$tokenAmount,
                'description' => "Payout Request #PY-{$payoutId}",
                'reference_id' => null
            ]);

            DB::commit();
            return back()->with('success', 'Payout request submitted! Balance updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process payout: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan Detail Payout
     */
    public function show($id)
    {
        $user = Auth::user();

        $payout = DB::table('staff_payouts')
            ->where('id', $id)
            ->where('user_id', $user->id) // Security: Cek kepemilikan
            ->first();

        if (!$payout) {
            abort(404);
        }

        return view('staff.finance.show', compact('payout', 'user'));
    }
}
