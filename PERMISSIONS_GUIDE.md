# IL Mission - Roles & Permissions System

## Overview

This system uses Spatie Laravel Permission package to manage roles and permissions. The SuperAdmin role has all permissions by default and doesn't need explicit permission assignment.

## Roles

### 1. SuperAdmin

-   **Description**: Full system access
-   **Permissions**: All permissions automatically granted
-   **Use Case**: System administrators

### 2. Approver

-   **Description**: Can approve/reject applications and manage finances
-   **Key Permissions**:
    -   `students.view`, `students.edit`, `students.approve`, `students.reject`
    -   `student_applications.*`
    -   `volunteer_applications.*`
    -   `enquiries.view`
    -   `newsletter.view`

### 3. Inspector

-   **Description**: Can view and comment on applications
-   **Key Permissions**:
    -   `students.view`, `students.documents_manage`
    -   `student_applications.view`, `student_applications.comment`
    -   `volunteer_applications.view`
    -   `enquiries.view`

### 4. Editor

-   **Description**: Can manage content (blogs, news, events, CMS)
-   **Key Permissions**:
    -   `cms.*`
    -   `blogs.*`
    -   `news.*`
    -   `events.*`
    -   `faqs.*`
    -   `banners.*`
    -   `media.*`

### 5. Viewer

-   **Description**: Read-only access to most modules
-   **Key Permissions**:
    -   `dashboard.view`
    -   `students.view`
    -   `student_applications.view`
    -   `volunteer_applications.view`
    -   `enquiries.view`
    -   `newsletter.view`
    -   `blogs.view`, `news.view`, `events.view`, `faqs.view`, `banners.view`

## Permission Structure

### Module-based Permissions

Each module has standard CRUD permissions:

#### Dashboard Module

-   `dashboard.view` - View dashboard
-   `dashboard.stats` - View statistics

#### User Management Module

-   `users.view` - View users list
-   `users.create` - Create new users
-   `users.edit` - Edit existing users
-   `users.delete` - Delete users
-   `users.export` - Export users data

#### Role & Permission Management Module

-   `roles.view` - View roles
-   `roles.create` - Create roles
-   `roles.edit` - Edit roles
-   `roles.delete` - Delete roles
-   `permissions.manage` - Manage permissions

#### Student Management Module

-   `students.view` - View students
-   `students.create` - Create students
-   `students.edit` - Edit students
-   `students.delete` - Delete students
-   `students.approve` - Approve students
-   `students.reject` - Reject students
-   `students.finance_manage` - Manage student finances
-   `students.documents_manage` - Manage student documents
-   `students.export` - Export student data
-   `students.activate` - Activate students
-   `students.deactivate` - Deactivate students

#### Student Applications Module

-   `student_applications.view` - View applications
-   `student_applications.edit` - Edit applications
-   `student_applications.delete` - Delete applications
-   `student_applications.approve` - Approve applications
-   `student_applications.reject` - Reject applications
-   `student_applications.finance_manage` - Manage application finances
-   `student_applications.comment` - Add comments
-   `student_applications.export` - Export applications

#### Content Management Modules

-   **CMS**: `cms.view`, `cms.create`, `cms.edit`, `cms.delete`, `cms.publish`
-   **Blogs**: `blogs.view`, `blogs.create`, `blogs.edit`, `blogs.delete`, `blogs.publish`
-   **News**: `news.view`, `news.create`, `news.edit`, `news.delete`, `news.publish`
-   **Events**: `events.view`, `events.create`, `events.edit`, `events.delete`, `events.publish`
-   **FAQs**: `faqs.view`, `faqs.create`, `faqs.edit`, `faqs.delete`, `faqs.publish`

#### Other Modules

-   **Banners**: `banners.*`
-   **Menus**: `menus.*`
-   **Custom Fields**: `custom_fields.*`
-   **Settings**: `settings.*`
-   **Activities**: `activities.*`
-   **Enquiries**: `enquiries.*`
-   **Volunteer Applications**: `volunteer_applications.*`
-   **Newsletter**: `newsletter.*`
-   **Media**: `media.*`

## Implementation

### 1. Route Protection

Routes are protected using middleware:

```php
Route::get('/users', [UserController::class, 'index'])
    ->middleware('permission:users.view');
```

### 2. Controller Protection

Use permission checks in controllers:

```php
public function index()
{
    $this->authorize('users.view');
    // Controller logic
}
```

### 3. View Protection

Use Blade directives in views:

```blade
@hasPermission('users.create')
    <button class="btn btn-primary">Add User</button>
@endHasPermission

@hasAnyPermission(['users.edit', 'users.delete'])
    <div class="actions">
        @hasPermission('users.edit')
            <button class="btn btn-warning">Edit</button>
        @endHasPermission
        @hasPermission('users.delete')
            <button class="btn btn-danger">Delete</button>
        @endHasPermission
    </div>
@endHasAnyPermission
```

### 4. Helper Functions

Use helper functions in PHP:

```php
if (hasPermission('users.create')) {
    // Show create button
}

if (hasAnyPermission(['users.edit', 'users.delete'])) {
    // Show action buttons
}
```

## Blade Directives

### Single Permission

```blade
@hasPermission('permission.name')
    <!-- Content for users with this permission -->
@endHasPermission
```

### Multiple Permissions (Any)

```blade
@hasAnyPermission(['permission1', 'permission2'])
    <!-- Content for users with any of these permissions -->
@endHasAnyPermission
```

### Role Check

```blade
@hasRole('SuperAdmin')
    <!-- Content for SuperAdmin only -->
@endHasRole
```

### Multiple Roles (Any)

```blade
@hasAnyRole(['SuperAdmin', 'Approver'])
    <!-- Content for SuperAdmin or Approver -->
@endHasAnyRole
```

## Database Seeding

### Run Permission Seeder

```bash
php artisan db:seed --class=PermissionSeeder
```

### Run All Seeders

```bash
php artisan db:seed
```

This will create:

-   All 117 permissions
-   5 roles (SuperAdmin, Approver, Inspector, Editor, Viewer)
-   Sample users for each role

## Testing Users

After seeding, you can login with:

1. **SuperAdmin**: admin@ilm.com / password
2. **Approver**: approver@ilm.com / password
3. **Inspector**: inspector@ilm.com / password

## Adding New Permissions

1. Add permission to `PermissionSeeder.php`
2. Run seeder: `php artisan db:seed --class=PermissionSeeder`
3. Assign to appropriate roles
4. Add middleware to routes
5. Add Blade directives to views

## Best Practices

1. **Always check permissions** before showing sensitive actions
2. **Use specific permissions** rather than broad ones
3. **Test with different roles** to ensure proper access control
4. **Document permission requirements** for each feature
5. **Regularly audit permissions** to ensure they're still relevant

## Troubleshooting

### Permission Not Working

1. Check if permission exists in database
2. Verify user has the permission assigned
3. Check middleware is applied correctly
4. Clear cache: `php artisan cache:clear`

### SuperAdmin Access Issues

-   SuperAdmin should have all permissions automatically
-   If not working, check role assignment
-   Verify SuperAdmin role exists and is assigned to user









