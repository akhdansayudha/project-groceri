<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $invoice->invoice_number }}</title>

    <style>
        /* PERBAIKAN: Menambahkan weight 600 agar nama user & tanggal terbaca benar */
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');

        @page {
            margin: 0px;
        }

        body {
            font-family: 'Manrope', sans-serif;
            color: #111;
            line-height: 1.4;
            font-size: 12px;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* HEADER SECTION (Gray Top) */
        .header-bg {
            background-color: #F8F9FB;
            /* Sama seperti show.blade.php */
            padding: 40px;
            border-bottom: 1px solid #E5E7EB;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-text {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -1px;
            color: #000;
            margin-bottom: 8px;
        }

        .logo-dot {
            color: #2563EB;
        }

        .company-info {
            color: #6B7280;
            font-size: 10px;
            line-height: 1.4;
        }

        .company-name {
            font-weight: 700;
            color: #111;
            font-size: 11px;
        }

        .invoice-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #9CA3AF;
            font-weight: 700;
        }

        .invoice-number {
            font-size: 20px;
            font-weight: 700;
            margin-top: 5px;
            color: #111;
            letter-spacing: -0.5px;
        }

        /* STATUS BADGE */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 8px;
            letter-spacing: 0.5px;
        }

        .badge-paid {
            background-color: #DCFCE7;
            color: #15803d;
            border: 1px solid #BBF7D0;
        }

        .badge-unpaid {
            background-color: #FEF9C3;
            color: #854D0E;
            border: 1px solid #FEF08A;
        }

        /* META GRID */
        .meta-section {
            padding: 30px 40px;
        }

        .meta-label {
            font-size: 9px;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        /* INI YANG SEBELUMNYA ERROR KARENA FONT WEIGHT 600 TIDAK DIIMPORT */
        .meta-value {
            font-size: 13px;
            font-weight: 600;
            color: #111;
        }

        .meta-sub {
            font-size: 10px;
            color: #6B7280;
            margin-top: 2px;
        }

        /* ITEMS TABLE */
        .table-container {
            padding: 0 40px;
        }

        .items-table {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #F3F4F6;
        }

        .items-table th {
            background-color: #F9FAFB;
            color: #6B7280;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            padding: 12px 20px;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
        }

        .items-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #F3F4F6;
            vertical-align: top;
        }

        .item-name {
            font-size: 13px;
            font-weight: 700;
            color: #111;
        }

        .item-desc {
            font-size: 11px;
            color: #6B7280;
            margin-top: 4px;
        }

        /* TOTAL SECTION */
        .total-row td {
            background-color: #F8F9FB;
            border-bottom: none;
            padding: 20px;
        }

        .total-label {
            font-size: 10px;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .total-amount {
            font-size: 18px;
            font-weight: 800;
            color: #000;
            letter-spacing: -0.5px;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 30px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 9px;
            color: #9CA3AF;
            border-top: 1px solid #E5E7EB;
            padding-top: 15px;
            line-height: 1.6;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header-bg">
        <table>
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="logo-text">vektora<span class="logo-dot">.</span></div>
                    <div class="company-info">
                        <span class="company-name">Vektora Creative Agency</span><br>
                        Surabaya, Indonesia<br>
                        support@vektora.id
                    </div>
                </td>
                <td style="width: 40%; vertical-align: top; text-align: right;">
                    <div class="invoice-label">Receipt</div>
                    <div class="invoice-number">#{{ $invoice->invoice_number }}</div>

                    @if ($invoice->status == 'paid')
                        <span class="badge badge-paid">PAID</span>
                    @else
                        <span class="badge badge-unpaid">UNPAID</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- META INFO --}}
    <div class="meta-section">
        <table>
            <tr>
                <td style="width: 35%; vertical-align: top;">
                    <div class="meta-label">Billed To</div>
                    <div class="meta-value">{{ $user->full_name }}</div>
                    <div class="meta-sub">{{ $user->email }}</div>
                </td>
                <td style="width: 30%; vertical-align: top;">
                    <div class="meta-label">Issued Date</div>
                    <div class="meta-value">{{ $invoice->created_at->format('d M Y') }}</div>
                    <div class="meta-sub">{{ $invoice->created_at->format('H:i') }} WIB</div>
                </td>
                <td style="width: 35%; vertical-align: top; text-align: right;">
                    <div class="meta-label">Payment Date</div>
                    <div class="meta-value">
                        {{ $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at)->format('d M Y') : '-' }}
                    </div>
                    <div class="meta-sub">
                        {{ $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at)->format('H:i') . ' WIB' : '' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- TABLE --}}
    <div class="table-container">
        <table class="items-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 15%; text-align: center;">Qty</th>
                    <th style="width: 20%; text-align: right;">Price</th>
                    <th style="width: 15%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-name">{{ $details['item_name'] }}</div>
                        <div class="item-desc">{{ $invoice->description }}</div>
                    </td>
                    <td style="text-align: center; font-weight: 500; color: #111;">
                        {{ $details['qty'] }}
                    </td>
                    <td style="text-align: right; color: #4B5563;">
                        Rp {{ number_format($details['price_per_unit'], 0, ',', '.') }}
                    </td>
                    <td style="text-align: right; font-weight: 700; color: #111;">
                        Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                    </td>
                </tr>

                {{-- GRAND TOTAL --}}
                <tr class="total-row">
                    <td colspan="2"></td>
                    <td style="text-align: right;">
                        <div class="total-label">Grand Total</div>
                    </td>
                    <td style="text-align: right;">
                        <div class="total-amount">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        *This document is automatically generated by Vektora System.<br>
        Generated at {{ now()->format('d M Y, H:i') }} WIB &bull; &copy; {{ date('Y') }} Vektora Creative Agency.
    </div>

</body>

</html>
