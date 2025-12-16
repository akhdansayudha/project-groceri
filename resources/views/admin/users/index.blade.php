@extends('admin.layouts.app')

@section('content')
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Client Database</h1>
            <p class="text-gray-500 text-sm">Kelola data pelanggan, tier membership, dan saldo token.</p>
        </div>

        {{-- SEARCH BAR --}}
        <form action="{{ route('admin.users.index') }}" method="GET" class="w-full md:w-auto">
            <div class="relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client..."
                    class="w-full md:w-80 bg-white border border-gray-200 rounded-xl px-4 py-2.5 pl-10 text-sm focus:outline-none focus:border-black transition-all shadow-sm">
                <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>

                @if (request('search'))
                    <a href="{{ route('admin.users.index') }}"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black">
                        <i data-feather="x" class="w-3 h-3"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in">
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-black text-white rounded-2xl flex items-center justify-center">
                <i data-feather="users" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Clients</p>
                <h3 class="text-2xl font-bold">{{ $totalClients }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center">
                <i data-feather="wifi" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Online Now</p>
                <h3 class="text-2xl font-bold text-green-600">{{ $totalOnline }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center">
                <i data-feather="database" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Token Circulating</p>
                <h3 class="text-2xl font-bold">{{ number_format($totalTokenCirculating) }} <span
                        class="text-sm font-normal text-gray-400">TX</span></h3>
            </div>
        </div>
    </div>

    {{-- ALERT MESSAGES --}}
    @if (session('success'))
        <div class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 flex items-center gap-2">
            <i data-feather="check-circle" class="w-4 h-4"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200 flex items-center gap-2">
            <i data-feather="alert-circle" class="w-4 h-4"></i> {{ session('error') }}
        </div>
    @endif

    {{-- CLIENT TABLE --}}
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden fade-in">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        {{-- Lebar kolom profile dikurangi --}}
                        <th class="px-6 py-4 w-[280px]">Client Profile</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tier</th>
                        <th class="px-6 py-4">Balance</th>
                        <th class="px-6 py-4">Total Spent</th>
                        <th class="px-6 py-4">Joined At</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50 transition-colors group">

                            {{-- Profile --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0">
                                        @php
                                            // Default ke UI Avatars
                                            $avatar =
                                                'https://ui-avatars.com/api/?name=' .
                                                urlencode($client->full_name) .
                                                '&background=random&color=fff';

                                            // Cek jika avatar_url ada isinya
                                            if (!empty($client->avatar_url)) {
                                                if (Str::startsWith($client->avatar_url, 'http')) {
                                                    // Jika sudah https (misal dari Google), pakai langsung
                                                    $avatar = $client->avatar_url;
                                                } else {
                                                    // Jika path file (hasil upload), generate URL Supabase
                                                    $avatar = \Illuminate\Support\Facades\Storage::disk(
                                                        'supabase',
                                                    )->url($client->avatar_url);
                                                }
                                            }
                                        @endphp

                                        <img src="{{ $avatar }}"
                                            class="w-10 h-10 rounded-full bg-gray-200 object-cover border border-gray-100 shadow-sm"
                                            alt="{{ $client->full_name }}">

                                        {{-- Indikator Online (Opsional) --}}
                                        @if (in_array($client->id, $onlineUserIds))
                                            <div
                                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 truncate max-w-[180px]">{{ $client->full_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate max-w-[180px]">{{ $client->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4">
                                @if (in_array($client->id, $onlineUserIds))
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200 uppercase tracking-wide">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Online
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200 uppercase tracking-wide">
                                        Offline
                                    </span>
                                @endif
                            </td>

                            {{-- Tier --}}
                            <td class="px-6 py-4">
                                @php
                                    $tierName = $client->wallet->tier->name ?? 'Starter';
                                    $tierColor = match ($tierName) {
                                        'Ultimate' => 'bg-black text-white border-black',
                                        'Professional' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'Basic' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                                    };
                                @endphp
                                <span
                                    class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase border {{ $tierColor }}">
                                    {{ $tierName }}
                                </span>
                            </td>

                            {{-- Balance --}}
                            <td class="px-6 py-4 font-bold text-gray-900">
                                {{ number_format($client->wallet->balance ?? 0) }} TX
                            </td>

                            {{-- Total Spent --}}
                            <td class="px-6 py-4 text-gray-500">
                                {{ number_format($client->wallet->total_purchased ?? 0) }} TX
                            </td>

                            {{-- Joined Date (Detail) --}}
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                <div class="font-medium text-gray-900">{{ $client->created_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-gray-400">{{ $client->created_at->format('H:i') }} WIB</div>
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Detail --}}
                                    <a href="{{ route('admin.users.show', $client->id) }}"
                                        class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-black transition-colors"
                                        title="View Detail">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </a>

                                    {{-- Edit (Open Modal) --}}
                                    <button
                                        onclick="openEditModal('{{ $client->id }}', '{{ $client->full_name }}', '{{ $client->email }}')"
                                        class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition-colors"
                                        title="Edit Client">
                                        <i data-feather="edit-2" class="w-4 h-4"></i>
                                    </button>

                                    {{-- Delete (SweetAlert) --}}
                                    <button
                                        onclick="confirmDeleteClient('{{ $client->id }}', '{{ $client->full_name }}', '{{ $client->email }}', '{{ $avatar }}')"
                                        class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-red-500 hover:bg-red-50 hover:border-red-200 transition-colors"
                                        title="Delete Client">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>

                                    {{-- Hidden Delete Form --}}
                                    <form id="delete-form-{{ $client->id }}"
                                        action="{{ route('admin.users.destroy', $client->id) }}" method="POST"
                                        class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="p-3 bg-gray-50 rounded-full border border-gray-100">
                                        <i data-feather="user-x" class="w-6 h-6 text-gray-300"></i>
                                    </div>
                                    <p class="text-sm">Tidak ada data client ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $clients->links() }}
        </div>
    </div>

    {{-- MODAL EDIT CLIENT --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeEditModal()"></div>

        {{-- Modal Content --}}
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-3xl p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Edit Client Profile</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-black">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="editForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    {{-- Avatar --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Profile Picture</label>
                        <input type="file" name="avatar"
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-black file:text-white hover:file:bg-gray-800 border border-gray-200 rounded-xl cursor-pointer">
                        <p class="text-[10px] text-gray-400 mt-1">Leave empty to keep current avatar.</p>
                    </div>

                    {{-- Full Name --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Full Name</label>
                        <input type="text" name="full_name" id="edit_name" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black transition-all">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email Address</label>
                        <input type="email" name="email" id="edit_email" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black transition-all">
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">New Password <span
                                class="text-gray-300 font-normal normal-case">(Optional)</span></label>
                        <input type="password" name="new_password" placeholder="Enter new password..."
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-black transition-all">
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="closeEditModal()"
                        class="flex-1 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-colors shadow-lg shadow-black/20">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    {{-- Pastikan SweetAlert sudah diload di layout app --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // --- EDIT MODAL LOGIC ---
        function openEditModal(id, name, email) {
            // Set form action URL dinamis
            const form = document.getElementById('editForm');
            form.action = `/admin/users/${id}`; // Sesuaikan dengan route update

            // Isi value input
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;

            // Tampilkan Modal
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // --- DELETE SWEETALERT LOGIC ---
        function confirmDeleteClient(id, name, email, avatarUrl) {
            Swal.fire({
                title: 'Delete Client?',
                html: `
                <div class="flex flex-col items-center mt-3">
                    <img src="${avatarUrl}" class="w-16 h-16 rounded-full border-2 border-gray-100 object-cover mb-3">
                    <h4 class="font-bold text-gray-900 text-lg">${name}</h4>
                    <p class="text-sm text-gray-500 mb-4">${email}</p>
                    
                    <div class="bg-red-50 border border-red-100 rounded-xl p-3 w-full text-left flex items-start gap-3">
                        <div class="text-red-500 mt-0.5"><i data-feather="alert-triangle" style="width: 16px; height: 16px;"></i></div>
                        <p class="text-xs text-red-600 leading-relaxed">
                            <strong>Warning:</strong> This action cannot be undone. All data associated with this client (Projects, Wallet, History) might be affected.
                        </p>
                    </div>
                </div>
            `,
                icon: null, // Kita pakai custom HTML di atas
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete Client',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-3xl shadow-2xl border border-gray-100 font-sans p-0 overflow-hidden',
                    htmlContainer: 'px-8 pb-6',
                    title: 'pt-8 px-8 text-xl font-bold text-gray-900',
                    confirmButton: 'w-full sm:w-auto bg-red-600 text-white rounded-xl font-bold text-sm px-6 py-3.5 hover:bg-red-700 transition-all shadow-lg shadow-red-500/30',
                    cancelButton: 'w-full sm:w-auto bg-white text-gray-700 border border-gray-200 rounded-xl font-bold text-sm px-6 py-3.5 hover:bg-gray-50 transition-all',
                    actions: 'gap-3 px-8 pb-8 w-full flex-col-reverse sm:flex-row',
                },
                width: '420px'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    document.getElementById('delete-form-' + id).submit();
                }
            });
            // Re-init feather icons inside Swal if needed, though simple <i> tags usually work
        }
    </script>
@endsection
