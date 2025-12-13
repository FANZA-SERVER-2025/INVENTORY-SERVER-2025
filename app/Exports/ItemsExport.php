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
            'Stok',
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
        return [
            $item->id,
            $item->code,
            $item->name,
            $item->category->name,
            $item->supplier->name,
            $item->unit,
            $item->stock,
            $item->minimum_stock,
            $item->purchase_price,
            $item->selling_price,
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
