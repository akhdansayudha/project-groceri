@extends('admin.layouts.app')

@section('content')
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 fade-in">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-1">Analytics & Reports</h1>
            <p class="text-gray-500 text-sm">Analisa performa bisnis dan pertumbuhan agency secara real-time.</p>
        </div>
        <div class="flex gap-2 mt-4 md:mt-0">
            <button
                class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold shadow-sm hover:bg-gray-50 flex items-center gap-2">
                <i data-feather="calendar" class="w-4 h-4"></i> Last 6 Months
            </button>
            <button
                class="px-4 py-2 bg-black text-white rounded-xl text-xs font-bold shadow-lg shadow-black/20 flex items-center gap-2">
                <i data-feather="download" class="w-4 h-4"></i> Export PDF
            </button>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in">
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Total Revenue</p>
                    <h3 class="text-3xl font-bold mt-1">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                    <i data-feather="trending-up" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span
                    class="text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded-full">+{{ $summary['growth'] }}%</span>
                <span class="text-gray-400">vs bulan lalu</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Total Projects</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $summary['total_projects'] }}</h3>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <i data-feather="layers" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400">Lifetime project completed</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Avg. Deal Value</p>
                    <h3 class="text-3xl font-bold mt-1">Rp {{ number_format($summary['avg_deal'], 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                    <i data-feather="pie-chart" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400">Rata-rata nilai per transaksi</p>
        </div>
    </div>

    {{-- CHARTS ROW 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8 fade-in">

        {{-- MAIN CHART: REVENUE --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="mb-6">
                <h3 class="font-bold text-lg">Revenue Growth</h3>
                <p class="text-xs text-gray-400">Pendapatan kotor per bulan</p>
            </div>
            <div id="revenueChart" class="w-full h-80"></div>
        </div>

        {{-- DONUT CHART: STATUS --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="mb-6">
                <h3 class="font-bold text-lg">Project Health</h3>
                <p class="text-xs text-gray-400">Distribusi status project saat ini</p>
            </div>
            <div id="statusChart" class="flex justify-center"></div>
        </div>
    </div>

    {{-- BOTTOM SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 fade-in">

        {{-- TOP SERVICES --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-lg">Most Popular Services</h3>
                    <p class="text-xs text-gray-400">Layanan dengan permintaan tertinggi</p>
                </div>
            </div>
            <div id="servicesChart"></div>
        </div>

        {{-- TOP CLIENTS TABLE --}}
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-lg">Top Spending Clients</h3>
                <p class="text-xs text-gray-400">Client dengan total transaksi terbesar</p>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-[10px] uppercase text-gray-500 font-bold">
                        <tr>
                            <th class="px-6 py-3">Client Name</th>
                            <th class="px-6 py-3">Tier</th>
                            <th class="px-6 py-3 text-right">Total Spent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($topClients as $client)
                            @php
                                // Hitung total spent manual dari invoices paid
                                $totalSpent = \App\Models\Invoice::where('user_id', $client->id)
                                    ->where('status', 'paid')
                                    ->sum('amount');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
                                        <img src="{{ $client->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $client->full_name }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <span class="font-bold text-gray-900">{{ $client->full_name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 rounded text-[10px] font-bold uppercase 
                                        {{ str_contains($client->wallet->tier->name ?? '', 'Ultimate') ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $client->wallet->tier->name ?? 'Starter' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900">
                                    Rp {{ number_format($totalSpent, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400">Belum ada data transaksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SCRIPTS FOR CHARTS --}}
    {{-- Menggunakan APEXCHARTS via CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // 1. REVENUE CHART (Area)
        var revenueOptions = {
            series: [{
                name: 'Revenue',
                data: @json($chartRevenue['data']) // [1500000, 3000000, ...]
            }],
            chart: {
                type: 'area',
                height: 320,
                toolbar: {
                    show: false
                },
                fontFamily: 'Inter, sans-serif',
            },
            colors: ['#000000'], // Warna Hitam Vektora
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: @json($chartRevenue['labels']), // ['Jan', 'Feb'...]
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        return "Rp " + (value / 1000000).toFixed(1) + "M"; // Format Juta
                    }
                }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
            }
        };
        var chartRev = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
        chartRev.render();


        // 2. PROJECT STATUS (Donut)
        var statusOptions = {
            series: @json($chartStatus), // [Active, Queue, Completed, Revision]
            labels: ['Active', 'Queue', 'Completed', 'Revision'],
            chart: {
                type: 'donut',
                height: 320,
                fontFamily: 'Inter, sans-serif',
            },
            colors: ['#3b82f6', '#eab308', '#22c55e', '#ef4444'], // Blue, Yellow, Green, Red
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '12px',
                                fontWeight: 600,
                                color: '#9ca3af'
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'bottom'
            }
        };
        var chartStat = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
        chartStat.render();


        // 3. TOP SERVICES (Bar)
        var serviceOptions = {
            series: [{
                name: 'Projects',
                data: @json($chartServices['data'])
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: false
                },
                fontFamily: 'Inter, sans-serif',
            },
            colors: ['#000000'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                    barHeight: '50%'
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: @json($chartServices['labels']),
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
            }
        };
        var chartServ = new ApexCharts(document.querySelector("#servicesChart"), serviceOptions);
        chartServ.render();
    </script>
@endsection
