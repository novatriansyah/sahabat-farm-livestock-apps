# DEPLOYMENT RUNBOOK — RELEASE 0 CLOSEOUT

1. Checkout `development` branch.
2. Run `composer install --no-interaction`.
3. Run `npm ci && npm run build`.
4. Run `php artisan test`.
