<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Book;

class BookModal extends Component
{
    public Book $book;

    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.book-modal');
    }
}
