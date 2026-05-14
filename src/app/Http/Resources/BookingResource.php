<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'title'      => $this->title,
            'room'       => [
                'id'       => $this->room->id,
                'name'     => $this->room->name,
                'location' => $this->room->location,
            ],
            'starts_at'  => $this->starts_at->toDateTimeString(),
            'ends_at'    => $this->ends_at->toDateTimeString(),
            'duration_minutes' => $this->starts_at->diffInMinutes($this->ends_at),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
