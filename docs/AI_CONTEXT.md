# Creative ERP

## AI Development Context

Version: 1.0

---

# Project Information

Project Name

Creative ERP

Description

Creative ERP is a professional Enterprise Resource Planning (ERP) platform designed for engineering, construction, contracting and project-driven companies.

This project is built for long-term scalability and future SaaS deployment.

---

# Technology Stack

Backend

Laravel 12

PHP 8.4+

Database

MySQL 8

Frontend

Blade

Tailwind CSS

Alpine.js

Chart.js

Axios

Build Tool

Vite

API

REST API

Authentication

Custom Laravel Authentication

(No Breeze)

(No Jetstream)

(No Filament)

---

# Architecture

The application follows Modular Architecture.

Business logic belongs inside Services.

Validation belongs inside Form Requests.

Authorization belongs inside Policies.

Controllers should remain thin.

Heavy jobs must use Queues.

Events should be used whenever appropriate.

---

# Design Rules

Professional ERP UI

Responsive

Clean

Modern

Minimal

Fast

Reusable Components

Future Dark Mode Support

---

# Development Status

Completed Documentation

✅ PROJECT_RULES

✅ PROJECT_VISION

✅ BUSINESS_ANALYSIS

✅ AUTHENTICATION

Completed Modules

None

Current Module

Authentication

Next Module

Companies

---

# Folder Structure

app/

Http/

Models/

Services/

Repositories/

Policies/

Events/

Listeners/

Traits/

Helpers/

database/

resources/

routes/

tests/

docs/

---

# Coding Standards

PSR-12

Service Pattern

Repository Pattern (only when necessary)

Use Dependency Injection

Use Eloquent Relationships

Avoid duplicated logic

Use Laravel Best Practices

---

# Database Rules

Every major table should include

id

created_at

updated_at

created_by

updated_by

company_id (where applicable)

Soft Deletes where appropriate

Use Foreign Keys

Use Indexes

---

# Permission Rules

Permissions are dynamic.

Roles are dynamic.

Users may have multiple roles.

No permission should be hardcoded.

Everything must be configurable.

---

# UI Components

Navigation

Sidebar

Header

Breadcrumbs

Search

Filters

Cards

Tables

Pagination

Modals

Notifications

Charts

Forms

Statistics

---

# Notifications

Included

Email

In-App

Future Paid Modules

SMS

WhatsApp

Push Notifications

---

# Modules

Authentication

Companies

Branches

Departments

Users

Roles

Permissions

Clients

Projects

Tasks

Documents

Inventory

Materials

Equipment

Procurement

Finance

HR

Reports

Dashboard

Website CMS

API

Settings

Audit Logs

---

# Future

Flutter App

Desktop App

White Label

Multi Tenant SaaS

AI Assistant

GPS

Biometrics

Digital Signature

OCR

---

# AI Instructions

Every implementation must

Read PROJECT_RULES.md

Read PROJECT_VISION.md

Read BUSINESS_ANALYSIS.md

Read AI_CONTEXT.md

Read the current module documentation.

Never recreate completed modules.

Always extend existing code.

Maintain naming consistency.

Do not overwrite existing functionality.

Follow Laravel best practices.

Generate production-ready code.
