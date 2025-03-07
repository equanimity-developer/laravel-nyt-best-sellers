<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\DTOs\BookDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property BookDTO $resource
 */
class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->resource->title,
            'author' => $this->resource->author,
            'description' => $this->resource->description,
            'publisher' => $this->resource->publisher,
            'isbn' => $this->resource->isbn,
            'ranks' => $this->resource->ranks,
        ];
    }
}
