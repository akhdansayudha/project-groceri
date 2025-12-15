@extends('admin.layouts.app')

@section('content')
    <div class="mb-6 flex items-center gap-4 fade-in">
        <a href="{{ route('admin.notifications.index') }}"
            class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
            <i data-feather="arrow-left" class="w-5 h-5 text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Broadcast Details</h1>
            <p class="text-sm text-gray-500">Dikirim pada
                {{ \Carbon\Carbon::parse($batch->created_at)->format('d F Y, H:i') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">

        {{-- Kiri: Detail Pesan & Stats --}}
        <div class="space-y-6">
            {{-- Card Message --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase border bg-gray-50 border-gray-200">
                        Type: {{ ucfirst($batch->type) }}
                    </span>
                    <span class="text-xs text-gray-400">Target:
                        {{ ucfirst(str_replace('_', ' ', $batch->target_audience)) }}</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $batch->title }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $batch->message }}</p>
            </div>

            {{-- Card Stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-black text-white p-5 rounded-2xl relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-gray-400 uppercase">Total Sent</p>
                        <p class="text-3xl font-bold mt-1">{{ $batch->total_sent }}</p>
                    </div>
                    <i data-feather="send" class="absolute right-3 bottom-3 w-12 h-12 text-gray-800 opacity-50"></i>
                </div>
                <div class="bg-white border border-gray-200 p-5 rounded-2xl relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-gray-400 uppercase">Read Rate</p>
                        <div class="flex items-end gap-2 mt-1">
                            <p class="text-3xl font-bold text-green-600">{{ $batch->total_read }}</p>
                            <span class="text-xs text-gray-500 mb-1">Users</span>
                        </div>
                        @php
                            $percentage =
                                $batch->total_sent > 0 ? round(($batch->total_read / $batch->total_sent) * 100) : 0;
                        @endphp
                        <div class="w-full bg-gray-100 h-1.5 rounded-full mt-2">
                            <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kanan: Tabel Penerima --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden flex flex-col h-full">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900">Recipient List</h3>
                    <span class="text-xs text-gray-500">Showing {{ $recipients->count() }} of
                        {{ $batch->total_sent }}</span>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left">
                        <thead class="bg-white text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">User Profile</th>
                                <th class="px-6 py-4">Email / Role</th>
                                <th class="px-6 py-4 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($recipients as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden border border-gray-200">
                                                <img src="{{ $item->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($item->user->full_name) }}"
                                                    class="w-full h-full object-cover">
                                            </div>
                                            <span
                                                class="font-bold text-gray-900 text-xs">{{ $item->user->full_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex flex-col">
                                            <span class="text-xs text-gray-600">{{ $item->user->email }}</span>
                                            <span
                                                class="text-[10px] text-gray-400 uppercase">{{ $item->user->role }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        @if ($item->is_read)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-green-50 text-green-700 border border-green-100 text-[10px] font-bold uppercase">
                                                <i data-feather="check-circle" class="w-3 h-3"></i> Read
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gray-100 text-gray-500 border border-gray-200 text-[10px] font-bold uppercase">
                                                <i data-feather="clock" class="w-3 h-3"></i> Sent
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-400 text-xs">No data
                                        available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $recipients->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
