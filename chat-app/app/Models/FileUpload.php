<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FileUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message_id',
        'original_name',
        'filename',
        'path',
        'mime_type',
        'size',
        'type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
    //obter url public
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    //verificar se é img
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }


    public function isDocument(): bool
    {
        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
        ];

        return in_array($this->mime_type, $documentTypes);
    }



    public function getIcon(): string
    {
        if ($this->isImage()) {
            return '🖼️';
        }

        if ($this->isDocument()) {
            return match ($this->mime_type) {
                'application/pdf' => '📄',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '📝',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '📊',
                'text/plain' => '📄',
                default => '📎',
            };
        }

        return '📎';
    }


    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($fileUpload) {
            if (Storage::exists($fileUpload->path)) {
                Storage::delete($fileUpload->path);
            }
        });
    }
}