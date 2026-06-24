<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tour = $this->route('tour');

        return $this->user()?->hasRole('host')
            && (! $tour || (int) $tour->host_id === (int) $this->user()->id);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:365'],
            'duration_nights' => ['required', 'integer', 'min:0', 'max:365'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1', 'max:10000'],
            'itinerary' => ['required', 'array', 'min:1'],
            'highlights' => ['nullable', 'array'],
            'included' => ['nullable', 'array'],
            'policies' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
            'images' => ['nullable', 'array', 'max:12'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'deleted_images' => ['nullable', 'array'],
            'deleted_images.*' => ['integer'],
        ];
    }
}
