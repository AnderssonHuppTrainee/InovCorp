<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Contact;

class ContactRole extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
