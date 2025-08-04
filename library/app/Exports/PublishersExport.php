<?php

namespace App\Exports;

use App\Models\Publisher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PublishersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Publisher::all();
    }
    public function headings(): array
    {
        return [
            'ID',
            'Nome',
        ];
    }

    public function map($publisher): array
    {
        return [
            $publisher->id,
            $publisher->name,
        ];
    }
}
