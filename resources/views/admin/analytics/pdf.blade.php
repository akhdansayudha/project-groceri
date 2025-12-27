<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Analytics Report - Vektora</title>
    <style>
        /* --- 1. REGISTER FONT LENGKAP (Agar tidak fallback ke Helvetica) --- */

        /* Regular (400) */
        @font-face {
            font-family: 'Manrope';
            src: url('{{ public_path('fonts/Manrope-Regular.ttf') }}') format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        /* Medium (500) - Opsional untuk text semi-tebal */
        @font-face {
            font-family: 'Manrope';
            src: url('{{ public_path('fonts/Manrope-Medium.ttf') }}') format("truetype");
            font-weight: 500;
            font-style: normal;
        }

        /* Bold (700) - PENTING: DomPDF butuh ini untuk tag <b> atau class font-bold standar */
        @font-face {
            font-family: 'Manrope';
            src: url('{{ public_path('fonts/Manrope-Bold.ttf') }}') format("truetype");
            font-weight: bold;
            font-style: normal;
        }

        /* ExtraBold (800) - Untuk Judul Besar */
        @font-face {
            font-family: 'Manrope';
            src: url('{{ public_path('fonts/Manrope-ExtraBold.ttf') }}') format("truetype");
            font-weight: 800;
            font-style: normal;
        }

        @page {
            margin: 40px 40px 50px 40px;
        }

        body {
            font-family: 'Manrope', sans-serif;
            color: #111;
            font-size: 11px;
            line-height: 1.4;
            background-color: #ffffff;
        }

        /* HELPER CLASSES */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .font-extrabold {
            font-weight: 800;
        }

        /* Untuk Angka Besar */
        .font-bold {
            font-weight: bold;
        }

        /* Untuk Header Tabel */
        .text-gray {
            color: #6b7280;
        }

        /* HEADER / KOP */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #111;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .brand-name {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1;
        }

        .brand-dot {
            color: #2563eb;
        }

        .brand-sub {
            font-size: 10px;
            color: #666;
            margin-top: 6px;
        }

        .report-title {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #6b7280;
        }

        .report-period {
            font-size: 12px;
            font-weight: 800;
            margin-top: 4px;
            margin-bottom: 4px;
        }

        .report-meta {
            font-size: 9px;
            color: #9ca3af;
            line-height: 1.3;
        }

        /* BENTO GRID SUMMARY - DIPERBAIKI */
        .bento-container {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            /* Kurangi spacing agar lebih pas */
            margin-bottom: 30px;
            /* HAPUS margin negatif */
        }

        .bento-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            width: 33.33%;
            text-align: left;
            /* Konten di dalam card tetap kiri */
        }

        .card-label {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .card-value {
            font-size: 20px;
            font-weight: 800;
            color: #111;
            letter-spacing: -0.5px;
        }

        /* SECTION TITLES */
        .section-title {
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 15px;
            margin-top: 20px;
            padding-left: 10px;
            border-left: 4px solid #111;
            line-height: 1;
        }

        /* TABLES */
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .data-table th {
            text-align: left;
            padding: 12px 15px;
            background-color: #f3f4f6;
            color: #4b5563;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            font-size: 10px;
            font-weight: normal;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        /* RANKING STYLES */
        .rank-column {
            width: 40px;
            text-align: center !important;
        }

        .rank-top {
            font-weight: 800;
            color: #111;
            font-size: 11px;
        }

        .rank-normal {
            color: #9ca3af;
            font-weight: normal;
            font-size: 11px;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 30px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        .footer-content {
            width: 100%;
            color: #9ca3af;
            font-size: 9px;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td width="50%" style="vertical-align: middle;">
                <div class="brand-name">vektora<span class="brand-dot">.</span></div>
                <div class="brand-sub">
                    Creative Agency Management<br>
                    Jakarta Selatan, Indonesia
                </div>
            </td>
            <td width="50%" class="text-right" style="vertical-align: middle;">
                <div class="report-title">Analytics Report</div>
                <div class="report-period">
                    {{ $startDate->format('d M Y') }} – {{ $endDate->format('d M Y') }}
                </div>
                <div class="report-meta">
                    Generated: {{ now()->format('d M Y, H:i') }}<br>
                    By: {{ auth()->user()->full_name ?? 'System' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- SUMMARY CARDS (Container Centered, Konten Card Rata Kiri) --}}
    <table class="bento-container">
        <tr>
            <td class="bento-card">
                <div class="card-label">Total Revenue</div>
                <div class="card-value">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
            </td>
            <td class="bento-card">
                <div class="card-label">Projects Created</div>
                <div class="card-value">
                    {{ $summary['total_projects'] }}
                    <span style="font-size: 12px; color: #9ca3af; font-weight: normal;">Tasks</span>
                </div>
            </td>
            <td class="bento-card">
                <div class="card-label">Avg. Deal Value</div>
                <div class="card-value">Rp {{ number_format($summary['avg_deal'], 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- 1. MOST POPULAR SERVICES --}}
    <div class="section-title">Most Popular Services</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="rank-column">#</th>
                <th width="41%">Service Name</th>
                <th width="25%" class="text-left">Total Projects</th>
                <th width="25%" class="text-left">Toratix Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($topServices as $service)
                <tr>
                    <td class="rank-column">
                        <span class="{{ $loop->iteration <= 3 ? 'rank-top' : 'rank-normal' }}">
                            #{{ $loop->iteration }}
                        </span>
                    </td>
                    <td>{{ $service->name }}</td>
                    <td class="text-left">{{ $service->tasks_count }}</td>
                    <td class="text-left text-gray">{{ $service->toratix_cost }} TX</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 2. TOP STAFF --}}
    <div class="section-title">Top Staff Performance</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="rank-column">#</th>
                <th width="41%">Staff Name</th>
                <th width="25%" class="text-left">Projects Done</th>
                <th width="25%" class="text-left">Tokens Earned</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($topStaff as $staff)
                <tr>
                    <td class="rank-column">
                        <span class="{{ $loop->iteration <= 3 ? 'rank-top' : 'rank-normal' }}">
                            #{{ $loop->iteration }}
                        </span>
                    </td>
                    <td>{{ $staff->full_name }}</td>
                    <td class="text-left">{{ $staff->completed_count }}</td>
                    <td class="text-left">
                        {{ number_format($staff->earned_tokens) }}
                        <span style="font-size:9px; color:#999;">TX</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 3. TOP CLIENTS --}}
    <div class="section-title">Top Spending Clients</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="rank-column">#</th>
                <th width="30%">Client Name</th>
                <th width="36%">Email</th>
                <th width="25%" class="text-right">Total Spent</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($topClients as $client)
                <tr>
                    <td class="rank-column">
                        <span class="{{ $loop->iteration <= 3 ? 'rank-top' : 'rank-normal' }}">
                            #{{ $loop->iteration }}
                        </span>
                    </td>
                    <td>{{ $client->full_name }}</td>
                    <td class="text-gray">{{ $client->email }}</td>
                    <td class="text-left">Rp {{ number_format($client->total_spent, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER CONTENT --}}
    <div class="footer">
        <table class="footer-content">
            <tr>
                <td width="60%">
                    <span style="font-weight: bold; color: #111;">Vektora Creative Agency</span><br>
                    Confidential Report &bull; Generated automatically by system.
                </td>
                <td width="40%" class="text-right" style="vertical-align: bottom;">
                    {{-- Nomor halaman akan muncul di sini via script --}}
                </td>
            </tr>
        </table>
    </div>

    {{-- SCRIPT PAGINATION - DIPERBAIKI --}}
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM}";
            $size = 9;
            // GANTI: Gunakan Helvetica yang lebih reliable di dompdf
            $font = $fontMetrics->getFont("Helvetica");
            $width = $fontMetrics->get_text_width($text, $font, $size);
            
            // Posisi: Kanan Bawah
            $pdf->page_text($pdf->get_width() - $width - 40, $pdf->get_height() - 28, $text, $font, $size, array(0.4, 0.4, 0.4));
        }
    </script>

</body>

</html>
