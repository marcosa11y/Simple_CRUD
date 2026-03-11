<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## Database setup

This sample application uses SQLite by default (see `DB_CONNECTION` in
`.env.example`).

If you copy the example env file without setting `DB_DATABASE` you may
encounter a 500 error similar to:

> Database file at path [laravel] does not exist. Ensure this is an absolute
> path to the database.

To avoid this make sure the variable points to a real file, e.g.

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=${PWD}/database/database.sqlite
```

or create a blank sqlite file in `database/` and set the name accordingly.

The application now contains logic in `AppServiceProvider` that will
convert a bare filename into an absolute path and even create the file on
startup, so a mis‑configured env will no longer crash the API, but it's
still a good idea to configure the database correctly and run
`
php artisan migrate
` before exercising the endpoints.

## API Endpoints

This application exposes a JSON API under `/api` with the following routes:

- `GET /api/products` – list all products (public)
- `GET /api/products/{id}` – view a single product (public)
- `GET /api/categories` – list categories with attached products (public)

The following routes require authentication and are typically used by the React admin UI:

- `POST /api/products` – create product (admin)
- `PUT /api/products/{id}` – update product (admin)
- `DELETE /api/products/{id}` – remove product (admin)
- `POST /api/categories` – create category (admin)
- `PUT /api/categories/{id}` – update category (admin)
- `DELETE /api/categories/{id}` – delete category (admin)

### Authentication

Authentication is handled via [Laravel Sanctum](https://laravel.com/docs/sanctum).
Clients should obtain a personal access token by registering or logging in:

- `POST /api/register` – create a new user. Body: `name`, `email`, `password`.
- `POST /api/login` – authenticate. Body: `email`, `password`.
- `POST /api/logout` – revoke current token (requires `Authorization: Bearer <token>`).
- `GET /api/user` – fetch the authenticated user's details.

After logging in, include the token in the `Authorization` header of
subsequent requests. The SPA frontend demonstrates this behavior.

Protected actions (cart, checkout, product management) are wrapped in
`auth:sanctum` middleware.

## React SPA

A simple React application resides in the `frontend` directory.  It
consumes the API endpoints above and demonstrates a basic e‑commerce
workflow.  Features include:

- Browsing/searching products and viewing categories (public).
- Shopping cart and checkout for authenticated users.
- **Admin dashboard** that appears when a logged-in user has the
  `role` field set to `admin`.

The admin dashboard allows full CRUD operations on products and
categories; this is the interface created for the frontend portion of
this exercise.  To test the admin views, seed the database and login as
`admin@example.com` / `password`.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
