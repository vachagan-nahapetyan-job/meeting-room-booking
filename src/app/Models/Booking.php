<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'title',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Scope to find overlapping bookings for a given time range.
     * An overlapping booking occurs if starts_at < $endsAt AND ends_at > $startsAt
     */

    public function scopeOverlapping($query, string $startsAt, string $endsAt, ?int $excludeId = null)
    {
        $query->where('starts_at', '<', $endsAt)
              ->where('ends_at', '>', $startsAt);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query;
    }
}
