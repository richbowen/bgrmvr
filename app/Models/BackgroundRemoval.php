<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BackgroundRemoval extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'original_filename',
        'original_path',
        'processed_path',
        'mime_type',
        'file_size',
        'replicate_prediction_id',
        'processing_cost',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'processing_cost' => 'decimal:4',
            'file_size' => 'integer',
            'processed_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function findByUuid(string $uuid): ?self
    {
        return static::where('uuid', $uuid)->first();
    }

    public function getOriginalUrlAttribute(): string
    {
        return route('background-removal.download', ['type' => 'original', 'uuid' => $this->uuid]);
    }

    public function getOriginalViewUrlAttribute(): string
    {
        return route('background-removal.view', ['type' => 'original', 'uuid' => $this->uuid]);
    }

    public function getProcessedUrlAttribute(): string
    {
        return route('background-removal.download', ['type' => 'processed', 'uuid' => $this->uuid]);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }
}
