<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\TokenPrice;
use App\Models\Tier;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        // Urutkan Token Price agar rapi
        $services = Service::orderBy('created_at', 'desc')->get();
        $tokenPrices = TokenPrice::orderBy('min_qty', 'asc')->get();
        $tiers = Tier::orderBy('min_toratix', 'asc')->get();

        return view('admin.services.index', compact('services', 'tokenPrices', 'tiers'));
    }

    // ... (Method Service CRUD tetap sama: create, store, edit, update, destroy) ...
    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'toratix_cost' => 'required|integer|min:1',
            'icon' => 'nullable|image|max:2048',
        ]);
        $iconPath = $request->hasFile('icon') ? $request->file('icon')->store('services', 'supabase') : null;
        Service::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description,
            'toratix_cost' => $request->toratix_cost,
            'icon_url' => $iconPath,
            'is_active' => true
        ]);
        return redirect()->route('admin.services.index')->with('success', 'Service baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'toratix_cost' => 'required|integer|min:1',
            'icon' => 'nullable|image|max:2048',
        ]);
        $data = [
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description,
            'toratix_cost' => $request->toratix_cost,
        ];
        if ($request->hasFile('icon')) {
            $data['icon_url'] = $request->file('icon')->store('services', 'supabase');
        }
        $service->update($data);
        return redirect()->route('admin.services.index')->with('success', 'Service berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return back()->with('success', 'Service berhasil dihapus.');
    }

    // --- TOKEN PRICE MANAGEMENT (UPDATED) ---

    public function storeTokenPrice(Request $request)
    {
        $request->validate([
            'min_qty' => 'required|integer|min:1',
            'max_qty' => 'required|integer|gt:min_qty',
            'price_per_token' => 'required|numeric|min:0',
            'label' => 'nullable|string'
        ]);

        TokenPrice::create($request->all());
        return back()->with('success', 'Paket token baru berhasil dibuat.');
    }

    public function updateTokenPrice(Request $request, $id)
    {
        $price = TokenPrice::findOrFail($id);

        $request->validate([
            'min_qty' => 'required|integer|min:1',
            'max_qty' => 'required|integer|gt:min_qty',
            'price_per_token' => 'required|numeric|min:0',
            'label' => 'nullable|string'
        ]);

        $price->update($request->all());
        return back()->with('success', 'Paket token berhasil diperbarui.');
    }

    public function destroyTokenPrice($id)
    {
        $price = TokenPrice::findOrFail($id);
        $price->delete();
        return back()->with('success', 'Paket token dihapus.');
    }

    // --- TIER MANAGEMENT (NEW) ---

    public function storeTier(Request $request)
    {
        // Validasi dan logic create tier bisa ditambahkan di sini jika butuh tombol "Add Tier"
        // Untuk saat ini kita fokus Update/Delete sesuai request
    }

    public function updateTier(Request $request, $id)
    {
        $tier = Tier::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'min_toratix' => 'required|integer',
            'max_toratix' => 'required|integer',
            'max_active_tasks' => 'required|integer',
            'max_workspaces' => 'required|integer',
            'benefits' => 'nullable|string' // Menerima string dipisah koma
        ]);

        // Convert comma separated string to JSON array
        $benefitsArray = $request->benefits ? array_map('trim', explode(',', $request->benefits)) : [];

        $tier->update([
            'name' => $request->name,
            'min_toratix' => $request->min_toratix,
            'max_toratix' => $request->max_toratix,
            'max_active_tasks' => $request->max_active_tasks,
            'max_workspaces' => $request->max_workspaces,
            'benefits' => json_encode($benefitsArray)
        ]);

        return back()->with('success', 'Membership Tier berhasil diperbarui.');
    }

    public function destroyTier($id)
    {
        $tier = Tier::findOrFail($id);
        // Cek jika ada user di tier ini (Opsional, untuk keamanan data)
        if ($tier->wallets()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus Tier ini karena masih ada User yang menggunakannya.');
        }
        $tier->delete();
        return back()->with('success', 'Membership Tier dihapus.');
    }
}
