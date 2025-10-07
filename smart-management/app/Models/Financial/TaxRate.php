<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Article;

class TaxRate extends Model
{
    protected $fillable = ['name', 'rate', 'is_active'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
