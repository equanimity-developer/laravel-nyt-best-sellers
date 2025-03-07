[![codecov](https://codecov.io/gh/equanimity-developer/laravel-nyt-best-sellers/branch/main/graph/badge.svg)](https://codecov.io/gh/equanimity-developer/laravel-nyt-best-sellers)

# NYT Best Sellers API Wrapper

A Laravel-based API wrapper for the New York Times Best Sellers History API.

## Features

- **API Versioning**: Supports versioned API endpoints (current: v1)
- **Internationalization**: Full i18n support for API responses and error messages
- **Caching**: Responses are cached to improve performance and reduce API calls
- **Parameter Validation**: Robust validation of all input parameters
- **Error Handling**: Comprehensive error handling with detailed error responses
- **Standardized Responses**: Consistent API response structure
- **OpenAPI Documentation**: Complete API specification using OpenAPI 3.0
- **Data Transfer Objects**: Type-safe domain objects for better maintainability
- **API Resources**: Clean transformation layer between domain and response

## Architecture

This API wrapper uses a clean, layered architecture:

1. **Controllers**: Handle HTTP requests and responses
2. **Form Requests**: Validate incoming parameters
3. **Services**: Interact with the NYT API and transform responses to DTOs
4. **DTOs** (Data Transfer Objects): Type-safe domain objects representing books
5. **API Resources**: Transform DTOs into standardized API responses

## API Documentation

The API is fully documented using the OpenAPI 3.0 specification. You can find the documentation in the `openapi.yaml` file at the root of the project.

### Using the OpenAPI Documentation

You can use the OpenAPI documentation in several ways:

1. **View with Swagger UI**: Import the `openapi.yaml` file into [Swagger Editor](https://editor.swagger.io/) for an interactive documentation experience.
2. **Generate Client Libraries**: Use tools like [OpenAPI Generator](https://openapi-generator.tech/) to generate client libraries for your preferred language.
3. **Validate Requests/Responses**: Use the specification to validate that your API implementation conforms to the documented contract.

The OpenAPI specification includes:
- Detailed endpoint descriptions
- Request parameters and their constraints
- Response structures and HTTP status codes
- Example requests and responses
- Schema definitions for all data models

## API Endpoints

### Get Best Sellers List

```
GET /api/v1/best-sellers
```

#### Query Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| author | string | Filter by author name |
| isbn[] | string | Filter by ISBN (supports ISBN-10 and ISBN-13) |
| title | string | Filter by book title |
| offset | integer | Pagination offset (0-1,000,000) |

### Search Best Sellers (for large filter sets)

```
POST /api/v1/best-sellers/search
```

This endpoint provides identical functionality to the GET endpoint but accepts parameters in the request body instead of as query parameters. Use this endpoint when dealing with a large number of filters (especially multiple ISBN values) that might exceed URL length limits.

#### Request Body (JSON)

```json
{
  "author": "Stephen King",
  "isbn": ["0593803485", "9780593803486"],
  "title": "The Shining",
  "offset": 0
}
```

#### Response Format

```json
{
  "status": "success",
  "message": "Best sellers data retrieved successfully",
  "data": {
    "best_sellers": [
      {
        "title": "Book Title",
        "author": "Author Name",
        "description": "Book description",
        "publisher": "Publisher",
        "isbn": ["1234567890", "1234567890123"],
        "ranks": []
      }
    ],
    "count": 1
  },
  "meta": {
    "api_version": "v1",
    "filters": {
      "author": "Author Name"
    }
  }
}
```

## Error Handling

The API returns standardized error responses:

```json
{
  "status": "error",
  "message": "Error message",
  "errors": {
    "field": "Validation error message"
  },
  "meta": {
    "api_version": "v1"
  }
}
```

## Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy environment file: `cp .env.example .env`
4. Generate application key: `php artisan key:generate`
5. Configure NYT API key in `.env`:
   ```
   NYT_API_KEY=your_api_key
   NYT_API_BASE_URL=https://api.nytimes.com/svc/books/v3
   ```

## Testing

Tests can be run without an internet connection or valid NYT API credentials:

```bash
php artisan test
```

## Breaking Changes Strategy

1. **API Versioning**: All breaking changes are introduced in new API versions
2. **Deprecation Notices**: Deprecated endpoints display notices before removal
3. **Backwards Compatibility**: Maintained for the lifecycle of each API version
