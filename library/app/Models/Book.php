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
        'price'
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
    public function isAvailable()
    {
        return $this->available;
    }
}
