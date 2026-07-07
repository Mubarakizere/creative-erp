# Creative ERP

Version: 1.0

---

# PROJECT RULES

This document defines the development standards of the Creative ERP project.

Every future implementation MUST follow these rules.

---

# Vision

Creative ERP is a modern Enterprise Resource Planning platform built for engineering, construction, and contracting companies.

The platform must be modular, scalable, secure and ready for SaaS deployment.

It should be able to serve:

- Small Companies
- Medium Companies
- Large Enterprises

without changing the architecture.

---

# Core Principles

The system must be

- Modular
- Maintainable
- Secure
- Scalable
- Fast
- Mobile Ready
- API First
- Multi Company
- Multi Language
- Multi Currency Ready

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

Authentication

Laravel Authentication

Role Based Access Control

REST API

Laravel API

Notification System

Email

SMS (Future)

WhatsApp (Future)

Push Notifications (Future)

---

# Coding Standards

Follow PSR-12.

Never duplicate code.

Use Service Classes.

Use Repository Pattern when necessary.

Use Form Requests.

Keep Controllers small.

Move business logic into Services.

Use Policies for authorization.

Use Events whenever appropriate.

Use Queues for heavy jobs.

Never place business logic inside Blade.

---

# Database Standards

Every table should have

id

created_at

updated_at

deleted_at (where appropriate)

created_by

updated_by

company_id (where applicable)

Use Foreign Keys.

Never store duplicate information.

Normalize data whenever possible.

---

# UI Standards

The UI must be

Professional

Minimal

Responsive

Fast

Modern

Accessible

Dashboard inspired by enterprise software.

Use reusable components.

Support Dark Mode in the future.

---

# Permission Rules

Permissions must not be hardcoded.

Everything should be configurable.

Roles can be created later.

Permissions can be assigned later.

Users can have multiple roles.

---

# Multi Company

Every company must only access its own data.

No company should access another company's information.

The architecture should support SaaS in the future.

---

# API

Every major module must expose REST APIs.

The mobile application should consume the same APIs.

---

# Notifications

Free

Email

In-App

Premium

SMS

WhatsApp

Push Notifications

Premium services should remain disabled until configured.

---

# File Management

Support

Documents

Images

Videos

Drawings

BOQs

Contracts

Invoices

Certificates

Version control must exist.

Every uploaded document should keep its history.

---

# Audit Logs

Every important action should be logged.

Examples

Login

Logout

Delete

Update

Approval

Assignment

Permission changes

Document uploads

Budget updates

---

# Security

Use Policies.

Validate every request.

Prevent SQL Injection.

Prevent XSS.

Prevent CSRF.

Sanitize inputs.

Encrypt sensitive information.

Passwords must never be stored in plain text.

---

# Performance

Cache where necessary.

Queue heavy jobs.

Lazy load relationships.

Avoid N+1 queries.

Optimize database indexes.

---

# Documentation

Every completed module must update documentation.

Every future AI prompt must read this document before implementation.

This file is the master development standard.

Never violate these rules unless explicitly approved.
