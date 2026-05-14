<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    use HasUuids;

    const STATUS_PENDING    = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_PROCESSED  = 2;
    const STATUS_FAILED     = 3;

    protected $fillable = [
        'user_id',
        'event',
        'source',
        'callback_url',
        'payload',
        'status',
        'exception',
        'processed_at',
        'failed_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'processed_at' => 'datetime',
        'failed_at'    => 'datetime',
    ];

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }
    public function isProcessed(): bool
    {
        return $this->status === self::STATUS_PROCESSED;
    }
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => self::STATUS_PROCESSING]);
    }

    public function markAsProcessed(): void
    {
        $this->update(['status' => self::STATUS_PROCESSED, 'processed_at' => now()]);
    }

    public function markAsFailed(string $exception): void
    {
        $this->update(['status' => self::STATUS_FAILED, 'exception' => $exception, 'failed_at' => now()]);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_PROCESSED  => 'Processed',
            self::STATUS_FAILED     => 'Failed',
            default                 => 'Unknown',
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'yellow',
            self::STATUS_PROCESSING => 'blue',
            self::STATUS_PROCESSED  => 'green',
            self::STATUS_FAILED     => 'red',
            default                 => 'gray',
        };
    }
}
