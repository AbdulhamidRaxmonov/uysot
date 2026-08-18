# Backend - listings & upload

This update adds image upload support for listings, thumbnail creation, and a sample seeder.

Important steps after pulling the branch:

1) Install Intervention Image for thumbnailing:
   composer require intervention/image

2) Add the ServiceProvider (Laravel 9+ auto-discovers the package) or configure if needed.

3) Create public storage symlink (if not already):
   php artisan storage:link

4) Run migrations and seeders:
   php artisan migrate
   php artisan db:seed

5) Notes on usage:
   - Upload endpoint: POST /api/listings/{id}/photos (multipart form-data, field name: file). Requires Sanctum auth token.
   - Stored files will be in storage/app/public/listings/{id}/ and accessible via /storage/listings/{id}/...

6) Keep ESKIZ_API_KEY and other ENV values in .env
