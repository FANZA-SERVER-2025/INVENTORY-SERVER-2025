<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InvoiceExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction->load(['user', 'vehicle', 'details.item']);
    }

    public function collection()
    {
        $data = collect();
        
        // Header dengan 2 kolom (kiri: company info, kanan: transaction info)
        // Kolom A-E untuk company info, Kolom F-I untuk transaction info
        
        // Row 1: Company Name + Tanggal
        $transactionDate = \Carbon\Carbon::parse($this->transaction->transaction_date)->format('d/m/Y');
        $data->push(['UD BERKAH ATHIFA AZZUHRA', '', '', '', '', 'Tanggal:', '', $transactionDate, '']);
        
        // Row 2: Address + No. Faktur
        $data->push(['Jl. Contoh Alamat No. 123', '', '', '', '', 'No. Faktur:', '', $this->transaction->transaction_number, '']);
        
        // Row 3: District + Nama Pelanggan
        $customerName = $this->transaction->customer_name ?? 'PT. Karya Mandiri';
        $data->push(['Kec. Kebayoran Baru', '', '', '', '', 'Nama Pelanggan:', '', $customerName, '']);
        
        // Row 4: City + Alamat
        $data->push(['Jakarta Selatan, DKI Jakarta 12345', '', '', '', '', 'Alamat', '', '', '']);
        
        // Row 5: Telp + Pelanggan (value)
        $customerAddress = $this->transaction->customer_address ?? 'Bekasi';
        $data->push(['Telp: (021) 1234-5678', '', '', '', '', 'Pelanggan:', '', $customerAddress, '']);
        
        // Row 6+: Additional info (Nama Toko jika ada)
        if ($this->transaction->store_name) {
            $data->push(['', '', '', '', '', 'Nama Toko:', '', $this->transaction->store_name, '']);
        }
        
        // Status Pembayaran untuk transaksi keluar
        if ($this->transaction->type === 'out') {
            $statusPembayaran = $this->transaction->payment_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS';
            $data->push(['', '', '', '', '', 'Status', '', '', '']);
            $data->push(['', '', '', '', '', 'Pembayaran:', '', $statusPembayaran, '']);
        }
        
        // Kendaraan jika ada
        if ($this->transaction->vehicle) {
            $vehicleInfo = $this->transaction->vehicle->name . ' - Delivery 1 (' . $this->transaction->vehicle->plate_number . ')';
            $data->push(['', '', '', '', '', 'Kendaraan:', '', $vehicleInfo, '']);
        }
        
        $data->push(['', '', '', '', '', '', '', '', '']); // Empty row before table
        
        // Calculate subtotal
        $subtotal = 0;
        foreach ($this->transaction->details as $detail) {
            $subtotal += $detail->subtotal;
        }
        
        // Items Table Header - persis seperti PDF (tanpa kolom Unit, Qty sudah include unit)
        $data->push(['No', 'Kode', 'Nama Item', 'Qty', 'Harga', 'D1', 'D2', 'D3', 'Total']);
        
        // Items - format seperti PDF
        foreach ($this->transaction->details as $index => $detail) {
            $qtyWithUnit = number_format($detail->quantity, 0, ',', '.') . ' ' . strtoupper($detail->unit_type);
            
            $data->push([
                $index + 1,
                $detail->item->code ?? '-',
                $detail->item->name ?? '-',
                $qtyWithUnit,
                number_format($detail->price, 0, ',', '.'),
                '-',
                '-',
                '-',
                number_format($detail->subtotal, 0, ',', '.'),
            ]);
        }
        
        // Add empty rows (minimum 5 rows total seperti PDF)
        $totalItems = $this->transaction->details->count();
        if ($totalItems < 5) {
            for ($i = $totalItems; $i < 5; $i++) {
                $data->push([$i + 1, '', '', '', '', '', '', '', '']);
            }
        }
        
        $data->push(['', '', '', '', '', '', '', '', '']); // Empty row after table
        
        // Summary - positioned on the right side (columns H & I)
        $discount = $this->transaction->discount ?? 0;
        $bonus = $this->transaction->bonus ?? 0;
        $grandTotal = $subtotal - $discount - $bonus;
        
        $data->push(['', '', '', '', '', '', '', 'Sub Total:', 'Rp ' . number_format($subtotal, 0, ',', '.')]);
        
        if ($discount > 0) {
            $data->push(['', '', '', '', '', '', '', 'Diskon:', 'Rp ' . number_format($discount, 0, ',', '.')]);
        }
        
        if ($bonus > 0) {
            $data->push(['', '', '', '', '', '', '', 'Bonus:', 'Rp ' . number_format($bonus, 0, ',', '.')]);
        }
        
        $data->push(['', '', '', '', '', '', '', 'TOTAL AKHIR:', 'Rp ' . number_format($grandTotal, 0, ',', '.')]);
        
        if ($this->transaction->notes) {
            $data->push(['', '', '', '', '', '', '', '', '']); // Empty row
            $data->push(['Catatan: ' . $this->transaction->notes, '', '', '', '', '', '', '', '']);
        }
        
        // Signature section - layout 2 kolom yang rapi
        $data->push(['', '', '', '', '', '', '', '', '']); // Empty row
        $data->push(['', '', '', '', '', '', '', '', '']); // Empty row
        $data->push(['Hormat Kami,', '', '', '', '', '', '', 'Penerima,', '']);
        $data->push(['', '', '', '', '', '', '', '', '']);
        $data->push(['', '', '', '', '', '', '', '', '']);
        $data->push(['', '', '', '', '', '', '', '', '']);
        $data->push([$this->transaction->user->name, '', '', '', '', '', '', '(                                           )', '']);
        
        $data->push(['', '', '', '', '', '', '', '', '']); // Empty row
        $data->push(['', '', '', '', '', '', '', '', 'Dicetak pada: ' . now()->format('d/m/Y H:i:s')]);
        
        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Company header - Row 1 - Bold and bigger font
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        // Company address rows
        $sheet->getStyle('A2:A5')->getFont()->setSize(10);
        
        // Right side labels (Tanggal, No. Faktur, etc) - Bold
        $sheet->getStyle('F1:F15')->getFont()->setBold(true);
        
        // Status Pembayaran - make it stand out
        $statusRow = 0;
        foreach ($sheet->getRowIterator() as $row) {
            $cellValue = $sheet->getCell('F' . $row->getRowIndex())->getValue();
            if ($cellValue === 'Pembayaran:') {
                $statusRow = $row->getRowIndex();
                $statusValue = $sheet->getCell('H' . $statusRow)->getValue();
                if ($statusValue === 'LUNAS') {
                    $sheet->getStyle('H' . $statusRow)->getFont()->setBold(true)->getColor()->setRGB('008000');
                } else {
                    $sheet->getStyle('H' . $statusRow)->getFont()->setBold(true)->getColor()->setRGB('FF0000');
                }
                break;
            }
        }
        
        // Find the row where items table starts
        $headerRow = 0;
        foreach ($sheet->getRowIterator() as $row) {
            $cellValue = $sheet->getCell('A' . $row->getRowIndex())->getValue();
            if ($cellValue === 'No') {
                $headerRow = $row->getRowIndex();
                break;
            }
        }
        
        if ($headerRow > 0) {
            // Add border to header info section
            $sheet->getStyle('A1:I' . ($headerRow - 1))->applyFromArray([
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
            
            // Style header row of table
            $sheet->getStyle('A' . $headerRow . ':I' . $headerRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 10],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFFFF']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            
            // Style data rows (5 rows minimum)
            $dataStartRow = $headerRow + 1;
            $dataEndRow = $dataStartRow + 4; // 5 rows minimum
            
            $sheet->getStyle('A' . $dataStartRow . ':I' . $dataEndRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            
            // Center align specific columns
            $sheet->getStyle('A' . $dataStartRow . ':A' . $dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $dataStartRow . ':B' . $dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $dataStartRow . ':D' . $dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $dataStartRow . ':H' . $dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Right align Harga and Total columns
            $sheet->getStyle('E' . $dataStartRow . ':E' . $dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I' . $dataStartRow . ':I' . $dataEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            // Style summary section
            $summaryStartRow = $dataEndRow + 2;
            $sheet->getStyle('H' . $summaryStartRow . ':I' . ($summaryStartRow + 10))->getFont()->setBold(true);
            $sheet->getStyle('H' . $summaryStartRow . ':I' . ($summaryStartRow + 10))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            // Add border to summary
            $sheet->getStyle('H' . $summaryStartRow . ':I' . ($summaryStartRow + 10))->applyFromArray([
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
            
            // Style TOTAL AKHIR row specially
            foreach ($sheet->getRowIterator($summaryStartRow) as $row) {
                $cellValue = $sheet->getCell('H' . $row->getRowIndex())->getValue();
                if ($cellValue === 'TOTAL AKHIR:') {
                    $sheet->getStyle('H' . $row->getRowIndex() . ':I' . $row->getRowIndex())->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'borders' => [
                            'top' => ['borderStyle' => Border::BORDER_THIN],
                            'bottom' => ['borderStyle' => Border::BORDER_DOUBLE],
                        ],
                    ]);
                    break;
                }
            }
        }
        
        // Catatan box
        foreach ($sheet->getRowIterator() as $row) {
            $cellValue = $sheet->getCell('A' . $row->getRowIndex())->getValue();
            if (strpos($cellValue, 'Catatan:') === 0) {
                $sheet->getStyle('A' . $row->getRowIndex() . ':I' . $row->getRowIndex())->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $sheet->mergeCells('A' . $row->getRowIndex() . ':I' . $row->getRowIndex());
                break;
            }
        }
        
        // Signature section styling
        foreach ($sheet->getRowIterator() as $row) {
            $cellValue = $sheet->getCell('A' . $row->getRowIndex())->getValue();
            if ($cellValue === 'Hormat Kami,') {
                $signatureRow = $row->getRowIndex();
                
                // Style "Hormat Kami," dan "Penerima," - center alignment
                $sheet->getStyle('A' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Merge cells untuk "Hormat Kami," section (A-F)
                $sheet->mergeCells('A' . $signatureRow . ':F' . $signatureRow);
                
                // Merge cells untuk "Penerima," section (H-I)
                $sheet->mergeCells('H' . $signatureRow . ':I' . $signatureRow);
                
                // Style nama dan kurung (4 baris ke bawah dari signature header)
                $nameRow = $signatureRow + 4;
                $sheet->getStyle('A' . $nameRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . $nameRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Merge cells untuk nama
                $sheet->mergeCells('A' . $nameRow . ':F' . $nameRow);
                
                // Merge cells untuk kurung penerima
                $sheet->mergeCells('H' . $nameRow . ':I' . $nameRow);
                
                break;
            }
        }
        
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 10,  // Kode
            'C' => 30,  // Nama Item
            'D' => 12,  // Qty
            'E' => 13,  // Harga
            'F' => 6,   // D1
            'G' => 6,   // D2
            'H' => 6,   // D3
            'I' => 16,  // Total
        ];
    }

    public function title(): string
    {
        return 'Invoice ' . substr($this->transaction->transaction_number, 0, 20);
    }
}
