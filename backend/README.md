# Uysot Backend

This folder contains a Laravel-based backend scaffold for the Uysot real estate app.

Quick start (development):

1) Copy .env example and configure DB / keys:
   cp .env.example .env
   Set DB_*, ESKIZ_API_KEY, PAYME_*, CLICK_* etc.

2) Install PHP deps (in backend/ directory):
   composer install

3) Generate app key:
   php artisan key:generate

4) Create storage link:
   php artisan storage:link

5) Run migrations and seeders:
   php artisan migrate --seed

6) Serve:
   php artisan serve --port=8000

Notes:
- This scaffold is opinionated and minimal. For production, configure queue workers, HTTPS, secure env handling, and proper logging.
- Payme and Click services are sketches and must be implemented using official provider docs for production.
