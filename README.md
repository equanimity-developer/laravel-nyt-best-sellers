# It's a simple Laravel 12 fresh project with Docker

Clone repository and...

Install Sail
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

- `cp .env.example .env` - copy .env file
- `./vendor/bin/sail build` - build the project 
- `./vendor/bin/sail up` - run application 
- `./vendor/bin/sail artisan key:generate` - generate application key  
- `./vendor/bin/sail artisan migrate` - run db migration

That all, you can access the application on http://localhost/
