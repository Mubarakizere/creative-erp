# Creative ERP

# Sprint 05 - Roles & Permissions

Version: 1.0

Status: Ready for Development

---

# Sprint Goal

Build a complete enterprise Role & Permission Management module using Spatie Laravel Permission.

The system must support dynamic roles and permissions without hardcoded authorization.

The architecture must support future multi-company expansion.

---

# Read Before Coding

Read:

docs/00_PROJECT_RULES.md

docs/01_PROJECT_VISION.md

docs/02_BUSINESS_ANALYSIS.md

docs/03_AUTHENTICATION.md

docs/04_PROJECT_SETUP.md

docs/AI_CONTEXT.md

---

# Current Status

Completed

- Authentication
- Dashboard
- Companies
- Branches
- Departments

Current Sprint

Roles & Permissions

Next Sprint

Users

---

# Important Rules

Do not modify Authentication.

Do not modify Companies.

Do not modify Branches.

Do not modify Departments.

Reuse existing layouts.

Reuse existing Blade Components.

Use Laravel 12.

Use Spatie Laravel Permission.

Controllers must remain thin.

Business logic belongs inside RoleService and PermissionService.

---

# Install Package

Use:

composer require spatie/laravel-permission

Publish configuration and migrations.

Configure according to Laravel 12.

---

# Features

Role Management

Permission Management

Assign Permissions to Roles

Assign Multiple Permissions

Search

Filters

Pagination

Soft Delete support for application records where applicable (do not modify Spatie tables)

---

# Roles

Default roles

Super Admin

Company Admin

Project Manager

HR Manager

Finance Manager

Procurement Officer

Warehouse Manager

Engineer

Employee

Client

These are default seed data only.

Administrators can create additional roles later.

---

# Permissions

Create permissions for all current and future modules.

Examples

company.view

company.create

company.update

company.delete

branch.view

department.view

user.view

project.view

inventory.view

report.view

settings.manage

Generate a comprehensive permission list grouped by module.

---

# Database

Use Spatie default tables.

Do not redesign them.

Add seeders for default roles and permissions.

---

# Services

RoleService

PermissionService

Responsibilities

Create Role

Update Role

Delete Role

Assign Permissions

Sync Permissions

---

# UI Pages

Roles List

Create Role

Edit Role

Role Details

Permissions List

Create Permission

Edit Permission

Permission Details

---

# Role Form

Role Name

Guard Name

Permissions (checkbox groups by module)

---

# Permission Form

Permission Name

Module

Description (optional)

---

# Search

Search Roles

Search Permissions

---

# Filters

Module

Guard

Created Date

---

# Permissions

role.view

role.create

role.update

role.delete

permission.view

permission.create

permission.update

permission.delete

---

# Seeder

Create default

Roles

Permissions

Assign all permissions to Super Admin.

---

# Tests

Generate Feature Tests

Create Role

Update Role

Delete Role

Assign Permissions

Authorization

Validation

---

# Acceptance Criteria

Package installed.

Configuration complete.

Migrations successful.

Seeder works.

CRUD works.

Permissions assign correctly.

Super Admin has all permissions.

Policies work.

Tests pass.

Responsive UI.

No duplicated logic.

---

# Definition of Done

✔ Package installed

✔ Configuration published

✔ Roles CRUD

✔ Permissions CRUD

✔ Seeder

✔ Services

✔ Requests

✔ Controllers

✔ Routes

✔ Views

✔ Tests

---

# Final Instructions

Generate ONLY:

Package configuration

Services

Requests

Controllers

Routes

Views

Seeders

Tests

Integrate with existing authentication.

Do not build Users.

Stop after Roles & Permissions.

Explain:

Generated files

Manual steps

Commands to run

Any assumptions