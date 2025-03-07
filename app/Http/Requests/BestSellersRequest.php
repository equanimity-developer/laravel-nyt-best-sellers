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
            'author.string' => __('api.validation.author.string'),
            'author.max' => __('api.validation.author.max', ['max' => 100]),
            'isbn.array' => __('api.validation.isbn.array'),
            'isbn.*.string' => __('api.validation.isbn.string'),
            'isbn.*.regex' => 'Each ISBN must be a valid 10-digit or 13-digit ISBN',
            'title.string' => __('api.validation.title.string'),
            'title.max' => __('api.validation.title.max', ['max' => 255]),
            'offset.integer' => __('api.validation.offset.integer'),
            'offset.min' => __('api.validation.offset.min'),
            'offset.max' => __('api.validation.offset.max', ['max' => 1000000]),
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
