<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\TokenPrice;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
    public function index()
    {
        // 1. Ambil Layanan yang Aktif
        // Mengurutkan berdasarkan harga termurah
        $services = Service::where('is_active', true)
            ->orderBy('toratix_cost', 'asc')
            ->get();

        // 2. Ambil Paket Topup (Token Rates)
        // Diurutkan dari qty terkecil
        $tokenPackages = TokenPrice::orderBy('min_qty', 'asc')->get();

        // 3. Ambil Base Rate dari Agency Settings
        // Ambil nilai payout_rate_per_token, default 0 jika tidak ada
        $baseRate = DB::table('agency_settings')->value('payout_rate_per_token') ?? 0;

        // Return ke view 'pricing' (di folder resources/views/pricing.blade.php)
        return view('pricing', compact('services', 'tokenPackages', 'baseRate'));
    }
}
