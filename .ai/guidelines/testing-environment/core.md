# Test Environment Isolation

- Never run database-destructive verification commands against the default local environment.
- Any verification command that can migrate, refresh, seed, or otherwise modify the database must target the testing environment explicitly: use `php artisan ... --env=testing`.
- Use the dedicated test database from `.env.testing` for all Laravel test runs and verification commands. In this project, that database is `pickleball_test`.
- Do not run plain `php artisan migrate:fresh`, `php artisan migrate`, `php artisan db:seed`, or similar commands against the local environment unless the user explicitly asks for it.
- Prefer `php artisan test` for automated verification, and when additional database preparation is needed, pair it with testing environment commands such as `php artisan migrate:fresh --env=testing --no-interaction`.
- If you need to inspect database configuration before running verification, check the testing environment config first, for example `php artisan config:show database.connections.mysql --env=testing`.
