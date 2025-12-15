@extends('admin.layouts.app')

@section('content')
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Staff & Roles</h1>
            <p class="text-gray-500 text-sm">Kelola akses tim, admin, dan pantau beban kerja.</p>
        </div>

        <div class="flex gap-3 w-full md:w-auto">
            {{-- SEARCH BAR --}}
            <form action="{{ route('admin.staff.index') }}" method="GET" class="flex-1 md:w-auto">
                <div class="relative group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Find team member..."
                        class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 pl-10 text-sm focus:outline-none focus:border-black transition-all shadow-sm">
                    <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                </div>
            </form>

            {{-- ADD BUTTON --}}
            <a href="{{ route('admin.staff.create') }}"
                class="px-5 py-2.5 bg-black text-white rounded-xl text-sm font-bold hover:bg-gray-800 transition-all flex items-center gap-2 shadow-lg shadow-black/20">
                <i data-feather="plus" class="w-4 h-4"></i> Add Member
            </a>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in">
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-gray-100 text-gray-600 rounded-2xl flex items-center justify-center">
                <i data-feather="users" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Staff</p>
                <h3 class="text-2xl font-bold">{{ $totalStaff }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-black text-white rounded-2xl flex items-center justify-center">
                <i data-feather="shield" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Administrators</p>
                <h3 class="text-2xl font-bold">{{ $totalAdmins }}</h3>
            </div>
        </div>

        {{-- Total Online (Gabungan Staff + Admin) --}}
        @php
            $onlineStaffCount = 0;
            foreach ($staffMembers as $staff) {
                if (in_array($staff->id, $onlineUserIds)) {
                    $onlineStaffCount++;
                }
            }
        @endphp
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center">
                <i data-feather="wifi" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Team Online</p>
                <h3 class="text-2xl font-bold text-green-600">{{ $onlineStaffCount }}</h3>
            </div>
        </div>
    </div>

    {{-- STAFF TABLE --}}
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden fade-in">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Profile</th>
                        <th class="px-6 py-4">Role / Access</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Workload (Active)</th>
                        <th class="px-6 py-4">Performance (Completed)</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($staffMembers as $staff)
                        <tr class="hover:bg-gray-50 transition-colors group">

                            {{-- Profile --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <img src="{{ $staff->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $staff->full_name }}"
                                            class="w-10 h-10 rounded-full bg-gray-200 object-cover border border-gray-100">
                                        {{-- Online Indicator --}}
                                        @if (in_array($staff->id, $onlineUserIds))
                                            <div
                                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full">
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $staff->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $staff->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Role --}}
                            <td class="px-6 py-4">
                                @if ($staff->role == 'admin')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold bg-black text-white uppercase tracking-wide">
                                        <i data-feather="shield" class="w-3 h-3"></i> Admin
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold bg-gray-100 text-gray-600 uppercase tracking-wide border border-gray-200">
                                        Staff
                                    </span>
                                @endif
                            </td>

                            {{-- Status Online --}}
                            <td class="px-6 py-4">
                                @if (in_array($staff->id, $onlineUserIds))
                                    <span class="text-green-600 text-xs font-bold">Online Now</span>
                                @else
                                    <span class="text-gray-400 text-xs">Offline</span>
                                @endif
                            </td>

                            {{-- Workload (Active Tasks) --}}
                            <td class="px-6 py-4">
                                @if ($staff->active_tasks_count > 0)
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full"
                                                style="width: {{ min($staff->active_tasks_count * 20, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-blue-600">{{ $staff->active_tasks_count }}
                                            Tasks</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Available</span>
                                @endif
                            </td>

                            {{-- Performance (Completed) --}}
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-gray-900">{{ $staff->completed_tasks_count }}</span>
                                <span class="text-[10px] text-gray-400">projects done</span>
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.staff.edit', $staff->id) }}"
                                        class="p-2 text-gray-400 hover:text-black hover:bg-gray-100 rounded-lg transition-colors">
                                        <i data-feather="edit-2" class="w-4 h-4"></i>
                                    </a>

                                    @if ($staff->id !== auth()->id())
                                        <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus staff ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                <i data-feather="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                Tidak ada data staff ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $staffMembers->links() }}
        </div>
    </div>
@endsection
