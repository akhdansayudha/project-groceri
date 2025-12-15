<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TokenController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $type = $request->query('type');

        // 1. Statistik (Bento Grid Data)
        $stats = [
            'circulating' => Wallet::sum('balance'),
            'total_purchased' => Wallet::sum('total_purchased'),
            'transactions_count' => Transaction::count(),
        ];

        // 2. Query Transaksi History
        $query = Transaction::with(['wallet.user'])
            ->orderBy('created_at', 'desc');

        // Filter Search (User Name)
        if ($search) {
            $query->whereHas('wallet.user', function ($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // Filter Type (Credit/Debit)
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        $transactions = $query->paginate(15)->withQueryString();

        // 3. List User untuk Dropdown Modal Adjustment
        $users = User::where('role', 'client')->orderBy('full_name')->get();

        return view('admin.tokens.index', compact('stats', 'transactions', 'users'));
    }

    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|integer|min:1',
            'description' => 'required|string|max:255'
        ]);

        DB::transaction(function () use ($request) {
            // Ambil atau Buat Wallet
            $wallet = Wallet::firstOrCreate(['user_id' => $request->user_id]);

            // Logic Saldo
            if ($request->type == 'credit') {
                $wallet->increment('balance', $request->amount);
                // Opsional: update total_purchased jika ini dianggap pembelian
                // $wallet->increment('total_purchased', $request->amount); 
            } else {
                if ($wallet->balance < $request->amount) {
                    throw new \Exception('Saldo user tidak mencukupi untuk pengurangan ini.');
                }
                $wallet->decrement('balance', $request->amount);
            }

            // Catat Transaksi
            Transaction::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'wallet_id' => $wallet->id,
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description . ' (Admin Adjustment)',
                'created_at' => now()
            ]);
        });

        return back()->with('success', 'Saldo token berhasil disesuaikan.');
    }
}
