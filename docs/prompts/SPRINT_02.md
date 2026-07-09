# Creative ERP

# Sprint 02 - Companies Module

Version: 1.0

Status: Ready for Development

---

# Sprint Goal

Build the complete Companies module for Creative ERP.

By the end of this sprint, the ERP must support complete company management and be ready for future multi-company functionality.

This sprint must NOT modify completed modules.

---

# Read Before Coding

Read these documents in order:

1. docs/00_PROJECT_RULES.md
2. docs/01_PROJECT_VISION.md
3. docs/02_BUSINESS_ANALYSIS.md
4. docs/03_AUTHENTICATION.md
5. docs/04_PROJECT_SETUP.md
6. docs/AI_CONTEXT.md

---

# Current Project Status

Completed

- Authentication
- Dashboard
- Layouts
- Components

Current Sprint

Companies Module

Next Sprint

Branches

---

# Important Rules

Do NOT recreate Authentication.

Do NOT recreate Dashboard.

Do NOT recreate Layouts.

Do NOT recreate Blade Components.

Use the existing architecture.

Use Laravel 12 best practices.

Controllers must remain thin.

Business logic belongs inside Services.

Validation belongs inside Form Requests.

Authorization belongs inside Policies.

Use Dependency Injection.

Use Eloquent Relationships.

Use TailwindCSS.

Responsive UI only.

---

# Module Purpose

A Company represents the highest organizational level within Creative ERP.

Every Branch, Department, User, Client, Project, Inventory, Warehouse, Employee and Report belongs to a Company.

The architecture must support future SaaS deployment without redesign.

---

# Features

The module must provide:

- Company List
- Company Details
- Create Company
- Edit Company
- Soft Delete Company
- Restore Company
- Activate Company
- Deactivate Company
- Upload Logo
- Upload Favicon
- Company Settings
- Search
- Filters
- Pagination

---

# Business Rules

- Company name must be unique.
- Slug must be generated automatically.
- Email must be unique.
- Deleted companies must be restorable.
- A company cannot be permanently deleted if it owns related records.
- Company status controls access for its users.
- Logo and favicon should be stored using Laravel Storage.

---

# Company Status

Available statuses:

- Active
- Inactive
- Suspended

Only Active companies should be available for normal operations.

---

# Database

Create table:

companies

Columns:

- id
- uuid
- name
- legal_name
- slug

Contact

- email
- phone
- alternate_phone
- website

Branding

- logo
- favicon

Business

- registration_number
- tax_number

Address

- country
- state
- city
- address
- postal_code

Localization

- currency
- timezone
- language

Business Hours

- working_days
- working_hours_start
- working_hours_end

Other

- notes
- status

Audit

- created_by
- updated_by

Laravel

- timestamps
- softDeletes

Use UUID support.

Use indexes where appropriate.

---

# Relationships

Company has many

- Branches
- Departments
- Users
- Clients
- Projects
- Employees
- Warehouses

Do NOT generate those modules.

Only prepare relationships.

---

# Files To Generate

Generate ONLY these files.

Migration

Model

Factory

Seeder

Service

StoreCompanyRequest

UpdateCompanyRequest

Policy

Controller

Routes

Views

Feature Tests

---

# Service Responsibilities

CompanyService must handle

Create Company

Update Company

Delete Company

Restore Company

Upload Logo

Upload Favicon

Generate Slug

Status Changes

No business logic inside the controller.

---

# Validation

Company Name

- Required
- Max 255
- Unique

Email

- Required
- Valid
- Unique

Website

- Nullable
- URL

Logo

- Image
- Max 2MB

Favicon

- PNG or ICO
- Max 512KB

Phone

- Nullable
- Max 30

---

# Permissions

Use Policies.

Permissions:

company.view

company.create

company.update

company.delete

company.restore

company.activate

company.deactivate

---

# UI Pages

Generate

Company List

Create Company

Edit Company

View Company

Company Settings

---

# Company List

Columns

Logo

Name

Email

Phone

Country

Currency

Status

Created At

Actions

Actions

View

Edit

Activate

Deactivate

Delete

Restore

---

# Filters

Search

Status

Country

Created Date

---

# Forms

Create Company

Edit Company

Use reusable Blade components.

Responsive.

Validation messages.

Image preview for logo and favicon.

---

# Search

Allow searching by

Company Name

Email

Phone

Country

Registration Number

---

# Pagination

Use Laravel Pagination.

25 records per page.

---

# File Upload

Store using Laravel Storage.

Do not save files directly in public.

Use symbolic link.

---

# Routes

Register resource routes.

Protect using authentication middleware.

Protect using policies.

---

# API Preparation

Prepare the module so API resources can be added later.

Do not generate API controllers yet.

---

# Tests

Generate Feature Tests for

Create Company

Update Company

Delete Company

Restore Company

Validation

Authorization

---

# Acceptance Criteria

The sprint is complete only if:

- Migration runs successfully
- Company CRUD works
- Soft Deletes work
- Restore works
- Logo uploads work
- Favicon uploads work
- Validation works
- Policies work
- Search works
- Filters work
- Pagination works
- Tests pass
- UI is responsive
- No duplicated logic
- No PHP errors

---

# Definition of Done

✔ Migration completed

✔ Model completed

✔ Relationships completed

✔ Service completed

✔ Requests completed

✔ Policy completed

✔ Controller completed

✔ Views completed

✔ Routes completed

✔ Tests completed

✔ Git ready

---

# Final Instructions

Generate the implementation in this exact order:

1. Migration
2. Model
3. Factory
4. Seeder
5. Relationships
6. Service
7. Store Request
8. Update Request
9. Policy
10. Controller
11. Routes
12. Views
13. Feature Tests

Do not modify completed modules.

Do not regenerate layouts.

Do not regenerate Blade components.

Do not generate Branches.

Stop after completing the Companies module.

At the end, explain:

- Folder structure
- Generated files
- Any assumptions made
- Any manual steps required after generation