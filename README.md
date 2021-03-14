## About
* FeedBank API is a standard *Laravel* application written in PHP
* It exposes a RESTful API, which is consumed by the FeedBank UI
* It communicates with the Analytics component to analyze resposnes received from users.
* Setup should therefore be routine for those familiar with the *Laravel* framework.

## Development Prerequisites
A `docker-compose` configuration is included, which is the recommended way to run the API locally. This is based on [Laravel Sail](https://laravel.com/docs/8.x/sail). To setup the API:

* Copy `.env.example` to `.env`
* Configure a standard Laravel database connection in `.env`
* Configure a standard Laravel mailer connection in `.env` ([mailtrap](https://mailtrap.io/) is recommended for local development)
* Set the following additional environment variables in `.env`:
  | Variable Key                      | Description                                                      |
  |-----------------------------------|------------------------------------------------------------------|
  | LARAVEL_WEBSOCKETS_SSL_LOCAL_PK   | Path to an SSL .key file for the API domain (can be self-signed) |
  | LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT | Path to an SSL .crt file for the API domain (can be self-signed) |
  | RECAPTCHA_SITE_KEY                | Google Invisible Recaptcha V2 site key                           |
  | RECAPTCHA_SECRET_KEY              | Google Invisible Recaptcha V2 secret key                         |
* Optionally, if you want to use real analytics data rather than mocked data, set the following environment variables in `.env`:
  | Variable Key             | Description                                      |
  |--------------------------|--------------------------------------------------|
  | CS261_ANALYTICS_MOCK     | Set to `false` to disable mock data generation   |
  | CS261_ANALYTICS_ENDPOINT | Set to the host of a FeedBank Analytics instance |
* Create a symbol link to the UI component:
  `ln -s ../path-to-ui-repository ./ui`
* Install PHP *composer* dependencies:
  ```
  docker run --rm \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php80-composer:latest \
    composer install
  ```

## Starting the API
* Run `./vendor/bin/sail up -d` to start the `docker-compose` services
* Run `./vendor/bin/sail artisan websockets:serve` to start the websocket server
* Run `./vendor/bin/sail artisan migrate:fresh --seed` to wipe, migrate and seed the database.

This will make the UI available at `localhost`, and the API available at `localhost:8080`

## Running tests
* Run `./vendor/bin/sail artisan test` to run the test suite

## Stopping the API
* Run `./vendor/bin/sail down`

## Application structure
The directory structure is identical to that of a typical *Laravel 8.0* installation, with the following files/folders being most noticable:

* `app/Events` - events that can be dispatched in the background
* `app/Http/Controllers` - handler logic for endpoints
* `app/Http/Resources` - serialize model data for responses
* `app/Listeners` - logic that reacts to specific events
* `app/Mail` - mail handling logic
* `app/Models` - data models for the application
* `database/migrations` - schema migrations for the database
* `database/seeders` - seed data for local development
* `routes/api.php` - API routes that are accessible to requesters
* `tests` - feature tests for the application
* `docker-compose.yml` - docker service configuration
* `swagger.yml` - Swagger/OpenAPI documentation for available endpoints

## Further reading

For a detailed explanation on how things, refer to the [Laravel 8.x Docs](https://laravel.com/docs/8.x)