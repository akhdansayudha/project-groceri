@extends('admin.layouts.app')

@section('content')
    {{-- LOAD FLATPICKR --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- CUSTOM CSS --}}
    <style>
        .flatpickr-calendar {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            font-family: 'Inter', sans-serif;
            border-radius: 16px;
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange,
        .flatpickr-day.startRange.inRange,
        .flatpickr-day.endRange.inRange,
        .flatpickr-day.selected:focus,
        .flatpickr-day.startRange:focus,
        .flatpickr-day.endRange:focus,
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover {
            background: #000000 !important;
            border-color: #000000 !important;
            color: #fff;
        }

        .flatpickr-day.inRange {
            background: #f3f4f6 !important;
            border-color: #f3f4f6 !important;
            box-shadow: -5px 0 0 #f3f4f6, 5px 0 0 #f3f4f6;
            color: #000;
        }
    </style>

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 fade-in">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-1">Analytics & Reports</h1>
            <p class="text-gray-500 text-sm">Analisa performa bisnis dan pertumbuhan agency secara real-time.</p>
        </div>

        <form action="{{ route('admin.analytics') }}" method="GET" id="filterForm" class="flex flex-wrap gap-2 mt-4 md:mt-0">
            <div class="relative group">
                <input type="text" name="date_range" id="dateRangePicker" value="{{ request('date_range') }}"
                    placeholder="Select Period"
                    class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold shadow-sm focus:outline-none focus:border-black focus:ring-1 focus:ring-black w-64 transition-all cursor-pointer">
                <i data-feather="calendar"
                    class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 group-focus-within:text-black"></i>
            </div>
            <button type="submit"
                class="px-5 py-2.5 bg-gray-50 text-gray-700 border border-gray-200 rounded-xl text-xs font-bold hover:bg-gray-100 transition-colors">Apply</button>
            <button type="button" onclick="submitExport()"
                class="px-5 py-2.5 bg-black text-white rounded-xl text-xs font-bold shadow-lg shadow-black/20 flex items-center gap-2 hover:scale-105 transition-transform ml-2">
                <i data-feather="download" class="w-4 h-4"></i> Export PDF
            </button>
        </form>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in">
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-2">Total Revenue</p>
            <div class="flex justify-between items-end">
                <h3 class="text-3xl font-bold">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</h3>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg"><i data-feather="trending-up" class="w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-2">Projects Created</p>
            <div class="flex justify-between items-end">
                <h3 class="text-3xl font-bold">{{ $summary['total_projects'] }}</h3>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i data-feather="layers" class="w-5 h-5"></i></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-2">Avg. Deal Value</p>
            <div class="flex justify-between items-end">
                <h3 class="text-3xl font-bold">Rp {{ number_format($summary['avg_deal'], 0, ',', '.') }}</h3>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg"><i data-feather="pie-chart" class="w-5 h-5"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: DAILY REVENUE GROWTH (FULL WIDTH) --}}
    <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm mb-8 fade-in">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-lg">Revenue Growth</h3>
                <p class="text-xs text-gray-400">Grafik pendapatan berdasarkan rentang waktu yang dipilih</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-black rounded-full"></span>
                <span class="text-xs font-bold text-gray-500">Total Income</span>
            </div>
        </div>
        <div id="revenueChart" class="w-full h-80"></div>
    </div>

    {{-- ROW 3: PROJECT STATUS & MOST POPULAR SERVICES --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 fade-in">

        {{-- LEFT: PROJECT STATUS --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex flex-col justify-center">
            <h3 class="font-bold text-lg mb-2 text-center">Project Status</h3>
            <p class="text-xs text-gray-400 text-center mb-6">Distribusi status project saat ini</p>
            <div id="statusChart" class="flex justify-center"></div>
        </div>

        {{-- RIGHT: MOST POPULAR SERVICES --}}
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-lg">Most Popular Services</h3>
                <p class="text-xs text-gray-400">Layanan dengan peminat tertinggi</p>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-[10px] uppercase text-gray-500 font-bold">
                        <tr>
                            <th class="px-6 py-3 w-10">#</th>
                            <th class="px-6 py-3">Service Name</th>
                            <th class="px-6 py-3 text-center">Orders</th>
                            <th class="px-6 py-3 text-right">Contribution</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($topServices as $service)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-gray-400 text-xs">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 font-bold text-gray-900 flex items-center gap-3">
                                    {{-- FIX: Tampilkan Ikon dari Supabase --}}
                                    @if ($service->icon_url)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('supabase')->url($service->icon_url) }}"
                                            class="w-8 h-8 rounded-lg object-cover border border-gray-100 p-0.5">
                                    @else
                                        <div
                                            class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                            <i data-feather="image" class="w-4 h-4"></i>
                                        </div>
                                    @endif
                                    {{ $service->name }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold">{{ $service->tasks_count }}</td>
                                <td class="px-6 py-4 text-right">
                                    @php
                                        $percentage =
                                            $summary['total_projects'] > 0
                                                ? ($service->tasks_count / $summary['total_projects']) * 100
                                                : 0;
                                    @endphp
                                    <span class="text-xs font-bold">{{ round($percentage) }}%</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-xs">No services ordered.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ROW 4: TOP STAFF & TOP CLIENTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 fade-in mb-10">

        {{-- TOP STAFF --}}
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg">Top Staff Performance</h3>
                <span class="bg-black text-white px-2 py-1 rounded text-[10px] font-bold">BY TOKEN EARNED</span>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-[10px] uppercase text-gray-500 font-bold">
                        <tr>
                            <th class="px-6 py-3">Rank</th>
                            <th class="px-6 py-3">Staff</th>
                            <th class="px-6 py-3 text-center">Projects</th>
                            <th class="px-6 py-3 text-right">Tokens</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($topStaff as $index => $staff)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    @if ($index == 0)
                                        🥇
                                    @elseif($index == 1)
                                        🥈
                                    @elseif($index == 2)
                                        🥉
                                    @else
                                        <span class="text-gray-400">#{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <img src="{{ $staff->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($staff->full_name) }}"
                                        class="w-8 h-8 rounded-full bg-gray-100 object-cover">
                                    <span
                                        class="font-bold text-gray-900 text-xs">{{ Str::limit($staff->full_name, 15) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center font-bold">{{ $staff->completed_count }}</td>
                                <td class="px-6 py-4 text-right font-bold text-green-600">
                                    {{ number_format($staff->earned_tokens) }} TX</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-xs">No data available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TOP CLIENTS --}}
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-lg">Top Clients</h3>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-[10px] uppercase text-gray-500 font-bold">
                        <tr>
                            <th class="px-6 py-3">Client</th>
                            <th class="px-6 py-3">Tier</th>
                            <th class="px-6 py-3 text-right">Total Spent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($topClients as $client)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <img src="{{ $client->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($client->full_name) }}"
                                        class="w-8 h-8 rounded-full bg-gray-100 object-cover">
                                    <span
                                        class="font-bold text-gray-900 text-xs">{{ Str::limit($client->full_name, 15) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-gray-100 text-gray-600">{{ $client->wallet->tier->name ?? 'Starter' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900">Rp
                                    {{ number_format($client->total_spent, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400 text-xs">No transactions.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        flatpickr("#dateRangePicker", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: "{{ $selectedRange }}".split(' to '),
            maxDate: "today",
            theme: "dark"
        });

        function submitExport() {
            const form = document.getElementById('filterForm');
            const originalAction = form.action;
            form.action = "{{ route('admin.analytics.export') }}";
            form.submit();
            setTimeout(() => {
                form.action = originalAction;
            }, 100);
        }

        // REVENUE CHART CONFIG
        new ApexCharts(document.querySelector("#revenueChart"), {
            series: [{
                name: 'Revenue',
                data: @json($chartRevenue['data'])
            }],
            chart: {
                type: 'area',
                height: 320,
                toolbar: {
                    show: false
                },
                fontFamily: 'Inter, sans-serif',
                zoom: {
                    enabled: false
                }
            },
            colors: ['#000000'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.1,
                    opacityTo: 0.0,
                    stops: [0, 90, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: @json($chartRevenue['labels']),
                tooltip: {
                    enabled: false
                },
                labels: {
                    style: {
                        fontSize: '10px',
                        colors: '#9ca3af'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    formatter: (val) => val >= 1000000 ? (val / 1000000).toFixed(1) + 'M' : (val >= 1000 ? (val /
                        1000).toFixed(0) + 'k' : val),
                    style: {
                        fontSize: '10px',
                        colors: '#9ca3af'
                    }
                }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 10
                }
            },
            tooltip: {
                theme: 'dark'
            }
        }).render();

        // STATUS CHART CONFIG
        new ApexCharts(document.querySelector("#statusChart"), {
            series: @json($chartStatus),
            labels: ['Active', 'Queue', 'Completed', 'Revision'],
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#2563eb', '#eab308', '#16a34a', '#dc2626'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '14px',
                                fontWeight: 'bold'
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'bottom',
                fontSize: '11px',
                markers: {
                    radius: 12
                }
            },
            tooltip: {
                theme: 'dark'
            },
            stroke: {
                show: false
            }
        }).render();
    </script>
@endsection
