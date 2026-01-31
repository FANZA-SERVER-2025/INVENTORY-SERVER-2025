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
                'No',
                'Kode',
                'Nama Item',
                'Qty',
                'Harga',
                'D1',
                'D2',
                'D3',
                'Total',
            ]);
            
            // Add details
            $no = 1;
            foreach ($transaction->details as $detail) {
                $qty = $detail->quantity . ' ' . strtoupper($detail->unit_type);
                
                // Format discount and bonus - show value or '-'
                // Convert to float for comparison since decimal fields return as strings
                $discountValue = floatval($detail->discount ?? 0);
                $bonusValue = floatval($detail->bonus ?? 0);
                
                $discount = $discountValue > 0 ? $discountValue : '-';
                $bonus = $bonusValue > 0 ? $bonusValue : '-';
                
                $data->push([
                    $no++,
                    $detail->item->code ?? '-',
                    $detail->item->name ?? '-',
                    $qty,
                    floatval($detail->price),
                    $discount,
                    $bonus,
                    '-',
                    floatval($detail->subtotal),
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
                'Sub Total:',
                floatval($transaction->total_amount),
            ]);
            
            $data->push([
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'TOTAL AKHIR:',
                floatval($transaction->total_amount),
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
            if ($cellValue === 'No') {
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
            if ($sheet->getCell('H' . $row)->getValue() === 'Sub Total:' || $sheet->getCell('H' . $row)->getValue() === 'TOTAL AKHIR:') {
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
            'A' => 8,  // No
            'B' => 12, // Kode
            'C' => 30, // Nama Item
            'D' => 12, // Qty
            'E' => 15, // Harga
            'F' => 12, // D1 (Diskon)
            'G' => 12, // D2 (Bonus)
            'H' => 5,  // D3 (-)
            'I' => 18, // Total
        ];
    }
}
