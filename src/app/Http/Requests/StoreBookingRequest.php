<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'   => ['required', 'integer', 'min:1'],
            'room_id'   => ['required', 'integer', 'exists:rooms,id'],
            'title'     => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date_format:Y-m-d H:i:s', 'after:now'],
            'ends_at'   => ['required', 'date_format:Y-m-d H:i:s', 'after:starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'starts_at.after' => 'Нельзя бронировать в прошлом.',
            'ends_at.after'   => 'ends_at должен быть позже starts_at.',
            'room_id.exists'  => 'Переговорка не найдена.',
        ];
    }
}
