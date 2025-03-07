# NYT Best Sellers API Wrapper

A Laravel-based API wrapper for the New York Times Best Sellers History API.

## Features

- **API Versioning**: Supports versioned API endpoints (current: v1)
- **Internationalization**: Full i18n support for API responses and error messages
- **Caching**: Responses are cached to improve performance and reduce API calls
- **Parameter Validation**: Robust validation of all input parameters
- **Error Handling**: Comprehensive error handling with detailed error responses
- **Standardized Responses**: Consistent API response structure

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
