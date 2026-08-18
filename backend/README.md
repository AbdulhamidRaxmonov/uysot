# Backend (Laravel) scaffold

This directory contains a small Laravel API scaffold to manage listings and a minimal admin blade view.

Files added:
- routes/api.php - API routes for listings and auth (skeleton)
- app/Models/Listing.php - Eloquent model
- app/Http/Controllers/ListingController.php - basic CRUD controller
- database/migrations/2026_08_18_000000_create_listings_table.php - migration skeleton
- resources/views/admin/dashboard.blade.php - very small admin dashboard UI

SMS integration (eskiz.uz) sketch
- Eskiz provides an HTTP API for sending SMS. Store API key in .env as ESKIZ_API_KEY.
- Example flow: when user creates a listing or verifies phone, call Eskiz send endpoint with message and phone number.
- Use Laravel Http::post() or Guzzle to call Eskiz API. Respect rate limits and handle delivery callbacks if provided.

Payment integration sketches
- Payme (Payme.uz)
  - Use Payme's merchant API to create payment session for bookings / daily rentals.
  - Store merchant credentials in .env (PAYME_MERCHANT_ID, PAYME_SECRET).
  - Create endpoints to create transactions and verify callbacks from Payme.
- Click (click.uz)
  - Similar approach: create order, provide redirect or display payment params, verify signature in callback.

Admin panel
- This scaffold uses a simple blade view; recommend integrating Laravel Jetstream or Nova for production admin features.

Notes
- This is a scaffold and does not include composer/vendor files or node modules. Run composer install and php artisan migrate after creating .env and app key.
