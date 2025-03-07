<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Isbn implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail($this->message());
            return;
        }

        $value = str_replace(['-', ' '], '', $value);

        if (!preg_match('/^(?:\d{9}[0-9Xx]|\d{13})$/', $value)) {
            $fail($this->message());
            return;
        }

        if (strlen($value) === 10) {
            if (!$this->validateIsbn10($value)) {
                $fail($this->message());
                return;
            }
        } elseif (strlen($value) === 13) {
            if (!$this->validateIsbn13($value)) {
                $fail($this->message());
                return;
            }
        }
    }

    protected function validateIsbn10($isbn): bool
    {
        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            $sum += (10 - $i) * (int)$isbn[$i];
        }

        $lastChar = strtoupper($isbn[9]);
        $sum += ($lastChar === 'X') ? 10 : (int)$lastChar;

        return ($sum % 11 === 0);
    }

    protected function validateIsbn13($isbn): bool
    {
        $sum = 0;

        for ($i = 0; $i < 12; $i++) {
            $digit = (int)$isbn[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }

        $expectedCheck = (10 - ($sum % 10)) % 10;

        return $expectedCheck == $isbn[12];
    }

    protected function message(): string
    {
        return __('api.validation.isbn.invalid');
    }
}
