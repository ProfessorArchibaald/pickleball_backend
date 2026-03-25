# Laravel Migrations

- Always use Laravel's schema builder syntax (`Schema::create`, `$table->string()`, etc.) for migrations. Only fall back to raw SQL via `DB::statement()` when the required operation is impossible with the schema builder (e.g. advanced index types, partial indexes, custom constraints).
- Every new table must have a comment describing its purpose: `$table->comment('...')`.
- Every new column must have a comment describing its purpose: `$table->string('name')->comment('...')`.
- Every new table must include `$table->timestamps()` to add `created_at` and `updated_at` columns. Never use `$table->timestamp('created_at')` or similar manual alternatives.
