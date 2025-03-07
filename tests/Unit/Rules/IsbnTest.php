<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\Isbn;
use Tests\TestCase;

class IsbnTest extends TestCase
{
    private function validateIsbn($value): bool
    {
        $passed = true;
        $rule = new Isbn();

        $rule->validate('isbn', $value, function() use (&$passed) {
            $passed = false;
        });

        return $passed;
    }

    public function test_validates_valid_isbn10()
    {
        $this->assertTrue($this->validateIsbn('0306406152'));
        $this->assertTrue($this->validateIsbn('080442957X'));
        $this->assertTrue($this->validateIsbn('0-306-40615-2'));
        $this->assertTrue($this->validateIsbn('0 306 40615 2'));
    }

    public function test_rejects_invalid_isbn10()
    {
        $this->assertFalse($this->validateIsbn('1234567890'));
        $this->assertFalse($this->validateIsbn('059380348A'));
        $this->assertFalse($this->validateIsbn('05938034'));
        $this->assertFalse($this->validateIsbn('059380348'));
        $this->assertFalse($this->validateIsbn('059380348XX'));
    }

    public function test_validates_valid_isbn13()
    {
        $this->assertTrue($this->validateIsbn('9780593803486'));
        $this->assertTrue($this->validateIsbn('978-0-593-80348-6'));
        $this->assertTrue($this->validateIsbn('978 0 593 80348 6'));
        $this->assertTrue($this->validateIsbn('9789996256639'));
    }

    public function test_rejects_invalid_isbn13()
    {
        $this->assertFalse($this->validateIsbn('9780593803487'));
        $this->assertFalse($this->validateIsbn('97805938034'));
        $this->assertFalse($this->validateIsbn('97805938034861'));
        $this->assertFalse($this->validateIsbn('978059380348X'));
    }

    public function test_rejects_non_string_value()
    {
        $this->assertFalse($this->validateIsbn(12345));
        $this->assertFalse($this->validateIsbn(null));
        $this->assertFalse($this->validateIsbn([]));
    }
}
