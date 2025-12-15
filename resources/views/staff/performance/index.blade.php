@extends('staff.layouts.app')

@section('content')
    <div class="fade-in pb-10">

        {{-- HEADER & FILTER --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">My Performance</h1>
                <p class="text-gray-500 mt-1">Analytics overview of your productivity and earnings.</p>
            </div>

            {{-- Filter Dropdown --}}
            <form action="{{ route('staff.performance.index') }}" method="GET" class="min-w-[200px]">
                <div class="relative">
                    <select name="range" onchange="this.form.submit()"
                        class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-black focus:border-transparent transition-all appearance-none cursor-pointer shadow-sm">
                        <option value="all_time" {{ $range == 'all_time' ? 'selected' : '' }}>All Time</option>
                        <option value="last_month" {{ $range == 'last_month' ? 'selected' : '' }}>Last 30 Days</option>
                    </select>
                    <i data-feather="calendar"
                        class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </form>
        </div>

        {{-- HERO METRICS GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            {{-- 1. TOTAL EARNINGS --}}
            <div class="bg-black text-white p-6 rounded-3xl shadow-lg relative overflow-hidden group">
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-white/20 rounded-xl">
                            <i data-feather="zap" class="w-6 h-6 text-yellow-400 fill-yellow-400"></i>
                        </div>
                        @if ($range == 'last_month')
                            <span
                                class="bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded-lg text-[10px] font-bold uppercase">30
                                Days</span>
                        @else
                            <span
                                class="bg-white/20 text-gray-300 px-2 py-1 rounded-lg text-[10px] font-bold uppercase">Lifetime</span>
                        @endif
                    </div>
                    <p class="text-xs uppercase font-bold text-gray-400 tracking-wider mb-1">Total Earnings</p>
                    <h3 class="text-3xl font-bold">{{ number_format($totalEarned) }} <span
                            class="text-sm font-normal text-gray-400">Tokens</span></h3>
                </div>
                {{-- Decorative Glow --}}
                <div
                    class="absolute -right-10 -bottom-10 w-40 h-40 bg-gray-800 rounded-full opacity-50 blur-3xl group-hover:bg-gray-700 transition-colors duration-500">
                </div>
            </div>

            {{-- 2. PROJECTS COMPLETED --}}
            <div class="bg-white border border-gray-200 p-6 rounded-3xl shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl">
                        <i data-feather="check-square" class="w-6 h-6 text-blue-600"></i>
                    </div>
                </div>
                <p class="text-xs uppercase font-bold text-gray-400 tracking-wider mb-1">Projects Delivered</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ $totalCompleted }} <span
                        class="text-sm font-normal text-gray-400">Projects</span></h3>
            </div>

            {{-- 3. ON-TIME DELIVERY RATE --}}
            <div class="bg-white border border-gray-200 p-6 rounded-3xl shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-green-50 border border-green-100 rounded-xl">
                        <i data-feather="clock" class="w-6 h-6 text-green-600"></i>
                    </div>
                    @if ($onTimeRate >= 90)
                        <span
                            class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-[10px] font-bold uppercase">Excellent</span>
                    @elseif($onTimeRate >= 75)
                        <span
                            class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-lg text-[10px] font-bold uppercase">Good</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-lg text-[10px] font-bold uppercase">Needs
                            Impr.</span>
                    @endif
                </div>
                <p class="text-xs uppercase font-bold text-gray-400 tracking-wider mb-1">On-Time Delivery</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ $onTimeRate }}%</h3>
            </div>
        </div>

        {{-- DETAILED BREAKDOWN --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM KIRI: SERVICE PERFORMANCE --}}
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <i data-feather="layers" class="w-4 h-4 text-gray-400"></i> Service Breakdown
                    </h3>
                </div>

                <div class="p-0">
                    @if ($serviceStats->count() > 0)
                        <table class="w-full text-left">
                            <thead class="bg-white text-[10px] uppercase text-gray-400 font-bold tracking-wider">
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-100">Service Category</th>
                                    <th class="px-6 py-3 border-b border-gray-100 text-center">Projects</th>
                                    <th class="px-6 py-3 border-b border-gray-100 text-right">Earnings</th>
                                    <th class="px-6 py-3 border-b border-gray-100 text-right">Contribution</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($serviceStats as $stat)
                                    @php
                                        $percentage = $totalEarned > 0 ? ($stat['earnings'] / $totalEarned) * 100 : 0;
                                    @endphp
                                    <tr class="group hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span
                                                class="font-bold text-gray-800 text-sm group-hover:text-black">{{ $stat['service_name'] }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="bg-gray-100 text-gray-600 px-2 py-1 rounded-md text-xs font-bold">{{ $stat['count'] }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-1 font-bold text-gray-900">
                                                <i data-feather="zap" class="w-3 h-3 text-yellow-500 fill-yellow-500"></i>
                                                {{ number_format($stat['earnings']) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right w-32">
                                            <div class="flex items-center gap-2 justify-end">
                                                <span
                                                    class="text-xs font-bold text-gray-500">{{ round($percentage) }}%</span>
                                                <div class="w-12 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                    <div class="h-full bg-black rounded-full"
                                                        style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-12 text-center">
                            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-feather="bar-chart-2" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm">No performance data available for this period.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- KOLOM KANAN: TIPS / SUMMARY --}}
            <div
                class="bg-gradient-to-br from-gray-900 to-black rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <div
                        class="w-12 h-12 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center mb-6 border border-white/10">
                        <i data-feather="trending-up" class="w-6 h-6 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Pro Tip</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        Maintaining a high <strong class="text-white">On-Time Delivery Rate</strong> increases your chances
                        of getting assigned to higher-value projects (Priority Queue).
                    </p>

                    <div class="pt-6 border-t border-white/10">
                        <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-2">Performance Grade</p>
                        @if ($totalCompleted == 0)
                            <div class="text-2xl font-bold text-gray-400">No Data</div>
                        @elseif($onTimeRate >= 90)
                            <div class="text-2xl font-bold text-green-400">Outstanding A+</div>
                        @elseif($onTimeRate >= 80)
                            <div class="text-2xl font-bold text-blue-400">Solid B</div>
                        @else
                            <div class="text-2xl font-bold text-yellow-400">Developing C</div>
                        @endif
                    </div>
                </div>

                {{-- Decoration --}}
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-purple-900 rounded-full opacity-20 blur-[80px] -mr-16 -mt-16">
                </div>
            </div>

        </div>

    </div>
@endsection
