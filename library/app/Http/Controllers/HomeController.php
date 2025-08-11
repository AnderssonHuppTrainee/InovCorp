<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class HomeController extends Controller
{
    public function index()
    {
        $allBooks = Book::latest()->take(8)->get(); // 
        $latestBooks = Book::latest()->take(10)->get(); //os utlimos 5 

        return view('welcome', compact('latestBooks', 'allBooks'));

    }
}
