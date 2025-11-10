<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Contact;

class ContactRole extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'is_active'];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
