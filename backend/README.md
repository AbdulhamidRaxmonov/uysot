# Payments - full integration notes

This branch (feature/payments-full) contains improved Payme and Click service implementations. They attempt to call the real provider endpoints when API base URLs are configured; otherwise they return simulated fallback responses so you can test end-to-end flows locally.

Environment variables to set in backend/.env:
- PAYME_MERCHANT_ID=
- PAYME_SECRET=
- PAYME_API_URL=https://pay.payme.uz
- PAYME_CALLBACK_URL=https://your.site/api/payments/payme/callback

- CLICK_MERCHANT_ID=
- CLICK_SERVICE_ID=
- CLICK_SECRET=
- CLICK_API_URL=https://api.click.uz

Usage
- Create a Transaction via POST /api/payments/payme/create or /api/payments/click/create (auth required)
- The controller will call the corresponding service and return checkout_url
- After payment, providers will call your callback endpoints which are handled in PaymentController; the service verifyCallback() is used to validate signatures

Security
- The verify implementations are conservative but may need adjustment to match the exact provider contract. Please provide sandbox callback examples or merchant docs to finalize signature computation.

Testing
- You can simulate callbacks by POSTing sample payloads to /api/payments/payme/callback and /api/payments/click/callback. The services will try to verify signatures; for simulated flows the controller will still mark transactions as paid if status indicates success.
