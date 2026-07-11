# Creative ERP

# Sprint 06 - Users Management

Version: 1.0

Status: Ready for Development

---

# Sprint Goal

Build a complete enterprise User Management module.

The module must integrate with:

- Authentication
- Companies
- Branches
- Departments
- Roles & Permissions

Users are system accounts that access the ERP based on assigned roles and permissions.

---

# Read Before Coding

Read:

docs/00_PROJECT_RULES.md

docs/01_PROJECT_VISION.md

docs/02_BUSINESS_ANALYSIS.md

docs/03_AUTHENTICATION.md

docs/04_PROJECT_SETUP.md

docs/AI_CONTEXT.md

docs/prompts/SPRINT_05.md

---

# Current Status

Completed

- Authentication
- Dashboard
- Companies
- Branches
- Departments
- Roles & Permissions

Current Sprint

Users

Next Sprint

Clients

---

# Important Rules

Do not modify completed modules.

Reuse layouts.

Reuse Blade components.

Reuse authentication.

Reuse roles and permissions.

Use Laravel 12 best practices.

Controllers must remain thin.

Business logic belongs in UserService.

Validation belongs in Form Requests.

Authorization uses Policies and Spatie Permissions.

---

# Module Purpose

Users are authenticated ERP accounts.

Every User belongs to:

- One Company
- One Branch
- One Department

Every User can have one or multiple Roles.

---

# Features

- User List
- Create User
- Edit User
- View User
- Activate / Deactivate
- Soft Delete
- Restore
- Reset Password
- Change Password
- Assign Roles
- Search
- Filters
- Pagination
- Profile Photo Upload

---

# Database

Use the existing users table.

Extend it if needed with:

- company_id
- branch_id
- department_id
- avatar
- phone
- job_title
- status
- last_login_at

Use foreign keys where appropriate.

---

# Relationships

User belongsTo Company

User belongsTo Branch

User belongsTo Department

User belongsToMany Roles (Spatie)

---

# Business Rules

- Email must be unique.
- Username (if implemented) must be unique.
- User must belong to an existing Company.
- Branch must belong to the selected Company.
- Department must belong to the selected Branch.
- Users cannot delete themselves.
- Super Admin cannot be deleted by non-Super Admins.
- Reset Password sends an email if mail is configured.

---

# Validation

Company

Required

Branch

Required

Department

Required

First Name

Required

Last Name

Required

Email

Required

Unique

Phone

Nullable

Password

Required on Create

Confirmed

Minimum 8 characters

Roles

Required

Must exist

---

# Permissions

user.view

user.create

user.update

user.delete

user.restore

user.activate

user.deactivate

user.reset-password

---

# UI Pages

Users List

Create User

Edit User

View User

Profile

---

# User Form

Fields

- Company
- Branch
- Department
- First Name
- Last Name
- Email
- Phone
- Job Title
- Profile Photo
- Roles
- Password
- Confirm Password
- Status

Use dependent dropdowns:

Company → Branch → Department

---

# Table

Columns

Profile Photo

Full Name

Email

Company

Branch

Department

Roles

Status

Last Login

Actions

---

# Search

Search by

- Name
- Email
- Phone
- Company
- Branch
- Department

---

# Filters

Company

Branch

Department

Role

Status

Created Date

---

# Service

UserService

Responsibilities

- Create User
- Update User
- Delete User
- Restore User
- Activate User
- Deactivate User
- Assign Roles
- Sync Roles
- Reset Password
- Upload Profile Photo

---

# Notifications

Prepare notifications for:

- Welcome Email
- Password Reset
- Account Activated
- Account Deactivated

Do not implement SMS or WhatsApp yet.

---

# Routes

Use authenticated admin routes.

Protect with Policies and Permissions.

---

# Seeder

Generate sample users for testing.

Assign default roles.

---

# Tests

Generate Feature Tests for

- Create User
- Update User
- Delete User
- Restore User
- Assign Roles
- Validation
- Authorization

---

# Acceptance Criteria

- User CRUD works
- Role assignment works
- Company/Branch/Department relationships work
- Search works
- Filters work
- Pagination works
- Profile photo upload works
- Password reset works
- Policies work
- Tests pass
- Responsive UI
- No duplicated logic
- No PHP errors

---

# Definition of Done

✔ User CRUD

✔ Role Assignment

✔ Profile Photo Upload

✔ Password Reset

✔ Services

✔ Requests

✔ Policies

✔ Routes

✔ Views

✔ Tests

---

# Final Instructions

Generate ONLY:

1. User migration (if needed)
2. Model updates
3. Relationships
4. UserService
5. StoreUserRequest
6. UpdateUserRequest
7. UserPolicy
8. UserController
9. Routes
10. Blade Views
11. Seeder
12. Notifications
13. Feature Tests

Reuse all previous architecture.

Do not regenerate completed modules.

Stop after Users.

At the end explain:

- Generated files
- Manual steps
- Routes added
- Migration changes
- Assumptions