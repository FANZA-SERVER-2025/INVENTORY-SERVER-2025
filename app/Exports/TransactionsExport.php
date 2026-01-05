<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransactionsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $typeFilter;
    protected $paymentStatusFilter;

    public function __construct($typeFilter = null, $paymentStatusFilter = null)
    {
        $this->typeFilter = $typeFilter;
        $this->paymentStatusFilter = $paymentStatusFilter;
    }

    public function collection()
    {
        $query = Transaction::with(['user', 'vehicle', 'details.item']);
        
        // Filter by transaction type
        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        // Filter by payment status
        if ($this->paymentStatusFilter) {
            $query->where('payment_status', $this->paymentStatusFilter);
        }
        
        $transactions = $query->orderBy('transaction_date', 'desc')->get();
        
        // Build data with transaction details
        $data = collect();
        
        foreach ($transactions as $transaction) {
            // Add transaction header
            $data->push([
                'NO. TRANSAKSI: ' . $transaction->transaction_number,
                'Tipe: ' . ($transaction->type === 'in' ? 'Masuk' : 'Keluar'),
                'Tanggal: ' . $transaction->transaction_date->format('d-m-Y'),
                'User: ' . $transaction->user->name,
                $transaction->type === 'out' ? ('Status: ' . ($transaction->payment_status === 'paid' ? 'Lunas' : 'Belum Lunas')) : '',
                '',
                '',
                '',
                '',
            ]);
            
            // Add detail headers
            $data->push([
                'Item',
                'Unit Type',
                'Box',
                'Lusin',
                'Pcs',
                'Harga',
                'Diskon',
                'Bonus',
                'Subtotal',
            ]);
            
            // Add details
            foreach ($transaction->details as $detail) {
                $lusin = $detail->quantity / 12;
                $pcs = $detail->unit_type === 'pcs' ? $detail->quantity : 0;
                
                $data->push([
                    $detail->item->name ?? '-',
                    ucfirst($detail->unit_type),
                    $detail->box_quantity > 0 ? number_format($detail->box_quantity) : '-',
                    $lusin > 0 ? number_format($lusin, 2) : '-',
                    $pcs > 0 ? number_format($pcs) : '-',
                    'Rp ' . number_format($detail->price, 0, ',', '.'),
                    $detail->discount > 0 ? 'Rp ' . number_format($detail->discount, 0, ',', '.') : '-',
                    $detail->bonus > 0 ? 'Rp ' . number_format($detail->bonus, 0, ',', '.') : '-',
                    'Rp ' . number_format($detail->subtotal, 0, ',', '.'),
                ]);
            }
            
            // Add total
            $data->push([
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'TOTAL:',
                'Rp ' . number_format($transaction->total_amount, 0, ',', '.'),
            ]);
            
            // Add notes if any
            if ($transaction->notes) {
                $data->push([
                    'Catatan: ' . $transaction->notes,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ]);
            }
            
            // Add empty row separator
            $data->push(['', '', '', '', '', '', '', '', '']);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'LAPORAN TRANSAKSI ' . strtoupper($this->typeFilter === 'in' ? 'MASUK' : ($this->typeFilter === 'out' ? 'KELUAR' : '')),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style for title
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Find and style transaction headers and detail headers
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('A' . $row)->getValue();
            
            // Transaction header (NO. TRANSAKSI)
            if (strpos($cellValue, 'NO. TRANSAKSI:') === 0) {
                $sheet->mergeCells('A'.$row.':I'.$row);
                $sheet->getStyle('A'.$row.':I'.$row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2196F3'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
            }
            
            // Detail column headers
            if ($cellValue === 'Item') {
                $sheet->getStyle('A'.$row.':I'.$row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '607D8B'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            }
            
            // Total row
            if ($sheet->getCell('H' . $row)->getValue() === 'TOTAL:') {
                $sheet->getStyle('A'.$row.':I'.$row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0E0E0'],
                    ],
                ]);
            }
            
            // Notes row
            if (strpos($cellValue, 'Catatan:') === 0) {
                $sheet->mergeCells('A'.$row.':I'.$row);
                $sheet->getStyle('A'.$row.':I'.$row)->applyFromArray([
                    'font' => [
                        'italic' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F5F5'],
                    ],
                ]);
            }
        }

        // Add borders to all cells
        $sheet->getStyle('A1:I'.$highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30, // Item
            'B' => 12, // Unit Type
            'C' => 10, // Box
            'D' => 10, // Lusin
            'E' => 10, // Pcs
            'F' => 15, // Harga
            'G' => 12, // Diskon
            'H' => 12, // Bonus
            'I' => 18, // Subtotal
        ];
    }
}
