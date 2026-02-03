<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon Faktur #{{ $transaction->transaction_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Print settings untuk Epson LX310 - Continuous Form 9.5" x 11" */
        @page {
            size: 9.5in 11in;
            margin: 0.25in 0.2in;
        }
        
        @media print {
            body {
                width: 9.5in;
                height: 11in;
                margin: 0;
                padding: 0;
            }
            .page {
                page-break-after: always;
                margin: 0;
                padding: 0.2in 0.3in;
            }
            .page:last-child {
                page-break-after: auto;
            }
        }
        
        body {
            font-family: 'Courier New', 'Courier', monospace;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        /* ============ PAGE STYLES - Continuous Form ============ */
        .page {
            width: 9in;
            min-height: 10.4in;
            padding: 0.2in 0.3in;
            margin: 0 auto;
            page-break-after: always;
            position: relative;
            text-align: left;
        }
        .page:last-child {
            page-break-after: auto;
            margin-bottom: 0;
        }

        /* Page 1 - Putih */
        .page-white {
            background-color: #ffffff;
        }

        /* Page 2 - Pink */
        .page-pink {
            background-color: #ffe4e1;
        }

        /* Page 3 - Kuning */
        .page-yellow {
            background-color: #fffacd;
        }

        /* ============ COMMON STYLES - Optimized for Dot Matrix ============ */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: left;
            padding-left: 12px;
        }
        .company-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .header-left div, .header-right div {
            margin-bottom: 1px;
            font-size: 9px;
        }
        .label {
            display: inline-block;
            width: 90px;
            font-weight: normal;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .items-table th {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
        }
        .items-table td {
            border: 1px solid #000;
            padding: 2px;
            font-size: 9px;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table .text-right {
            text-align: right;
        }
        .summary {
            width: 100%;
            margin-top: 8px;
        }
        .summary table {
            width: 200px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .summary td {
            padding: 3px;
            border-bottom: 1px solid #000;
            font-size: 9px;
        }
        .summary .label-col {
            text-align: right;
            font-weight: bold;
            width: 55%;
        }
        .summary .value-col {
            text-align: right;
            width: 45%;
        }
        .summary .total-row {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            font-weight: bold;
            font-size: 10px;
        }
        .signature-section {
            margin-top: 25px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 10px;
            font-size: 9px;
        }
        .signature-line {
            margin-top: 35px;
            border-top: 1px solid #000;
            padding-top: 4px;
            display: inline-block;
            min-width: 100px;
        }
        .print-date {
            margin-top: 10px;
            font-size: 8px;
            color: #333;
            text-align: right;
        }
        .notes-box {
            margin-top: 10px;
            padding: 6px;
            border: 1px solid #000;
            font-size: 8px;
        }
    </style>
</head>
<body>
    @php
        $subtotal = 0;
        foreach($transaction->details as $detail) {
            $subtotal += $detail->subtotal;
        }
        $discount = $transaction->discount ?? 0;
        $bonus = $transaction->bonus ?? 0;
        $grandTotal = $subtotal - $discount - $bonus;
        
        $pages = [
            ['class' => 'page-white', 'name' => 'Putih'],
            ['class' => 'page-pink', 'name' => 'Pink'],
            ['class' => 'page-yellow', 'name' => 'Kuning']
        ];
    @endphp

    @foreach($pages as $pageIndex => $page)
    <!-- PAGE {{ $pageIndex + 1 }} - {{ $page['name'] }} -->
    <div class="page {{ $page['class'] }}">
        <!-- Header Section -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">UD BERKAH ATHIFA AZZUHRA</div>
                <div>Jl. Contoh Alamat No. 123</div>
                <div>Kec. Kebayoran Baru</div>
                <div>Jakarta Selatan, DKI Jakarta 12345</div>
                <div>Telp: (021) 1234-5678</div>
            </div>
            <div class="header-right">
                <div><span class="label">Tanggal:</span> {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</div>
                <div><span class="label">No. Faktur:</span> <strong>{{ $transaction->transaction_number }}</strong></div>
                <div><span class="label">Nama Pelanggan:</span> {{ $transaction->customer_name ?? '-' }}</div>
                <div><span class="label">Alamat Pelanggan:</span> {{ $transaction->customer_address ?? '-' }}</div>
                @if($transaction->store_name)
                <div><span class="label">Nama Toko:</span> <strong>{{ $transaction->store_name }}</strong></div>
                @endif
                @if($transaction->type === 'out')
                <div><span class="label">Status Pembayaran:</span> 
                    @if($transaction->payment_status === 'paid')
                        <strong style="color: green;">LUNAS</strong>
                    @else
                        <strong style="color: red;">BELUM LUNAS</strong>
                    @endif
                </div>
                @endif
                @if($transaction->vehicle)
                <div><span class="label">Kendaraan:</span> {{ $transaction->vehicle->name }} ({{ $transaction->vehicle->plate_number }})</div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">Kode</th>
                    <th style="width: 25%;">Nama Item</th>
                    <th style="width: 13%;">Qty</th>
                    <th style="width: 12%;">Harga</th>
                    <th style="width: 6%;">D1</th>
                    <th style="width: 6%;">D2</th>
                    <th style="width: 6%;">D3</th>
                    <th style="width: 14%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->details as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $detail->item->code }}</td>
                    <td>{{ $detail->item->name }}</td>
                    <td class="text-center">
                        {{ number_format($detail->quantity, 0, ',', '.') }} {{ strtoupper($detail->unit_type) }}
                    </td>
                    <td class="text-right">{{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-right">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @for($i = count($transaction->details); $i < 5; $i++)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                @endfor
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary">
            <table>
                <tr>
                    <td class="label-col">Sub Total:</td>
                    <td class="value-col">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($discount > 0)
                <tr>
                    <td class="label-col">Diskon:</td>
                    <td class="value-col">Rp {{ number_format($discount, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($bonus > 0)
                <tr>
                    <td class="label-col">Bonus:</td>
                    <td class="value-col">Rp {{ number_format($bonus, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label-col">TOTAL AKHIR:</td>
                    <td class="value-col">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        @if($transaction->notes)
        <div class="notes-box">
            <strong>Catatan:</strong> {{ $transaction->notes }}
        </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div>Penerima,</div>
                <div class="signature-line">
                    (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                </div>
            </div>
            <div class="signature-box">
                <div>Mengetahui,</div>
                <div class="signature-line">
                    {{ $transaction->user->name }}
                </div>
            </div>
        </div>

        <div class="print-date">
            Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
    @endforeach
</body>
</html>
