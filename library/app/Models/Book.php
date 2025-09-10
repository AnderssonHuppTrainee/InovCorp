<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'isbn',
        'name',
        'publisher_id',
        'bibliography',
        'cover_image',
        'price',
        'available',
        'stock',
    ];
    //cryptografy
    protected $casts = [
        'bibliography' => 'encrypted',

    ];

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_book');
    }

    public function requests()
    {
        return $this->hasMany(BookRequest::class);
    }
    public function reviews()
    {
        return $this->hasManyThrough(Review::class, BookRequest::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()
            ->where('status', 'active')
            ->avg('rating') ?? 0; // rturn 0 se n houver avaliacao
    }

    public function isAvailable()
    {
        return $this->available;
    }

    public function relatedBooks($limit)
    {

        $books = Book::where('id', '!=', $this->id)->get();

        $currentKeywords = $this->extractKeywords($this->bibliography);

        $books = $books->map(function ($book) use ($currentKeywords) {
            $otherKeywords = $this->extractKeywords($book->bibliography);
            $common = count(array_intersect($currentKeywords, $otherKeywords));
            $book->similarity_score = $common;
            return $book;
        });

        return $books->sortByDesc('similarity_score')->take($limit);

    }

    public function extractKeywords($text)
    {
        if (!$text)
            return [];

        //passa para lowercase
        $text = mb_strtolower($text);

        //remover pontuacao
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);

        //dividir em palavras
        $words = preg_split('/\s+/', $text);

        $stopwords = ['a', 'o', 'e', 'de', 'do', 'da', 'em', 'para', 'com', 'um', 'uma', 'que', 'no', 'na', 'os', 'as', 'por', 'se', 'mais', 'como'];
        //remover stopwords e palavras curtas
        $keywords = array_filter($words, function ($w) use ($stopwords) {
            return mb_strlen($w) > 2 && !in_array($w, $stopwords);
        });

        return array_values($keywords);
    }
}
