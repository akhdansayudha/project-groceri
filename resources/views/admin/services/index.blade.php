@extends('admin.layouts.app')

@section('content')
    {{-- SWEETALERT CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- HEADER --}}
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Services & Pricing</h1>
            <p class="text-gray-500 text-sm">Atur katalog layanan, harga token, dan tier membership.</p>
        </div>
        <a href="{{ route('admin.services.create') }}"
            class="px-5 py-2.5 bg-black text-white rounded-xl text-sm font-bold hover:bg-gray-800 transition-all flex items-center gap-2 shadow-lg shadow-black/20">
            <i data-feather="plus" class="w-4 h-4"></i> New Service
        </a>
    </div>

    {{-- SECTION 1: SERVICES CATALOG --}}
    <div class="mb-10 fade-in">
        <div class="flex items-center gap-2 mb-4">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <i data-feather="grid" class="w-5 h-5"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Service Catalog</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($services as $service)
                <div
                    class="bg-white p-5 rounded-3xl border border-gray-200 shadow-sm hover:shadow-md transition-all group relative flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center border border-gray-100">
                                @if ($service->icon_url)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('supabase')->url($service->icon_url) }}"
                                        class="w-6 h-6 object-contain">
                                @else
                                    <i data-feather="box" class="w-6 h-6 text-gray-400"></i>
                                @endif
                            </div>
                            <div
                                class="px-3 py-1 bg-yellow-50 text-yellow-700 text-xs font-bold rounded-lg border border-yellow-100 flex items-center gap-1">
                                <i data-feather="zap" class="w-3 h-3"></i> {{ $service->toratix_cost }} TX
                            </div>
                        </div>

                        <h3 class="font-bold text-gray-900 mb-1">{{ $service->name }}</h3>
                        <p class="text-xs text-gray-500 line-clamp-2 mb-4 h-8 leading-relaxed">{{ $service->description }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 pt-4 border-t border-gray-50 mt-auto">
                        <a href="{{ route('admin.services.edit', $service->id) }}"
                            class="flex-1 py-2.5 text-center text-xs font-bold bg-gray-50 hover:bg-black hover:text-white rounded-xl transition-colors border border-gray-200 hover:border-black">
                            Edit Service
                        </a>
                        <button type="button" onclick="confirmDeleteService('{{ $service->id }}')"
                            class="p-2.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors border border-transparent hover:border-red-100">
                            <i data-feather="trash-2" class="w-4 h-4"></i>
                        </button>
                        <form id="delete-service-{{ $service->id }}"
                            action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="hidden">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full py-16 text-center border-2 border-dashed border-gray-200 rounded-3xl text-gray-400">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i data-feather="inbox" class="w-8 h-8 text-gray-300"></i>
                    </div>
                    <p class="font-medium">Belum ada service yang ditambahkan.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 fade-in">

        {{-- SECTION 2: TOKEN PRICING (INLINE EDITING) --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex flex-col h-full">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">
                        <i data-feather="database" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Token Packages</h2>
                </div>
                {{-- Add Token Button --}}
                <button onclick="toggleAddTokenRow()"
                    class="text-xs font-bold bg-black text-white px-4 py-2 rounded-xl hover:bg-gray-800 shadow-lg shadow-black/10 transition-all">
                    + Add New
                </button>
            </div>

            <div class="space-y-4 flex-1">

                {{-- ROW FORM ADD NEW --}}
                <div id="addTokenRow" class="hidden p-5 rounded-2xl border border-dashed border-gray-300 bg-gray-50/50">
                    <form action="{{ route('admin.token-prices.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-gray-400">Label</label>
                                <input type="text" name="label" placeholder="e.g. Starter"
                                    class="w-full text-xs p-3 rounded-xl border border-gray-200 focus:border-black focus:ring-0 transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-gray-400">Price (Rp)</label>
                                <input type="number" name="price_per_token" placeholder="10000"
                                    class="w-full text-xs p-3 rounded-xl border border-gray-200 focus:border-black focus:ring-0 transition-all">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-gray-400">Min Qty</label>
                                <input type="number" name="min_qty" placeholder="1"
                                    class="w-full text-xs p-3 rounded-xl border border-gray-200 focus:border-black focus:ring-0 transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-gray-400">Max Qty</label>
                                <input type="number" name="max_qty" placeholder="100"
                                    class="w-full text-xs p-3 rounded-xl border border-gray-200 focus:border-black focus:ring-0 transition-all">
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" onclick="toggleAddTokenRow()"
                                class="text-xs font-bold text-gray-500 hover:text-black py-2 px-3">Cancel</button>
                            <button type="submit"
                                class="text-xs font-bold bg-black text-white px-4 py-2 rounded-xl hover:bg-gray-800 shadow-md">Save
                                Package</button>
                        </div>
                    </form>
                </div>

                @foreach ($tokenPrices as $price)
                    <div
                        class="group relative p-4 rounded-2xl border border-gray-100 hover:border-gray-300 transition-all bg-white hover:shadow-sm">

                        {{-- DISPLAY MODE --}}
                        <div id="display-token-{{ $price->id }}" class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center shadow-sm text-xs font-bold text-gray-600 border border-gray-100 group-hover:bg-black group-hover:text-white transition-colors">
                                    {{ $price->min_qty }}TX
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-gray-900">{{ $price->label ?? 'Custom Package' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Range: <span
                                            class="font-medium text-black">{{ $price->min_qty }} -
                                            {{ $price->max_qty }}</span> Tokens</p>
                                </div>
                            </div>
                            <div class="text-right flex items-center gap-4">
                                <div>
                                    <p class="font-bold text-sm">Rp
                                        {{ number_format($price->price_per_token, 0, ',', '.') }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase font-medium">per token</p>
                                </div>

                                {{-- GEAR DROPDOWN --}}
                                <div class="relative">
                                    <button onclick="toggleDropdown('dropdown-{{ $price->id }}')"
                                        class="p-2 hover:bg-gray-100 rounded-xl text-gray-400 hover:text-black transition-colors">
                                        <i data-feather="settings" class="w-4 h-4"></i>
                                    </button>

                                    {{-- Dropdown Menu --}}
                                    <div id="dropdown-{{ $price->id }}"
                                        class="hidden absolute right-0 mt-2 w-36 bg-white border border-gray-100 rounded-2xl shadow-xl shadow-gray-200/50 z-20 overflow-hidden p-1">
                                        <button onclick="toggleEditToken('{{ $price->id }}')"
                                            class="w-full text-left px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 rounded-xl flex items-center gap-2 transition-colors">
                                            <i data-feather="edit-2" class="w-3.5 h-3.5"></i> Edit
                                        </button>
                                        <button onclick="confirmDeleteToken('{{ $price->id }}')"
                                            class="w-full text-left px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50 rounded-xl flex items-center gap-2 transition-colors">
                                            <i data-feather="trash" class="w-3.5 h-3.5"></i> Delete
                                        </button>
                                    </div>
                                    <form id="delete-token-{{ $price->id }}"
                                        action="{{ route('admin.token-prices.destroy', $price->id) }}" method="POST"
                                        class="hidden">@csrf @method('DELETE')</form>
                                </div>
                            </div>
                        </div>

                        {{-- EDIT FORM MODE --}}
                        <form id="edit-token-{{ $price->id }}"
                            action="{{ route('admin.token-prices.update', $price->id) }}" method="POST"
                            class="hidden space-y-3">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[10px] uppercase font-bold text-gray-400">Label</label>
                                    <input type="text" name="label" value="{{ $price->label }}"
                                        class="w-full text-xs p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-black font-bold transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] uppercase font-bold text-gray-400">Price / Token</label>
                                    <input type="number" name="price_per_token" value="{{ $price->price_per_token }}"
                                        class="w-full text-xs p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-black font-bold transition-all">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[10px] uppercase font-bold text-gray-400">Min Qty</label>
                                    <input type="number" name="min_qty" value="{{ $price->min_qty }}"
                                        class="w-full text-xs p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-black transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] uppercase font-bold text-gray-400">Max Qty</label>
                                    <input type="number" name="max_qty" value="{{ $price->max_qty }}"
                                        class="w-full text-xs p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-black transition-all">
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 pt-3 border-t border-dashed border-gray-200">
                                <button type="button" onclick="toggleEditToken('{{ $price->id }}')"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-500 hover:bg-gray-100">Cancel</button>
                                <button type="submit"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold bg-black text-white hover:bg-gray-800 flex items-center gap-1 shadow-md">
                                    <i data-feather="save" class="w-3 h-3"></i> Save
                                </button>
                            </div>
                        </form>

                    </div>
                @endforeach
            </div>
        </div>

        {{-- SECTION 3: MEMBERSHIP TIERS --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm h-full">
            <div class="flex items-center gap-2 mb-6">
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                    <i data-feather="award" class="w-5 h-5"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Membership Tiers</h2>
            </div>

            <div class="space-y-4">
                @foreach ($tiers as $tier)
                    <div
                        class="p-5 rounded-2xl border border-gray-200 flex items-center justify-between group hover:border-black transition-all hover:shadow-md relative overflow-hidden bg-white">
                        <div class="relative z-10 flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                                    {{ $tier->name }}
                                    @if ($loop->first)
                                        <span
                                            class="px-1.5 py-0.5 rounded-md bg-gray-100 text-[9px] text-gray-500 uppercase font-bold">Default</span>
                                    @endif
                                </h3>
                                <div class="flex gap-2">
                                    <button onclick="openTierModal({{ $tier }})"
                                        class="px-3 py-1.5 bg-gray-50 hover:bg-black hover:text-white rounded-lg text-xs font-bold text-gray-600 transition-colors flex items-center gap-1">
                                        <i data-feather="settings" class="w-3 h-3"></i> Manage
                                    </button>
                                    <form id="delete-tier-{{ $tier->id }}"
                                        action="{{ route('admin.tiers.destroy', $tier->id) }}" method="POST"
                                        class="hidden">@csrf @method('DELETE')</form>
                                </div>
                            </div>

                            <div class="text-xs text-gray-500 mb-3 font-medium">
                                Purchase Range: <span
                                    class="font-bold text-black bg-gray-100 px-2 py-0.5 rounded">{{ number_format($tier->min_toratix) }}
                                    - {{ number_format($tier->max_toratix) }} TX</span>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span
                                    class="px-2.5 py-1 bg-purple-50 text-purple-700 text-[10px] font-bold rounded-lg border border-purple-100">Max
                                    {{ $tier->max_active_tasks }} Projects</span>
                                <span
                                    class="px-2.5 py-1 bg-purple-50 text-purple-700 text-[10px] font-bold rounded-lg border border-purple-100">Max
                                    {{ $tier->max_workspaces }} Workspaces</span>
                            </div>
                        </div>

                        {{-- Decor --}}
                        <div class="absolute right-0 top-0 w-32 h-full bg-gradient-to-l from-gray-50/50 to-transparent">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MODAL EDIT TIER (BENTO STYLE) --}}
    <div id="tierModal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-900/30 backdrop-blur-sm transition-opacity" onclick="closeTierModal()"></div>

        {{-- Modal Content --}}
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-3xl p-8 shadow-2xl transform transition-all scale-100">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Edit Tier</h3>
                    <p class="text-sm text-gray-500">Sesuaikan batasan dan keuntungan membership.</p>
                </div>
                <button onclick="closeTierModal()"
                    class="p-2 bg-gray-50 hover:bg-gray-200 rounded-full text-gray-500 transition-colors">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="tierForm" method="POST" class="space-y-5">
                @csrf @method('PUT')

                {{-- Input Group: Name --}}
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Tier Name</label>
                    <input type="text" name="name" id="tierName"
                        class="w-full text-lg font-bold bg-transparent border-none p-0 focus:ring-0 placeholder-gray-300 text-gray-900"
                        placeholder="e.g. Enterprise">
                </div>

                {{-- Input Group: Token Range --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Min Tokens</label>
                        <input type="number" name="min_toratix" id="tierMin"
                            class="w-full font-bold bg-transparent border-none p-0 focus:ring-0 text-gray-900">
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Max Tokens</label>
                        <input type="number" name="max_toratix" id="tierMax"
                            class="w-full font-bold bg-transparent border-none p-0 focus:ring-0 text-gray-900">
                    </div>
                </div>

                {{-- Input Group: Limits --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Max Active Projects</label>
                        <input type="number" name="max_active_tasks" id="tierTask"
                            class="w-full font-bold bg-transparent border-none p-0 focus:ring-0 text-gray-900">
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Max Workspaces</label>
                        <input type="number" name="max_workspaces" id="tierSpace"
                            class="w-full font-bold bg-transparent border-none p-0 focus:ring-0 text-gray-900">
                    </div>
                </div>

                {{-- Input Group: Benefits --}}
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-2 block">Benefits (Comma
                        Separated)</label>
                    <textarea name="benefits" id="tierBenefits" rows="3" placeholder="e.g. Priority Support, Free Consultation"
                        class="w-full text-sm bg-white rounded-xl border-gray-200 focus:border-black focus:ring-0 transition-all"></textarea>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-4">
                    <button type="button" id="btnDeleteTier"
                        class="p-4 rounded-xl bg-red-50 text-red-600 font-bold hover:bg-red-100 transition-colors">
                        <i data-feather="trash-2" class="w-5 h-5"></i>
                    </button>
                    <button type="submit"
                        class="flex-1 py-4 rounded-xl bg-black text-white font-bold text-sm hover:bg-gray-800 shadow-xl shadow-black/20 transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS LOGIC --}}
    <script>
        // --- CUSTOM BENTO SWEETALERT MIXIN ---
        const BentoSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-3xl border border-gray-100 shadow-2xl p-0 overflow-hidden font-sans',
                title: 'text-xl font-bold text-gray-900 mt-8',
                htmlContainer: 'text-sm text-gray-500 mb-8 px-8',
                confirmButton: 'px-6 py-3 rounded-xl bg-black text-white font-bold text-sm shadow-lg shadow-black/20 hover:bg-gray-800 transition-all mx-2',
                cancelButton: 'px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-bold text-sm hover:bg-gray-200 transition-all mx-2',
                actions: 'mb-8 w-full flex justify-center gap-2'
            },
            buttonsStyling: false,
            reverseButtons: true,
            showClass: {
                popup: 'animate__animated animate__fadeInUp animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutDown animate__faster'
            }
        });

        // --- NOTIFICATIONS ---
        @if (session('success'))
            BentoSwal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if (session('error'))
            BentoSwal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
            });
        @endif

        // --- TOKEN UI LOGIC ---
        function toggleAddTokenRow() {
            const row = document.getElementById('addTokenRow');
            row.classList.toggle('hidden');
        }

        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if (el.id !== id) el.classList.add('hidden');
            });
            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.relative')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
            }
        });

        function toggleEditToken(id) {
            document.getElementById('display-token-' + id).classList.toggle('hidden');
            document.getElementById('edit-token-' + id).classList.toggle('hidden');
            document.getElementById('dropdown-' + id).classList.add('hidden');
        }

        function confirmDeleteToken(id) {
            BentoSwal.fire({
                title: 'Delete Package?',
                text: "Paket token ini akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-token-' + id).submit();
                }
            })
        }

        // --- SERVICE DELETE LOGIC ---
        function confirmDeleteService(id) {
            BentoSwal.fire({
                title: 'Delete Service?',
                text: "Layanan ini akan dihapus dari katalog.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-service-' + id).submit();
                }
            })
        }

        // --- TIER MODAL LOGIC ---
        function openTierModal(tier) {
            const modal = document.getElementById('tierModal');
            const form = document.getElementById('tierForm');

            // Set Action URL
            form.action = `/admin/tiers/${tier.id}`;

            // Set Values
            document.getElementById('tierName').value = tier.name;
            document.getElementById('tierMin').value = tier.min_toratix;
            document.getElementById('tierMax').value = tier.max_toratix;
            document.getElementById('tierTask').value = tier.max_active_tasks;
            document.getElementById('tierSpace').value = tier.max_workspaces;

            // Handle Benefits
            let benefits = '';
            try {
                const b = typeof tier.benefits === 'string' ? JSON.parse(tier.benefits) : tier.benefits;
                if (Array.isArray(b)) benefits = b.join(', ');
            } catch (e) {}
            document.getElementById('tierBenefits').value = benefits;

            // Setup Delete Button inside Modal
            const btnDelete = document.getElementById('btnDeleteTier');
            btnDelete.onclick = function() {
                closeTierModal(); // Close modal first
                confirmDeleteTier(tier.id); // Trigger Swal
            };

            modal.classList.remove('hidden');
        }

        function closeTierModal() {
            document.getElementById('tierModal').classList.add('hidden');
        }

        function confirmDeleteTier(id) {
            BentoSwal.fire({
                title: 'Delete Tier?',
                text: "Pastikan tidak ada user yang terhubung ke tier ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-tier-' + id).submit();
                }
            })
        }
    </script>
@endsection
