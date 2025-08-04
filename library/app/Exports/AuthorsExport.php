<?php

namespace App\Exports;

use App\Models\Author;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AuthorsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Author::all();
    }
    public function headings(): array
    {
        return [
            'ID',
            'Nome',
        ];
    }

    public function map($author): array
    {
        return [
            $author->id,
            $author->name,
        ];
    }
}
