# Eloquent Model Conventions

- Every model must declare a `$table` property with the table name, even when it matches Laravel's default snake_case/plural convention. This makes the mapping explicit and prevents silent breakage if the class is renamed.

```php
// Correct
class User extends Model
{
    protected $table = 'users';
}

// Incorrect — implicit table name
class User extends Model
{
}
```
