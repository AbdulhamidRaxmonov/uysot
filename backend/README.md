# Payments integration (Payme & Click)

This update adds a payments skeleton to the backend:

New files:
- backend/app/Models/Transaction.php - Transaction model
- backend/database/migrations/2026_08_18_000003_create_transactions_table.php - migration
- backend/app/Http/Controllers/PaymentController.php - endpoints to create payments and receive callbacks
- backend/app/Services/PaymeService.php - sketch service for Payme interactions
- backend/app/Services/ClickService.php - sketch service for Click interactions
- routes updated to include /payments/* endpoints

Environment variables (add to .env):
- PAYME_MERCHANT_ID=
- PAYME_SECRET=
- PAYME_BASE_URL= (optional, sandbox/checkout URL)
- CLICK_MERCHANT_ID=
- CLICK_SECRET=
- CLICK_BASE_URL= (optional)
- FRONTEND_RETURN_URL= (where the provider should redirect after payment)

Important notes
- The Payme and Click service implementations are sketches. Each payment provider has specific API requirements (signatures, currencies, amount units, callback formats). Use provider docs to fully implement createPayment() and verifyCallback().
- For testing, you can return the simulated checkout_url from the create endpoints and manually simulate callbacks by POSTing to /api/payments/{provider}/callback with expected payload.
- Protect callback endpoints by verifying signatures and IP ranges where applicable.
