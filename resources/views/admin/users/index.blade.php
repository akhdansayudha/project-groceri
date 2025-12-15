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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
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

    {{-- CLIENT TABLE --}}
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden fade-in">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Client Profile</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Membership Tier</th>
                        <th class="px-6 py-4">Token Balance</th>
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
                                    <div class="relative">
                                        <img src="{{ $client->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $client->full_name }}"
                                            class="w-10 h-10 rounded-full bg-gray-200 object-cover">
                                        {{-- Indikator Online di Avatar (Opsional) --}}
                                        @if (in_array($client->id, $onlineUserIds))
                                            <div
                                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full">
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $client->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $client->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4">
                                @if (in_array($client->id, $onlineUserIds))
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-600 border border-green-100 uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Online
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200 uppercase">
                                        Offline
                                    </span>
                                @endif
                            </td>

                            {{-- Tier --}}
                            <td class="px-6 py-4">
                                @php
                                    $tierName = $client->wallet->tier->name ?? 'Starter';
                                    $tierColor = match ($tierName) {
                                        'Professional', 'Ultimate' => 'bg-black text-white',
                                        'Basic' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $tierColor }}">
                                    {{ $tierName }}
                                </span>
                            </td>

                            {{-- Balance --}}
                            <td class="px-6 py-4 font-bold text-gray-900">
                                {{ number_format($client->wallet->balance ?? 0) }} TX
                            </td>

                            {{-- Total Spent (Total Purchased) --}}
                            <td class="px-6 py-4 text-gray-500">
                                {{ number_format($client->wallet->total_purchased ?? 0) }} TX
                            </td>

                            {{-- Joined Date --}}
                            <td class="px-6 py-4 text-gray-400 text-xs">
                                {{ $client->created_at->format('d M Y') }}
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.users.show', $client->id) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 hover:bg-black hover:text-white hover:border-black transition-all">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <p>Tidak ada data client ditemukan.</p>
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
@endsection
