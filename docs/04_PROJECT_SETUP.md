# Creative ERP

Version: 1.0

Document

04_PROJECT_SETUP

Status

Approved

---

# Purpose

This document defines the development structure of Creative ERP.

Every module must follow this structure.

---

# Technology Stack

Backend

- Laravel 12
- PHP 8.4+

Frontend

- Blade
- TailwindCSS
- Alpine.js
- Vite
- Axios
- Chart.js

Database

- MySQL 8

Version Control

- Git

---

# Folder Structure

app/

    Console/

    Events/

    Exceptions/

    Helpers/

    Http/

        Controllers/

            Admin/

            Client/

            Website/

            API/

        Middleware/

        Requests/

        Resources/

    Jobs/

    Listeners/

    Mail/

    Models/

    Notifications/

    Observers/

    Policies/

    Providers/

    Services/

    Traits/

resources/

    css/

    js/

    views/

        layouts/

        components/

        auth/

        admin/

        client/

        website/

        errors/

routes/

    web.php

    auth.php

    admin.php

    client.php

    api.php

database/

    migrations/

    seeders/

    factories/

docs/

tests/

storage/

public/

---

# Layout Structure

resources/views/layouts/

app.blade.php

admin.blade.php

client.blade.php

website.blade.php

auth.blade.php

---

# Components

resources/views/components/

button.blade.php

card.blade.php

table.blade.php

input.blade.php

textarea.blade.php

select.blade.php

badge.blade.php

modal.blade.php

breadcrumb.blade.php

sidebar.blade.php

navbar.blade.php

alert.blade.php

stats-card.blade.php

dropdown.blade.php

pagination.blade.php

loader.blade.php

---

# Services

Every module should contain a Service class.

Examples

CompanyService

ProjectService

InventoryService

FinanceService

AuthenticationService

---

# Controllers

Controllers should only

Validate request

Call Service

Return Response

Never contain business logic.

---

# Form Requests

Validation belongs inside

Http/Requests

Never validate inside controllers.

---

# Policies

Every secured module must use Policies.

---

# Database Standards

Every table should include

id

created_at

updated_at

created_by

updated_by

deleted_at (when applicable)

---

# Naming Convention

Controllers

ProjectController

Models

Project

Services

ProjectService

Requests

StoreProjectRequest

UpdateProjectRequest

Policies

ProjectPolicy

---

# Git Strategy

Feature branches

feature/authentication

feature/projects

feature/finance

Commit messages

feat:

fix:

refactor:

docs:

test:

---

# UI Style

Professional ERP

Minimal

Fast

Responsive

Clean

Consistent

---

# Development Workflow

Read AI_CONTEXT.md

↓

Read Module Documentation

↓

Generate Migration

↓

Generate Model

↓

Generate Service

↓

Generate Form Requests

↓

Generate Controller

↓

Generate Policies

↓

Generate Views

↓

Generate Routes

↓

Generate Tests

↓

Review

↓

Commit

---

# End