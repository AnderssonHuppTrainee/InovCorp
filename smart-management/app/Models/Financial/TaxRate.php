<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Article;

class TaxRate extends Model
{
    protected $fillable = ['name', 'rate', 'is_active'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
