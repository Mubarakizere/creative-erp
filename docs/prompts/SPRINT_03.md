# Creative ERP

# Sprint 03 - Branches Module

Version: 1.0

Status: Ready for Development

---

# Sprint Goal

Build the complete Branches module.

Every Company can have one or many Branches.

All future Departments, Employees, Projects and Warehouses will belong to a Branch.

---

# Read Before Coding

Read:

docs/00_PROJECT_RULES.md

docs/01_PROJECT_VISION.md

docs/02_BUSINESS_ANALYSIS.md

docs/03_AUTHENTICATION.md

docs/04_PROJECT_SETUP.md

docs/AI_CONTEXT.md

docs/prompts/SPRINT_02.md

---

# Current Status

Completed

✅ Authentication

✅ Dashboard

✅ Companies

Current Sprint

Branches

Next Sprint

Departments

---

# Important Rules

Do NOT modify Authentication.

Do NOT recreate Companies.

Reuse existing Blade components.

Reuse layouts.

Reuse Services architecture.

Controllers must remain thin.

Use Policies.

Use Form Requests.

Laravel 12 best practices.

---

# Module Purpose

A Branch represents a physical office, regional office, project office or operational location belonging to a Company.

Every Branch belongs to exactly one Company.

---

# Features

Branch List

Create Branch

Edit Branch

View Branch

Delete Branch

Restore Branch

Activate Branch

Deactivate Branch

Search

Filters

Pagination

---

# Database

Table

branches

Columns

id

uuid

company_id

name

code

email

phone

manager_name

country

state

city

address

postal_code

latitude

longitude

status

notes

created_by

updated_by

timestamps

softDeletes

---

# Relationships

Branch belongsTo Company

Branch hasMany Departments

Branch hasMany Employees

Branch hasMany Projects

Branch hasMany Warehouses

Prepare relationships only.

Do not generate those modules.

---

# Business Rules

Every Branch belongs to one Company.

Branch Code must be unique within the Company.

Branch Name must be unique within the Company.

Deleted Branches must be restorable.

A Branch with dependent records cannot be permanently deleted.

---

# Validation

Company

Required

Must exist

Branch Name

Required

Max 255

Branch Code

Required

Unique within Company

Email

Nullable

Valid email

Phone

Nullable

Latitude

Nullable

Numeric

Longitude

Nullable

Numeric

---

# Permissions

branch.view

branch.create

branch.update

branch.delete

branch.restore

branch.activate

branch.deactivate

---

# UI Pages

Branch List

Create Branch

Edit Branch

View Branch

---

# Company Selection

When creating a Branch

Allow selecting Company.

Prepare architecture for future automatic company selection in multi-tenant mode.

---

# Table

Company

Branch

Code

Manager

Phone

Status

Created At

Actions

---

# Search

Search by

Company

Branch Name

Branch Code

Manager

Phone

City

---

# Filters

Company

Status

Country

Created Date

---

# Service

BranchService

Must handle

Create

Update

Delete

Restore

Status Change

No business logic in controller.

---

# Routes

Use authenticated admin routes.

Policies required.

---

# Tests

Generate Feature Tests

Create

Update

Delete

Restore

Validation

Authorization

---

# Acceptance Criteria

Migration works.

Relationships work.

CRUD works.

Search works.

Filters work.

Pagination works.

Policies work.

Validation works.

Tests pass.

Responsive UI.

No duplicated logic.

No PHP errors.

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

✔ Tests

---

# Final Instructions

Generate ONLY:

Migration

Model

Factory

Seeder

Relationships

Service

Store Request

Update Request

Policy

Controller

Routes

Views

Feature Tests

Reuse existing project architecture.

Stop after Branches.

Explain

Generated files

Manual steps

Routes

Assumptions