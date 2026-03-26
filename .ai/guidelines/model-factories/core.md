# Model Factories

- Every model must have a corresponding factory, including dictionary models.
- Place dictionary model factories in `database/factories/Dictionary/` to mirror `app/Models/Dictionary/`.
- Every factory must declare the `$model` property explicitly.
- The `@use` tag on the model must reference the factory class: `/** @use HasFactory<UserRoleFactory> */`.
