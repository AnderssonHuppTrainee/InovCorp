<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class HomeController extends Controller
{
    public function index()
    {
        $allBooks = Book::paginate(8); // todos 
        $latestBooks = Book::latest()->take(5)->get(); //os utlimos 5 

        return view('welcome', compact('latestBooks', 'allBooks'));

    }
}
