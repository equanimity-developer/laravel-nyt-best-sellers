<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\NYTApiException;
use Tests\TestCase;

class NYTApiExceptionTest extends TestCase
{
    public function test_exception_can_be_created_with_default_values()
    {
        $exception = new NYTApiException();
        
        $this->assertEquals('NYT API Error', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
        $this->assertEquals([], $exception->getErrors());
        $this->assertEquals([], $exception->getMeta());
    }
    
    public function test_exception_stores_error_data_and_context()
    {
        $message = 'Custom NYT API Error';
        $statusCode = 403;
        $errorData = ['response' => ['error' => 'Access denied']];
        $contextData = ['filters' => ['author' => 'Test Author']];
        
        $exception = new NYTApiException($message, $statusCode, $errorData, $contextData);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($statusCode, $exception->getCode());
        $this->assertEquals($errorData, $exception->getErrors());
        $this->assertEquals($contextData, $exception->getMeta());
    }
    
    public function test_exception_can_wrap_previous_exception()
    {
        $previous = new \Exception('Original error');
        $exception = new NYTApiException('Wrapped error', 500, [], [], $previous);
        
        $this->assertEquals('Wrapped error', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
} 