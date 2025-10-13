<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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
        'expires_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public function archivable()
    {
        return $this->morphTo(); //relacao polimorfica, pode pertencer a qualquer model
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }


    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    public function scopeByDocumentType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeFilter($query, array $filters = [])
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        })->when($filters['document_type'] ?? null, function ($query, $type) {
            if ($type !== 'all') {
                $query->where('document_type', $type);
            }
        })->when(isset($filters['is_public']) && $filters['is_public'] !== 'all', function ($query) use ($filters) {
            $query->where('is_public', $filters['is_public'] === '1');
        })->when($filters['archivable_type'] ?? null, function ($query, $type) {
            if ($type !== 'all') {
                $query->where('archivable_type', $type);
            }
        });
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function fileExists(): bool
    {
        return Storage::exists($this->file_path);
    }

    public function getFormattedFileSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->file_size;

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileExtension(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function getFileIcon(): string
    {
        $extension = strtolower($this->getFileExtension());

        $icons = [
            'pdf' => 'file-text',
            'doc' => 'file-text',
            'docx' => 'file-text',
            'xls' => 'file-spreadsheet',
            'xlsx' => 'file-spreadsheet',
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'gif' => 'image',
            'svg' => 'image',
            'zip' => 'file-archive',
            'rar' => 'file-archive',
            '7z' => 'file-archive',
        ];

        return $icons[$extension] ?? 'file';
    }

    public function deleteFile(): bool
    {
        if ($this->fileExists()) {
            return Storage::delete($this->file_path);
        }

        return true;
    }
}
