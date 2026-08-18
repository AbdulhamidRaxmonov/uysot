# Backend (Laravel) scaffold - Auth & SMS additions

This update adds a simple phone-based authentication flow using Laravel Sanctum and an Eskiz SMS service wrapper.

New environment variables (add to your backend/.env):
- ESKIZ_API_KEY=your_eskiz_api_key

Sanctum
- Install and configure Laravel Sanctum following the official docs. This scaffold expects Sanctum to be installed and configured (middleware and provider).

API endpoints (examples):
- POST /api/auth/send-code { phone }
  - Generates a 4-digit code, caches it for 5 minutes and sends via Eskiz.
- POST /api/auth/verify-code { phone, code }
  - Verifies code and returns a personal access token.
- POST /api/auth/logout (auth:sanctum)
  - Revokes current token.

Notes
- This is a minimal flow for development and testing. For production, add rate limiting, better phone normalization, resend limits, and logging.
