<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class BookDTO
{
    public function __construct(
        public ?string $title,
        public ?string $author,
        public ?string $description,
        public ?string $publisher,
        public array $isbn,
        public array $ranks = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? null,
            $data['author'] ?? null,
            $data['description'] ?? null,
            $data['publisher'] ?? null,
            $data['isbn'] ?? [],
            $data['ranks'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
            'publisher' => $this->publisher,
            'isbn' => $this->isbn,
            'ranks' => $this->ranks,
        ];
    }
}
