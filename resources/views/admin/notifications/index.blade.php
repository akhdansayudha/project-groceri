@extends('admin.layouts.app')

@section('content')
    {{-- SWEETALERT CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Broadcast Center</h1>
            <p class="text-gray-500 text-sm">Kelola pengiriman notifikasi masal ke pengguna.</p>
        </div>
        <a href="{{ route('admin.notifications.create') }}"
            class="px-5 py-2.5 bg-black text-white rounded-xl text-sm font-bold hover:bg-gray-800 transition-all flex items-center gap-2 shadow-lg shadow-black/20">
            <i data-feather="send" class="w-4 h-4"></i> New Broadcast
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden fade-in">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Date Sent</th>
                        <th class="px-6 py-4">Type</th> {{-- Kolom Terpisah --}}
                        <th class="px-6 py-4">Target</th> {{-- Kolom Terpisah --}}
                        <th class="px-6 py-4">Message Preview</th>
                        <th class="px-6 py-4 text-center">Recipients</th> {{-- Kolom Terpisah --}}
                        <th class="px-6 py-4 text-center">Read</th> {{-- Kolom Terpisah --}}
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            {{-- DATE --}}
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($batch->created_at)->format('d M Y') }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($batch->created_at)->format('H:i') }} WIB
                                </p>
                            </td>

                            {{-- TYPE --}}
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase border 
                                    {{ $batch->type == 'info' ? 'bg-blue-50 text-blue-600 border-blue-100' : '' }}
                                    {{ $batch->type == 'warning' ? 'bg-yellow-50 text-yellow-600 border-yellow-100' : '' }}
                                    {{ $batch->type == 'promo' ? 'bg-purple-50 text-purple-600 border-purple-100' : '' }}
                                    {{ $batch->type == 'error' ? 'bg-red-50 text-red-600 border-red-100' : '' }}
                                    {{ $batch->type == 'success' ? 'bg-green-50 text-green-600 border-green-100' : '' }}">
                                    {{ ucfirst($batch->type) }}
                                </span>
                            </td>

                            {{-- TARGET --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="p-1.5 bg-gray-100 rounded-md text-gray-500">
                                        <i data-feather="users" class="w-3 h-3"></i>
                                    </div>
                                    <span class="text-xs font-bold text-gray-700">
                                        {{ str_replace('_', ' ', ucfirst($batch->target_audience)) }}
                                    </span>
                                </div>
                            </td>

                            {{-- MESSAGE --}}
                            <td class="px-6 py-4 max-w-xs">
                                <p class="font-bold text-gray-900 text-xs mb-0.5 truncate">{{ $batch->title }}</p>
                                <p class="text-gray-500 text-xs truncate">{{ $batch->message }}</p>
                            </td>

                            {{-- RECIPIENTS --}}
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-bold border border-gray-200">
                                    {{ $batch->total_sent }}
                                </span>
                            </td>

                            {{-- READ STATUS --}}
                            <td class="px-6 py-4 text-center">
                                @if ($batch->total_read > 0)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-green-50 text-green-700 text-xs font-bold border border-green-100">
                                        <i data-feather="check-circle" class="w-3 h-3"></i> {{ $batch->total_read }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">-</span>
                                @endif
                            </td>

                            {{-- ACTION --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- View Button --}}
                                    <a href="{{ route('admin.notifications.show', $batch->id) }}"
                                        class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-black hover:text-white hover:border-black transition-all shadow-sm"
                                        title="View Details">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </a>

                                    {{-- Delete Button (Trigger SweetAlert) --}}
                                    <button type="button" onclick="confirmDeleteBatch('{{ $batch->id }}')"
                                        class="p-2 bg-white border border-gray-200 text-red-500 rounded-xl hover:bg-red-50 hover:border-red-200 transition-all shadow-sm"
                                        title="Delete Batch">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>

                                    {{-- Hidden Delete Form --}}
                                    <form id="delete-batch-{{ $batch->id }}"
                                        action="{{ route('admin.notifications.destroy', $batch->id) }}" method="POST"
                                        class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="p-4 bg-gray-50 rounded-full border border-gray-100">
                                        <i data-feather="inbox" class="w-8 h-8 text-gray-300"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">No broadcasts found</p>
                                        <p class="text-xs text-gray-500">Belum ada riwayat pengiriman notifikasi.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($batches->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

    {{-- SCRIPT: CUSTOM BENTO SWEETALERT --}}
    <script>
        // Mixin untuk Style Konsisten
        const BentoSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-3xl border border-gray-100 shadow-2xl p-0 overflow-hidden font-sans',
                title: 'text-xl font-bold text-gray-900 mt-8 mb-2',
                htmlContainer: 'text-sm text-gray-500 mb-8 px-8',
                confirmButton: 'px-6 py-3 rounded-xl bg-black text-white font-bold text-sm shadow-lg shadow-black/20 hover:bg-gray-800 transition-all mx-2',
                cancelButton: 'px-6 py-3 rounded-xl bg-gray-50 text-gray-700 font-bold text-sm hover:bg-gray-100 border border-gray-200 transition-all mx-2',
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

        // Function Trigger
        function confirmDeleteBatch(id) {
            BentoSwal.fire({
                title: 'Delete Broadcast?',
                text: "Batch notifikasi ini dan seluruh riwayat penerimanya akan dihapus permanen.",
                icon: 'warning', // Ikon bawaan SweetAlert (bisa diganti custom HTML jika mau)
                iconColor: '#fee2e2', // Merah muda pudar (Tailwind red-100)
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-batch-' + id).submit();
                }
            })
        }

        // Notifikasi Sukses (Optional, jika ada flash message)
        @if (session('success'))
            BentoSwal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>
@endsection
