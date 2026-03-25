# Filament Admin Panel Conventions

## Delete and Edit Actions

These rules apply to all Filament resource views (`List`, `Create`, `Edit`, `View`):

- Do not add delete buttons or edit buttons to `ListResource` (index) or `ViewResource` (show/detail) pages.
- Always place both the delete action and the edit action inside `EditResource` (edit/update) view only.
- This keeps destructive and mutating actions one step away from browsing and reduces accidental changes or deletions.

## Post-Create Redirect

- After creating a new record, always redirect to the list page (`index`) by overriding `getRedirectUrl()` in the `CreateRecord` page class:

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
```
