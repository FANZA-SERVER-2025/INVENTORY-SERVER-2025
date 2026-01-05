<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Item::with(['category', 'supplier'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kode',
            'Nama Item',
            'Kategori',
            'Supplier',
            'Unit',
            'Box Type',
            'Box Quantity',
            'Sub Unit Type',
            'Stok (Pcs)',
            'Stok (Box)',
            'Stok (Lusin)',
            'Stok Minimum',
            'Harga Beli',
            'Harga Jual',
            'Status Stok',
            'Status',
            'Dibuat Pada',
            'Diperbarui Pada'
        ];
    }

    public function map($item): array
    {
        // Calculate stock in different units
        $stockPcs = $item->stock;
        $stockBox = 0;
        $stockLusin = 0;
        
        if ($item->box_type) {
            if ($item->box_type === 'dozen') {
                // For dozen-based items: 1 box = box_quantity lusin
                $pcsPerBox = $item->box_quantity * 12;
                $stockBox = $pcsPerBox > 0 ? floor($stockPcs / $pcsPerBox) : 0;
                $stockLusin = floor($stockPcs / 12);
            } else {
                // For pcs-based items: 1 box = box_quantity pcs
                $stockBox = $item->box_quantity > 0 ? floor($stockPcs / $item->box_quantity) : 0;
            }
        }
        
        return [
            $item->id,
            $item->code,
            $item->name,
            $item->category->name,
            $item->supplier->name,
            $item->unit,
            $item->box_type ? ucfirst($item->box_type) : '-',
            $item->box_quantity ?? '-',
            $item->sub_unit_type ?? '-',
            number_format($stockPcs),
            $stockBox > 0 ? number_format($stockBox) : '-',
            $stockLusin > 0 ? number_format($stockLusin, 2) : '-',
            $item->minimum_stock,
            'Rp ' . number_format($item->purchase_price, 0, ',', '.'),
            'Rp ' . number_format($item->selling_price, 0, ',', '.'),
            $item->isLowStock() ? 'Stok Rendah' : 'Stok Normal',
            $item->is_active ? 'Aktif' : 'Tidak Aktif',
            $item->created_at->format('d-m-Y H:i:s'),
            $item->updated_at->format('d-m-Y H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
