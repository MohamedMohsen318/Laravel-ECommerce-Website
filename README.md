# Ecommerce

Laravel ecommerce project with user authentication, admin authentication, categories, translations, media, and Blade views.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Checks

```bash
php artisan test
php artisan route:list
```
