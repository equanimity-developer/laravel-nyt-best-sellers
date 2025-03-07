<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\NYTApiException;
use App\Services\NYTBestSellersService;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class ResponseFailedHandlingTest extends TestCase
{
    public function test_response_failed_throws_nyt_api_exception()
    {
        $statusCode = 429;
        $errorJson = ['error' => 'Too Many Requests'];
        $filters = ['author' => 'Test Author'];

        Log::spy();

        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('failed')->andReturn(true);
        $mockResponse->shouldReceive('status')->andReturn($statusCode);
        $mockResponse->shouldReceive('body')->andReturn(json_encode($errorJson));
        $mockResponse->shouldReceive('json')->andReturn($errorJson);

        $testService = new class($mockResponse) extends NYTBestSellersService {
            private Response $mockResponse;

            public function __construct(Response $mockResponse) {
                parent::__construct();
                $this->mockResponse = $mockResponse;
                $this->apiKey = 'test-key';
                $this->endpoint = '/test-endpoint';
            }

            protected function client(): PendingRequest {
                $pendingRequest = Mockery::mock(PendingRequest::class);
                $pendingRequest->shouldReceive('get')->withAnyArgs()->andReturn($this->mockResponse);
                return $pendingRequest;
            }

            public function testMakeRequest(array $filters): array {
                return $this->makeRequest($filters);
            }
        };

        try {
            $testService->testMakeRequest($filters);
            $this->fail('Expected NYTApiException was not thrown');
        } catch (NYTApiException $e) {
            $this->assertEquals($statusCode, $e->getCode());
            $this->assertEquals(['response' => $errorJson], $e->getErrors());
            $this->assertEquals(['filters' => $filters], $e->getMeta());
            $this->assertEquals(__('api.nyt.failed', ['status' => $statusCode]), $e->getMessage());
        }
    }
}
