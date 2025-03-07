<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\Isbn;
use Illuminate\Foundation\Http\FormRequest;

class BestSellersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'author' => 'sometimes|string|max:100',
            'isbn' => 'sometimes|array',
            'isbn.*' => ['required', 'string', new Isbn()],
            'title' => 'sometimes|string|max:255',
            'offset' => 'sometimes|integer|min:0|max:1000000',
        ];
    }

    public function messages(): array
    {
        return [
            'author.string' => 'Author must be a text string',
            'author.max' => 'Author name cannot exceed 100 characters',
            'isbn.array' => 'ISBN must be provided as an array',
            'isbn.*.string' => 'Each ISBN must be a string',
            'isbn.*.regex' => 'Each ISBN must be a valid 10-digit or 13-digit ISBN',
            'title.string' => 'Title must be a text string',
            'title.max' => 'Title cannot exceed 255 characters',
            'offset.integer' => 'Offset must be an integer',
            'offset.min' => 'Offset cannot be negative',
            'offset.max' => 'Offset cannot exceed 1,000,000',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('isbn') && is_string($this->input('isbn'))) {
            $this->merge([
                'isbn' => [$this->input('isbn')]
            ]);
        }
    }
}
