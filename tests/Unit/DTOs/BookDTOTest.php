<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\BookDTO;
use Tests\TestCase;

class BookDTOTest extends TestCase
{
    public function test_can_create_book_dto()
    {
        $dto = new BookDTO(
            'Test Title',
            'Test Author',
            'Test Description',
            'Test Publisher',
            ['1234567890', '9781234567897'],
            [['rank' => 1, 'list' => 'Fiction']]
        );

        $this->assertEquals('Test Title', $dto->title);
        $this->assertEquals('Test Author', $dto->author);
        $this->assertEquals('Test Description', $dto->description);
        $this->assertEquals('Test Publisher', $dto->publisher);
        $this->assertEquals(['1234567890', '9781234567897'], $dto->isbn);
        $this->assertEquals([['rank' => 1, 'list' => 'Fiction']], $dto->ranks);
    }

    public function test_can_create_book_dto_from_array()
    {
        $data = [
            'title' => 'Array Title',
            'author' => 'Array Author',
            'description' => 'Array Description',
            'publisher' => 'Array Publisher',
            'isbn' => ['0987654321', '9780987654321'],
            'ranks' => [['rank' => 2, 'list' => 'Non-Fiction']]
        ];

        $dto = BookDTO::fromArray($data);

        $this->assertEquals('Array Title', $dto->title);
        $this->assertEquals('Array Author', $dto->author);
        $this->assertEquals('Array Description', $dto->description);
        $this->assertEquals('Array Publisher', $dto->publisher);
        $this->assertEquals(['0987654321', '9780987654321'], $dto->isbn);
        $this->assertEquals([['rank' => 2, 'list' => 'Non-Fiction']], $dto->ranks);
    }

    public function test_can_handle_missing_data()
    {
        $dto = BookDTO::fromArray([
            'title' => 'Partial Data'
        ]);

        $this->assertEquals('Partial Data', $dto->title);
        $this->assertNull($dto->author);
        $this->assertNull($dto->description);
        $this->assertNull($dto->publisher);
        $this->assertEmpty($dto->isbn);
        $this->assertEmpty($dto->ranks);
    }

    public function test_can_convert_to_array()
    {
        $dto = new BookDTO(
            'Array Test',
            'Array Author',
            'Array Description',
            'Array Publisher',
            ['1122334455', '9781122334455'],
            [['rank' => 3, 'list' => 'Combined']]
        );

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('Array Test', $array['title']);
        $this->assertEquals('Array Author', $array['author']);
        $this->assertEquals('Array Description', $array['description']);
        $this->assertEquals('Array Publisher', $array['publisher']);
        $this->assertEquals(['1122334455', '9781122334455'], $array['isbn']);
        $this->assertEquals([['rank' => 3, 'list' => 'Combined']], $array['ranks']);
    }
} 