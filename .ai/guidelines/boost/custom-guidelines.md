# Custom Guidelines

`CLAUDE.md` and `AGENTS.md` are fully managed by Laravel Boost and regenerated on every `php artisan boost:update`. Do not add custom rules directly to those files.

## Adding New Guidelines

Place custom guidelines in `.ai/guidelines/` at the project root. Boost merges these into all agent instruction files on every update.

- Create a subdirectory and a `core.md` file: `.ai/guidelines/{name}/core.md`
- The file content will be wrapped automatically in the appropriate `=== {name} ===` section
- Run `php artisan boost:update` after creating or modifying a guideline to apply it
