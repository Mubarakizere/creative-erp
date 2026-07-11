# Creative ERP

# Sprint 07 - Clients Management

Version: 1.0

Status: Ready for Development

---

# Sprint Goal

Build a complete enterprise Clients Management module fully integrated with the existing ERP.

This module must integrate with:

- Authentication
- Dashboard
- Companies
- Branches
- Departments
- Roles & Permissions
- Users

This is NOT an isolated CRUD module.

It must extend the ERP by updating the dashboard, navigation, permissions, activity logs, statistics and future reporting architecture.

---

# Read Before Coding

Read these documents before generating code.

1. docs/00_PROJECT_RULES.md
2. docs/01_PROJECT_VISION.md
3. docs/02_BUSINESS_ANALYSIS.md
4. docs/03_AUTHENTICATION.md
5. docs/04_PROJECT_SETUP.md
6. docs/AI_CONTEXT.md
7. docs/prompts/SPRINT_02.md
8. docs/prompts/SPRINT_03.md
9. docs/prompts/SPRINT_04.md
10. docs/prompts/SPRINT_05.md
11. docs/prompts/SPRINT_06.md

---

# Current Project Status

Completed

✅ Authentication

✅ Dashboard

✅ Companies

✅ Branches

✅ Departments

✅ Roles & Permissions

✅ Users

Current Sprint

Clients

Next Sprint

Projects

---

# Important Rules

Do NOT regenerate Authentication.

Do NOT regenerate Dashboard.

Do NOT regenerate Companies.

Do NOT regenerate Branches.

Do NOT regenerate Departments.

Do NOT regenerate Roles.

Do NOT regenerate Permissions.

Do NOT regenerate Users.

Reuse existing layouts.

Reuse existing Blade components.

Reuse existing Tailwind components.

Reuse existing Services architecture.

Controllers must remain thin.

Business logic belongs inside Services.

Validation belongs inside Form Requests.

Authorization belongs inside Policies and Spatie Permissions.

Follow Laravel 12 best practices.

Never duplicate existing code.

Only modify previous modules where integration is required.

---

# Module Purpose

Clients represent customers who own projects.

A Client can be

- Company
- Individual

Every Client belongs to

- Company
- Branch

Future modules connected to Clients

- Projects
- Quotations
- Contracts
- Invoices
- Payments
- Support Tickets
- Documents
- Meetings

Design the architecture with these future relationships in mind.

---

# Features

The Clients module must include

- Clients List
- Client Profile
- Create Client
- Edit Client
- Archive Client
- Restore Client
- Activate Client
- Deactivate Client
- Upload Client Logo
- Client Notes
- Search
- Filters
- Pagination
- Export Preparation

---

# Database

Create table

clients

Columns

id

uuid

company_id

branch_id

client_type

company_name

first_name

last_name

display_name

email

phone

alternate_phone

website

tax_number

registration_number

country

state

city

address

postal_code

logo

status

notes

created_by

updated_by

created_at

updated_at

deleted_at

Use UUID.

Use Soft Deletes.

Use foreign keys.

Add indexes where appropriate.

---

# Relationships

Client belongsTo Company

Client belongsTo Branch

Client belongsTo User (creator)

Client hasMany Projects

Client hasMany Contacts

Client hasMany Documents

Client hasMany Quotations

Prepare future relationships.

Do not generate those modules.

---

# Business Rules

Every Client belongs to exactly one Company.

Every Client belongs to exactly one Branch.

Client Type determines required fields.

Company Client

Required

Company Name

Optional

Contact Person

Individual Client

Required

First Name

Last Name

Email should be unique when provided.

Company Name should be unique within Company.

Archived Clients remain searchable.

Deleted Clients must be restorable.

Clients with Projects cannot be permanently deleted.

---

# Validation

Company

Required

Must exist

Branch

Required

Must exist

Client Type

Required

Allowed

Company

Individual

Company Name

Required when Client Type = Company

First Name

Required when Client Type = Individual

Last Name

Required when Client Type = Individual

Email

Nullable

Unique

Valid email

Phone

Required

Website

Nullable

URL

Logo

Image

Maximum 2MB

---

# Permissions

client.view

client.create

client.update

client.delete

client.restore

client.activate

client.deactivate

client.export

client.import

---

# Service

ClientService

Responsibilities

Create Client

Update Client

Archive Client

Restore Client

Activate Client

Deactivate Client

Upload Logo

Generate Display Name

Status Changes

Business logic belongs ONLY here.

Controllers must remain thin.

---

# UI Pages

Generate

Clients List

Create Client

Edit Client

View Client

Client Profile

---

# Client Form

Fields

Company

Branch

Client Type

Company Name

First Name

Last Name

Email

Phone

Alternate Phone

Website

Tax Number

Registration Number

Country

State

City

Address

Postal Code

Logo

Notes

Status

Dynamic Form

If Client Type = Company

Show

Company Name

Registration Number

Tax Number

Website

Hide

First Name

Last Name

If Client Type = Individual

Show

First Name

Last Name

Hide

Company Name

Registration Number

Tax Number

Website

Use Alpine.js where necessary.

---

# Clients Table

Columns

Logo

Client Name

Type

Company

Branch

Phone

Email

Status

Created At

Actions

Actions

View

Edit

Archive

Restore

Activate

Deactivate

Delete
---

# ERP Integration Requirements

This ERP already contains completed modules.

This sprint MUST integrate with the entire application.

Never build isolated CRUD modules.

Update previous modules only where integration is required.

Maintain backward compatibility.

---

# Dashboard Integration

Update the existing Dashboard.

Do NOT recreate it.

Extend it.

Dashboard Statistics Cards

Display

- Total Companies
- Total Branches
- Total Departments
- Total Users
- Total Clients

Each card must display

- Total Count
- Active Count
- Monthly Growth Placeholder
- Icon
- Color
- Link to Module

Cards must be responsive.

Cards must automatically update when records are created.

---

# Dashboard Widgets

Generate widgets

Latest Companies

Latest Branches

Latest Departments

Latest Users

Latest Clients

Each widget displays

Latest 5 records

Created Date

Quick View button

Responsive.

---

# Dashboard Charts

Prepare architecture for charts.

Charts

Companies Growth

Users Growth

Clients Growth

Departments Growth

Use service classes.

Do not implement analytics yet.

Return placeholder datasets.

---

# Dashboard Quick Actions

If Dashboard already supports Quick Actions

Add

Create Company

Create Branch

Create Department

Create User

Create Client

Quick Actions must respect permissions.

---

# Sidebar Integration

Update existing Sidebar.

Structure

Dashboard

Organization

- Companies
- Branches
- Departments

Security

- Roles
- Permissions
- Users

CRM

- Clients

Projects

Inventory

Finance

Reports

Website CMS

Settings

Only show menu items if user has permission.

Never hardcode visibility.

---

# Breadcrumbs

Generate breadcrumbs.

Examples

Dashboard

Dashboard / Clients

Dashboard / Clients / Create

Dashboard / Clients / View

Dashboard / Clients / Edit

---

# Navigation Integration

Update navigation without breaking previous modules.

Register Clients inside existing navigation architecture.

Maintain active menu highlighting.

---

# Global Search

If Global Search exists

Register Clients.

Search by

Company Name

Display Name

Email

Phone

Website

Tax Number

Registration Number

Otherwise

Prepare ClientSearchService for future implementation.

---

# Activity Feed

Update Dashboard Activity Feed.

Log

Client Created

Client Updated

Client Archived

Client Restored

Display

User

Company

Branch

Date

Action

---

# Audit Logs

Log every important action.

Create

Update

Archive

Restore

Activate

Deactivate

Include

User

Company

Branch

IP Address

Timestamp

Prepare architecture if Audit module does not yet exist.

---

# Notifications

Prepare Notification Center integration.

Generate notification events

Client Created

Client Updated

Client Archived

Client Activated

Client Deactivated

Do not implement realtime.

Use Laravel Notifications architecture.

---

# Permission Seeder

Update existing RolesAndPermissionsSeeder.

Register

client.view

client.create

client.update

client.delete

client.restore

client.activate

client.deactivate

client.export

client.import

Assign all permissions to

Super Admin

---

# Dashboard Counters

Update dashboard counters.

Companies

Branches

Departments

Users

Clients

Automatically calculate totals.

Do not hardcode values.

---

# Empty States

Generate professional empty states.

No Clients Found

No Search Results

No Archived Clients

Responsive.

---

# Filters

Company

Branch

Status

Client Type

Country

Created Date

---

# Search

Company Name

Display Name

Phone

Email

Tax Number

Registration Number

---

# Pagination

Use Laravel Pagination.

Default

25 per page.

Remember filters while navigating.

---

# File Uploads

Store Client Logos using Laravel Storage.

Use symbolic links.

Do not store directly inside public.

---

# API Preparation

Prepare architecture for future API Resources.

Do not generate API Controllers.

---

# Routes

Register authenticated admin routes.

Protect all routes using

Policies

Permissions

Authentication

Use resource routes whenever possible.

Additional routes

Restore Client

Activate Client

Deactivate Client

Archive Client

Export Clients (placeholder)

---

# Feature Tests

Generate Feature Tests

Create Client

Update Client

Archive Client

Restore Client

Activate Client

Deactivate Client

Validation

Authorization

Search

Filters

Pagination

Dashboard Integration

Navigation Visibility

Permission Enforcement

---

# Seeder

Generate sample Clients.

Company Clients

Individual Clients

Assign to existing Companies and Branches.

---

# Acceptance Criteria

Sprint is complete only if

Migration succeeds

CRUD works

Relationships work

Dashboard cards update

Dashboard widgets work

Sidebar updated

Permission Seeder updated

Policies work

Search works

Filters work

Pagination works

Logo upload works

Audit logs generated

Notifications prepared

Feature Tests pass

Responsive UI

No duplicated logic

No PHP errors

Backward compatibility maintained

---

# Definition of Done

✔ Migration

✔ Model

✔ Factory

✔ Seeder

✔ Relationships

✔ ClientService

✔ Requests

✔ Policy

✔ Controller

✔ Routes

✔ Views

✔ Dashboard Integration

✔ Sidebar Integration

✔ Activity Feed

✔ Notification Preparation

✔ Dashboard Cards

✔ Dashboard Widgets

✔ Breadcrumbs

✔ Tests

✔ Git Ready

---

# Final Instructions

Before generating code

Analyze the entire project.

Detect completed modules automatically.

Reuse existing architecture.

Never regenerate existing functionality.

Generate ONLY

1. Migration
2. Model
3. Factory
4. Seeder
5. Relationships
6. ClientService
7. StoreClientRequest
8. UpdateClientRequest
9. ClientPolicy
10. ClientController
11. Routes
12. Blade Views
13. Dashboard Integration
14. Sidebar Integration
15. Activity Feed Integration
16. Notification Preparation
17. Feature Tests

At the end explain

- Generated files
- Modified files
- Manual commands
- Database changes
- Dashboard changes
- Sidebar changes
- Seeder updates
- Routes added
- Any assumptions made

Stop after completing Sprint 07.