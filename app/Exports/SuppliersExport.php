<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuppliersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Supplier::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kode',
            'Nama Supplier',
            'Email',
            'Telepon',
            'Alamat',
            'Contact Person',
            'Deskripsi',
            'Status',
            'Dibuat Pada',
            'Diperbarui Pada'
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->code,
            $supplier->name,
            $supplier->email,
            $supplier->phone,
            $supplier->address,
            $supplier->contact_person,
            $supplier->description,
            $supplier->is_active ? 'Aktif' : 'Tidak Aktif',
            $supplier->created_at->format('d-m-Y H:i:s'),
            $supplier->updated_at->format('d-m-Y H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
