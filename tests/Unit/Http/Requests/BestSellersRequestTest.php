<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\BestSellersRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class BestSellersRequestTest extends TestCase
{
    use WithFaker;

    private function rules(): array
    {
        return (new BestSellersRequest())->rules();
    }

    public function test_author_validation()
    {
        $rules = $this->rules();

        $validator = Validator::make(['author' => 'Valid Author'], $rules);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['author' => ''], $rules);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['author' => str_repeat('a', 101)], $rules);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('author'));

        $validator = Validator::make(['author' => 123], $rules);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('author'));
    }

    public function test_isbn_validation()
    {
        $rules = $this->rules();

        $validator = Validator::make(['isbn' => ['0593803485']], $rules);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['isbn' => ['9780593803486']], $rules);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['isbn' => 'not-an-array'], $rules);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('isbn'));

        $validator = Validator::make(['isbn' => ['1234567890']], $rules);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('isbn.0'));

        $validator = Validator::make(['isbn' => [123456789]], $rules);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('isbn.0'));
    }

    public function test_title_validation()
    {
        $rules = $this->rules();

        $validator = Validator::make(['title' => 'Valid Title'], $rules);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['title' => ''], $rules);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['title' => str_repeat('a', 256)], $rules);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('title'));

        $validator = Validator::make(['title' => 123], $rules);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('title'));
    }

    public function test_offset_validation()
    {
        $rules = $this->rules();

        $validator = Validator::make(['offset' => 0], $rules);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['offset' => 10], $rules);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['offset' => 1000000], $rules);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['offset' => 1000001], $rules);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('offset'));

        $validator = Validator::make(['offset' => -1], $rules);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('offset'));

        $validator = Validator::make(['offset' => 'not-an-integer'], $rules);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('offset'));
    }

    public function test_prepare_for_validation()
    {
        $request = new BestSellersRequest();
        $request->replace(['isbn' => '1234567890']);

        $requestReflection = new \ReflectionClass($request);
        $method = $requestReflection->getMethod('prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);

        $this->assertEquals(['1234567890'], $request->input('isbn'));
    }
}
