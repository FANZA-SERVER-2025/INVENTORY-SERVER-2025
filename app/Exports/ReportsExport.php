<?php

namespace App\Exports;

use App\Models\TransactionDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;

class ReportsExport implements FromCollection, WithHeadings, WithTitle
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
        return 'Reports_' . $this->from . '_to_' . $this->to;
    }
}