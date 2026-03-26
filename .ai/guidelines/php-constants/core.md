# Typed Constants

- Every new constant must have an explicit type declaration (PHP 8.3+ typed constants).

```php
// Correct
public const string ADMIN = 'Admin';
public const int MAX_RETRIES = 3;

// Incorrect — missing type
public const ADMIN = 'Admin';
```
