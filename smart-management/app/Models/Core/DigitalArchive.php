<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigitalArchive extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'description',
        'document_type',
        'archivable_id',
        'archivable_type',
        'uploaded_by',
        'is_public',
        'expires_at'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'expires_at' => 'datetime'
    ];

    public function archivable()
    {
        return $this->morphTo(); //relação oplimofica pode prtecener a qualquer model
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}