# Neriah ERP V1.0

Sistema de gestion monolitico en Laravel 12 para la fabricacion y comercializacion de jabones artesanales Neriah.

## Stack

- Laravel 12
- PHP 8.2+ en el entorno actual
- MySQL 8
- Blade, Livewire y Alpine.js para la UI
- Laravel Sanctum
- Spatie Permission
- Spatie Activity Log
- Spatie Backup

## Arquitectura

El codigo de negocio vive en `app/Domains`, organizado por dominios:

- Security
- People
- Customers
- Products
- Recipes
- Inventory
- Purchases
- Production
- Sales
- Commissions
- Payments
- Assets
- Finance
- SocialFund
- Dashboard

El centro operativo es el Kardex: el stock nunca debe modificarse manualmente; todo cambio de inventario debe pasar por movimientos en `inventory_movements`.

## Instalacion local

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
```

Usuario demo:

- Email: `admin@neriah.test`
- Password: `password`

## Comandos utiles

```bash
php artisan test
php artisan migrate:status
php artisan permission:show
php artisan backup:run
```

## Ruta de desarrollo

La hoja de ruta principal esta en [docs/DEVELOPMENT_ROADMAP.md](docs/DEVELOPMENT_ROADMAP.md).
