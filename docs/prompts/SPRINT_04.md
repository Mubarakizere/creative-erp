# Creative ERP

# Sprint 04 - Departments Module

Version: 1.0

Status: Ready for Development

---

# Sprint Goal

Build the complete Departments module.

Every Company can have multiple Branches.

Every Branch can have multiple Departments.

Departments organize employees, users and future workflows.

This module must integrate seamlessly with the existing Companies and Branches modules.

---

# Read Before Coding

Read these documents in order:

1. docs/00_PROJECT_RULES.md
2. docs/01_PROJECT_VISION.md
3. docs/02_BUSINESS_ANALYSIS.md
4. docs/03_AUTHENTICATION.md
5. docs/04_PROJECT_SETUP.md
6. docs/AI_CONTEXT.md
7. docs/prompts/SPRINT_02.md
8. docs/prompts/SPRINT_03.md

---

# Current Status

Completed

- Authentication
- Dashboard
- Companies
- Branches

Current Sprint

Departments

Next Sprint

Roles & Permissions

---

# Important Rules

Do NOT modify Authentication.

Do NOT recreate Companies.

Do NOT recreate Branches.

Reuse existing layouts.

Reuse Blade components.

Use Services.

Use Form Requests.

Use Policies.

Use Laravel 12 best practices.

Controllers must remain thin.

---

# Module Purpose

A Department represents an organizational unit within a Branch.

Examples:

- Human Resources
- Finance
- Procurement
- Engineering
- Electrical
- Mechanical
- Architecture
- Administration
- Warehouse
- IT
- Operations

Each Department belongs to one Company and one Branch.

---

# Features

- Department List
- Create Department
- Edit Department
- View Department
- Soft Delete
- Restore
- Activate
- Deactivate
- Search
- Filters
- Pagination

---

# Database

Table: departments

Columns

- id
- uuid
- company_id
- branch_id
- name
- code
- manager_name
- email
- phone
- description
- status
- created_by
- updated_by
- created_at
- updated_at
- deleted_at

Use UUIDs.

Use Soft Deletes.

Use foreign keys.

---

# Relationships

Department belongsTo Company

Department belongsTo Branch

Department hasMany Users

Department hasMany Employees

Department hasMany Projects

Prepare relationships only.

Do not generate Users, Employees or Projects.

---

# Business Rules

Every Department belongs to exactly one Company.

Every Department belongs to exactly one Branch.

Department Code must be unique within the Company.

Department Name must be unique within the Branch.

Departments cannot be permanently deleted if related records exist.

---

# Validation

Company

- Required
- Must exist

Branch

- Required
- Must exist

Name

- Required
- Max 255

Code

- Required
- Max 50
- Unique within Company

Email

- Nullable
- Valid email

Phone

- Nullable
- Max 30

Description

- Nullable
- Max 1000

---

# Permissions

department.view

department.create

department.update

department.delete

department.restore

department.activate

department.deactivate

---

# UI Pages

- Department List
- Create Department
- Edit Department
- View Department

---

# Department Form

Fields

- Company
- Branch
- Department Name
- Department Code
- Manager Name
- Email
- Phone
- Description
- Status

When a Company is selected, only show Branches that belong to that Company.

Use AJAX or Alpine.js for dependent dropdowns if appropriate.

---

# Table

Columns

- Company
- Branch
- Department
- Code
- Manager
- Status
- Created At
- Actions

Actions

- View
- Edit
- Activate
- Deactivate
- Delete
- Restore

---

# Search

Search by:

- Company
- Branch
- Department Name
- Department Code
- Manager Name

---

# Filters

- Company
- Branch
- Status
- Created Date

---

# Service

DepartmentService

Responsibilities

- Create
- Update
- Delete
- Restore
- Status Change

Business logic belongs here.

---

# Routes

Use authenticated admin routes.

Protect using Policies.

---

# Tests

Generate Feature Tests for

- Create
- Update
- Delete
- Restore
- Validation
- Authorization

---

# Acceptance Criteria

- Migration runs successfully
- CRUD works
- Relationships work
- Search works
- Filters work
- Pagination works
- Validation works
- Policies work
- Tests pass
- Responsive UI
- No duplicated logic
- No PHP errors

---

# Definition of Done

✔ Migration

✔ Model

✔ Factory

✔ Seeder

✔ Relationships

✔ Service

✔ Requests

✔ Policy

✔ Controller

✔ Routes

✔ Views

✔ Feature Tests

---

# Final Instructions

Generate ONLY:

1. Migration
2. Model
3. Factory
4. Seeder
5. Relationships
6. DepartmentService
7. StoreDepartmentRequest
8. UpdateDepartmentRequest
9. DepartmentPolicy
10. DepartmentController
11. Routes
12. Blade Views
13. Feature Tests

Reuse existing architecture.

Reuse layouts.

Reuse Blade components.

Do not regenerate previous modules.

Stop after Departments.

At the end explain:

- Generated files
- Manual steps
- Routes added
- Any assumptions