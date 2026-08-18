# Payments - Mock testing

This branch adds mock testing endpoints so you can simulate provider callbacks locally without real merchant credentials.

New endpoints (development only):
- POST /api/payments/mock/payme/{id} — simulate Payme callback for transaction {id}
- POST /api/payments/mock/click/{id} — simulate Click callback for transaction {id}

Usage:
1) Create a transaction via the normal create endpoints (auth required):
   POST /api/payments/payme/create { listing_id, amount, currency }
   -> returns transaction.id in response

2) Simulate provider callback (local):
   curl -X POST http://127.0.0.1:8000/api/payments/mock/payme/123
   curl -X POST http://127.0.0.1:8000/api/payments/mock/click/123

3) Fetch transaction status:
   GET /api/transactions/{id}

Important: These mock endpoints are intended ONLY for local development and testing. Do NOT enable them in production.
