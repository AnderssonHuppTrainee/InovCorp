<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Crypt;

class BooksExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Book::with(['authors', 'publisher'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'ISBN',
            'Editora',
            'Autores',
            'Preço',
            'Data Criação'
        ];
    }

    public function map($book): array
    {

        return [
            $book->id,
            $book->name,
            $book->isbn,
            $book->publisher->name ?? 'N/A',
            $book->authors->pluck('name')->join(', '),
            $book->price,
            $book->created_at->format('d/m/Y H:i')
        ];
    }
}
