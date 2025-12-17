<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportsExport implements WithMultipleSheets
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function sheets(): array
    {
        return [
            new RecapSheet($this->from, $this->to),
            new OmsetSheet(),
        ];
    }
}

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\DB;

class RecapSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection(): Collection
    {
        // Get IN recap
        $inRecap = TransactionDetail::select('item_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_value'),
                DB::raw("'IN' as type")
            )
            ->whereHas('transaction', function ($q) {
                $q->where('type', 'in')
                  ->whereBetween('transaction_date', [$this->from, $this->to]);
            })
            ->with(['item.category'])
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->get();

        // Get OUT recap
        $outRecap = TransactionDetail::select('item_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_value'),
                DB::raw("'OUT' as type")
            )
            ->whereHas('transaction', function ($q) {
                $q->where('type', 'out')
                  ->whereBetween('transaction_date', [$this->from, $this->to]);
            })
            ->with(['item.category'])
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->get();

        // Combine and format
        $data = collect();

        if ($inRecap->isNotEmpty()) {
            $data->push(['REKAPAN BARANG MASUK', '', '', '', '']);
            $data->push(['Item', 'Category', 'Total Qty', 'Total Value', 'Type']);
            foreach ($inRecap as $row) {
                $data->push([
                    $row->item?->name ?? '-',
                    $row->item?->category?->name ?? '-',
                    $row->total_qty,
                    $row->total_value,
                    $row->type
                ]);
            }
            $data->push(['', '', '', '', '']); // Empty row
        }

        if ($outRecap->isNotEmpty()) {
            $data->push(['REKAPAN BARANG KELUAR', '', '', '', '']);
            $data->push(['Item', 'Category', 'Total Qty', 'Total Value', 'Type']);
            foreach ($outRecap as $row) {
                $data->push([
                    $row->item?->name ?? '-',
                    $row->item?->category?->name ?? '-',
                    $row->total_qty,
                    $row->total_value,
                    $row->type
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Laporan Rekapan Barang Masuk dan Keluar',
            'Periode: ' . $this->from . ' - ' . $this->to,
            '',
            '',
            ''
        ];
    }

    public function title(): string
    {
        return 'Rekapan';
    }

    public function styles(Worksheet $sheet)
    {
        // Style for title
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1:E1')->applyFromArray([
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

        // Style for period
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2:E2')->applyFromArray([
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
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Find section headers and style them
        $highestRow = $sheet->getHighestRow();
        for ($row = 3; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('A' . $row)->getValue();
            if (strpos($cellValue, 'REKAPAN') === 0) {
                $sheet->mergeCells('A'.$row.':E'.$row);
                $sheet->getStyle('A'.$row.':E'.$row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => strpos($cellValue, 'MASUK') ? 'FF9800' : 'F44336'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                $row++; // Skip the header row
                // Style column headers
                $sheet->getStyle('A'.$row.':E'.$row)->applyFromArray([
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
        }

        // Add borders to all cells
        $sheet->getStyle('A1:E'.$highestRow)->applyFromArray([
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
            'B' => 20, // Category
            'C' => 15, // Total Qty
            'D' => 20, // Total Value
            'E' => 10, // Type
        ];
    }
}

class OmsetSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function collection(): Collection
    {
        // Daily omset (last 30 days)
        $dailyOmset = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyOmset->push([
                $date->format('Y-m-d'),
                $date->format('l'),
                (float) Transaction::where('type', 'out')
                    ->whereDate('transaction_date', $date)
                    ->sum('total_amount'),
            ]);
        }

        // Weekly omset (last 12 weeks)
        $weeklyOmset = collect();
        for ($i = 11; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            $weeklyOmset->push([
                $weekStart->format('Y-m-d') . ' - ' . $weekEnd->format('Y-m-d'),
                (float) Transaction::where('type', 'out')
                    ->whereBetween('transaction_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                    ->sum('total_amount'),
            ]);
        }

        // Monthly omset (last 12 months)
        $monthlyOmset = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyOmset->push([
                $month->format('F Y'),
                (float) Transaction::where('type', 'out')
                    ->whereYear('transaction_date', $month->year)
                    ->whereMonth('transaction_date', $month->month)
                    ->sum('total_amount'),
            ]);
        }

        $data = collect();
        $data->push(['OMSET HARIAN (30 hari terakhir)', '', '']);
        $data->push(['Tanggal', 'Hari', 'Omset']);
        $data = $data->merge($dailyOmset);
        $data->push(['', '', '']);
        $data->push(['OMSET MINGGUAN (12 minggu terakhir)', '', '']);
        $data->push(['Minggu', 'Omset', '']);
        $data = $data->merge($weeklyOmset);
        $data->push(['', '', '']);
        $data->push(['OMSET BULANAN (12 bulan terakhir)', '', '']);
        $data->push(['Bulan', 'Omset', '']);
        $data = $data->merge($monthlyOmset);

        return $data;
    }

    public function headings(): array
    {
        return [
            'Laporan Omset Harian, Mingguan, dan Bulanan',
            '',
            ''
        ];
    }

    public function title(): string
    {
        return 'Omset';
    }

    public function styles(Worksheet $sheet)
    {
        // Style for title
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9C27B0'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Find section headers and style them
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('A' . $row)->getValue();
            if (strpos($cellValue, 'OMSET') === 0) {
                $sheet->mergeCells('A'.$row.':C'.$row);
                $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '3F51B5'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                $row++; // Skip the header row
                // Style column headers
                $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
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
        }

        // Add borders to all cells
        $sheet->getStyle('A1:C'.$highestRow)->applyFromArray([
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
            'A' => 25,
            'B' => 20,
            'C' => 20,
        ];
    }
}