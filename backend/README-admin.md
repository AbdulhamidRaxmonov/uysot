# Admin panel (Filament) - scaffold

This folder contains a scaffold for adding an admin panel using Filament for Laravel. It provides resource stubs for Listings and Transactions so you can quickly enable admin CRUD.

What I added in this branch:
- app/Filament/Resources/ListingResource.php (resource stub)
- app/Filament/Resources/ListingResource/Pages/ListListings.php (page stub)
- app/Filament/Resources/ListingResource/Pages/CreateListing.php
- app/Filament/Resources/ListingResource/Pages/EditListing.php
- app/Filament/Resources/TransactionResource.php (resource stub)
- backend/README-admin.md with step-by-step instructions to install and enable Filament

Notes and next steps to enable admin panel locally:
1) Require Filament packages (run in backend directory):
   composer require filament/filament

2) Publish and migrate (Filament will create its own tables for admin users/roles):
   php artisan vendor:publish --tag=filament-config
   php artisan migrate

3) Create an admin user (example using tinker):
   php artisan tinker
   >>> \App\Models\User::create(['name' => 'Admin', 'phone' => '+998900000000']);
   // Then assign filament admin role if using Filament user provider setup

4) Visit /admin after setup and login with your admin user. Filament configuration may require customizing the user provider.

If you prefer not to use Filament, I can implement a lightweight Blade-based admin (already have a simple dashboard in backend/resources/views/admin/dashboard.blade.php). Tell me which approach you prefer and I will continue.
