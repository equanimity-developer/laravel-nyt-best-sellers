<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Resources;

use App\DTOs\BookDTO;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Tests\TestCase;

class BookResourceTest extends TestCase
{
    public function test_resource_transforms_dto_correctly()
    {
        $dto = new BookDTO(
            'Resource Title',
            'Resource Author',
            'Resource Description',
            'Resource Publisher',
            ['9876543210', '9789876543210'],
            [['rank' => 5, 'list' => 'Test List']]
        );

        $resource = new BookResource($dto);
        $result = $resource->toArray(new Request());

        $this->assertEquals('Resource Title', $result['title']);
        $this->assertEquals('Resource Author', $result['author']);
        $this->assertEquals('Resource Description', $result['description']);
        $this->assertEquals('Resource Publisher', $result['publisher']);
        $this->assertEquals(['9876543210', '9789876543210'], $result['isbn']);
        $this->assertEquals([['rank' => 5, 'list' => 'Test List']], $result['ranks']);
    }

    public function test_resource_collection_works_correctly()
    {
        $dtos = [
            new BookDTO('Title 1', 'Author 1', 'Description 1', 'Publisher 1', ['1111111111'], []),
            new BookDTO('Title 2', 'Author 2', 'Description 2', 'Publisher 2', ['2222222222'], []),
        ];

        $collection = BookResource::collection(collect($dtos));
        $result = $collection->response()->getData(true);

        $this->assertCount(2, $result['data']);
        $this->assertEquals('Title 1', $result['data'][0]['title']);
        $this->assertEquals('Author 1', $result['data'][0]['author']);
        $this->assertEquals('Title 2', $result['data'][1]['title']);
        $this->assertEquals('Author 2', $result['data'][1]['author']);
    }

    public function test_resource_handles_null_values()
    {
        $dto = new BookDTO(
            null,
            null,
            null,
            null,
            [],
            []
        );

        $resource = new BookResource($dto);
        $result = $resource->toArray(new Request());

        $this->assertNull($result['title']);
        $this->assertNull($result['author']);
        $this->assertNull($result['description']);
        $this->assertNull($result['publisher']);
        $this->assertEmpty($result['isbn']);
        $this->assertEmpty($result['ranks']);
    }
} 