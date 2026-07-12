# Creative ERP

# Sprint 08 - Projects Management

Version: 1.0

Status: Ready For Development

---

# Sprint Goal

Build a complete Enterprise Project Management module.

Projects are the core operational entity of Creative ERP.

This module must integrate with every previously completed module and provide a scalable foundation for future modules including:

- Project Teams
- Tasks
- Milestones
- Time Tracking
- Documents
- Meetings
- Procurement
- Inventory
- Expenses
- Invoicing
- Reports

This is NOT a CRUD module.

It is the foundation of the operational side of the ERP.

---

# Read Before Coding

Read the following documents in order.

docs/MASTER_DEVELOPMENT_RULES.md

docs/AI_CONTEXT.md

docs/prompts/SPRINT_02.md

docs/prompts/SPRINT_03.md

docs/prompts/SPRINT_04.md

docs/prompts/SPRINT_05.md

docs/prompts/SPRINT_06.md

docs/prompts/SPRINT_07.md

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

✅ Clients

Current Sprint

Projects

Next Sprint

Project Teams

---

# Important Rules

Do NOT regenerate existing modules.

Reuse existing layouts.

Reuse existing Blade Components.

Reuse existing Services.

Reuse existing Dashboard.

Reuse existing Navigation.

Reuse existing Sidebar.

Reuse existing Authentication.

Reuse existing Permission architecture.

Controllers must remain thin.

Business Logic belongs inside Services.

Validation belongs inside Form Requests.

Authorization belongs inside Policies.

Follow Laravel 12 Best Practices.

Maintain backward compatibility.

Never duplicate code.

---

# Module Purpose

Projects represent work performed for Clients.

Every Project belongs to:

- Company
- Branch
- Client

Every Project is managed by:

- Project Manager

Every Project will later contain:

- Team Members
- Tasks
- Milestones
- Documents
- Time Entries
- Budget
- Expenses
- Invoices
- Meetings
- Equipment
- Procurement Requests

Design the architecture now to avoid future refactoring.

---

# Features

The module must support

- Projects List
- Project Details
- Create Project
- Edit Project
- Archive Project
- Restore Project
- Close Project
- Reopen Project
- Duplicate Project
- Project Timeline
- Search
- Filters
- Pagination
- Export Preparation

---

# Database

Create table

projects

Columns

id

uuid

company_id

branch_id

client_id

project_manager_id

project_code

name

description

category

priority

status

progress

estimated_budget

actual_budget

estimated_cost

actual_cost

currency

start_date

planned_end_date

actual_end_date

contract_number

reference_number

location

notes

created_by

updated_by

created_at

updated_at

deleted_at

Use

UUID

Soft Deletes

Foreign Keys

Indexes

---

# Relationships

Project belongsTo Company

Project belongsTo Branch

Project belongsTo Client

Project belongsTo User (Project Manager)

Project belongsTo User (Created By)

Project belongsTo User (Updated By)

Prepare future relationships

Project hasMany Team Members

Project hasMany Tasks

Project hasMany Milestones

Project hasMany Documents

Project hasMany Expenses

Project hasMany Time Entries

Project hasMany Meetings

Project hasMany Purchase Requests

Project hasMany Equipment

Do NOT generate these modules.

Only prepare architecture.

---

# Business Rules

Every Project belongs to exactly one Company.

Every Project belongs to exactly one Branch.

Every Project belongs to exactly one Client.

Client must belong to the selected Company.

Branch must belong to the selected Company.

Project Manager must belong to the selected Company.

Project Code must be unique within a Company.

Projects cannot start before Company creation.

Closed Projects become read-only except for Super Admin.

Archived Projects remain searchable.

Projects with financial records cannot be permanently deleted.

---

# Project Status

Allowed values

Planning

Pending

In Progress

On Hold

Completed

Cancelled

Closed

---

# Project Priority

Allowed values

Low

Medium

High

Critical

---

# Validation

Company

Required

Must exist

Branch

Required

Must exist

Client

Required

Must exist

Project Manager

Required

Must exist

Project Code

Required

Maximum 50

Unique

Project Name

Required

Maximum 255

Description

Nullable

Category

Nullable

Priority

Required

Status

Required

Estimated Budget

Numeric

Nullable

Actual Budget

Numeric

Nullable

Estimated Cost

Numeric

Nullable

Actual Cost

Numeric

Nullable

Currency

Required

Start Date

Required

Planned End Date

Nullable

Actual End Date

Nullable

Progress

Integer

Minimum 0

Maximum 100

Contract Number

Nullable

Reference Number

Nullable

Location

Nullable

Notes

Nullable

---

# Permissions

project.view

project.create

project.update

project.delete

project.restore

project.archive

project.close

project.reopen

project.export

project.import

project.assign-manager

project.change-status

project.view-budget

project.edit-budget

---

# Service

Generate

ProjectService

Responsibilities

Create Project

Update Project

Archive Project

Restore Project

Duplicate Project

Close Project

Reopen Project

Generate Project Code

Calculate Progress

Validate Project Relationships

Business logic belongs ONLY here.

Controllers remain thin.

---

# UI Pages

Generate

Projects List

Create Project

Edit Project

View Project

Project Profile

Project Timeline

---

# Project Form

Fields

Company

Branch

Client

Project Manager

Project Code

Project Name

Description

Category

Priority

Status

Estimated Budget

Estimated Cost

Currency

Start Date

Planned End Date

Contract Number

Reference Number

Location

Notes

Dynamic Dropdowns

Company

↓

Branch

↓

Client

↓

Project Manager

Use Alpine.js for dependent dropdowns.

Do not reload the page.

---

# Projects Table

Columns

Project Code

Project Name

Client

Company

Branch

Manager

Priority

Status

Progress

Budget

Start Date

End Date

Actions

Actions

View

Edit

Archive

Restore

Close

Reopen

Duplicate
---

# ERP Integration Requirements

This ERP already contains completed modules.

This sprint MUST integrate with the entire application.

Do NOT create isolated CRUD functionality.

Maintain backward compatibility.

Update only where integration is required.

---

# Dashboard Integration

Update the existing Dashboard.

Do NOT recreate it.

Extend it.

---

## Statistics Cards

Dashboard must now display

- Total Companies
- Total Branches
- Total Departments
- Total Users
- Total Clients
- Total Projects

Each card must display

- Total Count
- Active Count
- Monthly Growth Placeholder
- Icon
- Color
- Quick Link

Cards should automatically update.

Responsive design required.

---

## Dashboard Widgets

Generate widgets

Latest Companies

Latest Branches

Latest Departments

Latest Users

Latest Clients

Latest Projects

Each widget should display

Latest 5 records

Status

Created Date

Quick View button

---

## Project Overview Widget

Create a dashboard widget displaying

Projects In Planning

Projects In Progress

Projects On Hold

Projects Completed

Projects Closed

Show percentages.

Prepare architecture for charts.

---

## Budget Widget

Display

Total Estimated Budget

Total Actual Budget

Budget Variance

Return calculated values from ProjectService.

---

## Dashboard Charts

Prepare architecture.

Charts

Projects Created Per Month

Projects By Status

Projects By Priority

Projects By Branch

Projects By Client

Budget Overview

Return placeholder datasets.

Do not implement analytics yet.

---

## Quick Actions

Update Dashboard Quick Actions

Create Company

Create Branch

Create Department

Create User

Create Client

Create Project

Only display actions the authenticated user has permission to use.

---

# Sidebar Integration

Update Sidebar.

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

- Projects

Inventory

Finance

Reports

Website CMS

Settings

Use permission-based visibility.

Never hardcode menus.

---

# Breadcrumbs

Generate breadcrumbs

Dashboard

Dashboard / Projects

Dashboard / Projects / Create

Dashboard / Projects / View

Dashboard / Projects / Edit

Dashboard / Projects / Timeline

---

# Navigation Integration

Update navigation.

Maintain active menu highlighting.

Do not break previous navigation.

---

# Global Search

Register Projects inside Global Search.

Search by

Project Code

Project Name

Client

Manager

Reference Number

Contract Number

Status

Location

---

# Activity Feed

Update Dashboard Activity Feed.

Log

Project Created

Project Updated

Project Archived

Project Restored

Project Closed

Project Reopened

Project Duplicated

Display

User

Company

Branch

Client

Date

Action

---

# Audit Logs

Generate audit entries.

Actions

Create

Update

Archive

Restore

Close

Reopen

Duplicate

Include

User

Company

Branch

Client

IP Address

Timestamp

Old Values

New Values

Prepare architecture if Audit module is not yet available.

---

# Notifications

Prepare Notification Center integration.

Events

Project Created

Project Assigned

Project Updated

Project Closed

Project Archived

Project Reopened

Project Manager Changed

Use Laravel Notifications.

Do not implement realtime.

---

# Permission Seeder

Update RolesAndPermissionsSeeder.

Register

project.view

project.create

project.update

project.delete

project.restore

project.archive

project.close

project.reopen

project.export

project.import

project.assign-manager

project.change-status

project.view-budget

project.edit-budget

Assign all permissions to

Super Admin

---

# Project Profile

The Project Profile page must include

Project Information

Client Information

Company

Branch

Project Manager

Timeline

Budget Summary

Progress

Status

Priority

Quick Actions

Recent Activity

Placeholders for

Tasks

Milestones

Documents

Meetings

Expenses

Invoices

These placeholders will be completed in future sprints.

---

# Search

Allow searching by

Project Code

Project Name

Client

Manager

Reference Number

Location

Status

---

# Filters

Company

Branch

Client

Manager

Priority

Status

Start Date

End Date

Created Date

---

# Pagination

Use Laravel Pagination.

25 records per page.

Keep filters during navigation.

---

# API Preparation

Prepare architecture for API Resources.

Do not generate API Controllers.

---

# Routes

Register authenticated admin routes.

Protect all routes using

Authentication

Policies

Permissions

Generate

Resource Routes

Additional Routes

Restore

Archive

Close

Reopen

Duplicate

Change Status

Assign Manager

Timeline

---

# Seeder

Generate realistic sample Projects.

Each Project must belong to

Company

Branch

Client

Project Manager

Generate various

Statuses

Priorities

Budgets

Progress values

Dates

---

# Feature Tests

Generate Feature Tests

Create Project

Update Project

Archive Project

Restore Project

Close Project

Reopen Project

Duplicate Project

Validation

Authorization

Relationships

Search

Filters

Pagination

Dashboard Integration

Permission Enforcement

Navigation Visibility

---

# Acceptance Criteria

Sprint is complete only if

Migration succeeds

Relationships work

CRUD works

Duplicate works

Close/Reopen works

Dashboard cards update

Dashboard widgets update

Sidebar updated

Project Profile works

Timeline page works

Permission Seeder updated

Policies work

Search works

Filters work

Pagination works

Notifications prepared

Audit logging prepared

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

✔ ProjectService

✔ Requests

✔ Policy

✔ Controller

✔ Routes

✔ Blade Views

✔ Dashboard Integration

✔ Sidebar Integration

✔ Project Profile

✔ Dashboard Widgets

✔ Dashboard Charts

✔ Activity Feed

✔ Notification Preparation

✔ Audit Preparation

✔ Breadcrumbs

✔ Tests

✔ Git Ready

---

# Final Instructions

Before generating code

Analyze the entire project.

Detect existing architecture.

Reuse all completed modules.

Never regenerate existing functionality.

Generate ONLY

1. Migration
2. Model
3. Factory
4. Seeder
5. Relationships
6. ProjectService
7. StoreProjectRequest
8. UpdateProjectRequest
9. ProjectPolicy
10. ProjectController
11. Routes
12. Blade Views
13. Dashboard Integration
14. Sidebar Integration
15. Dashboard Widgets
16. Dashboard Charts
17. Activity Feed Integration
18. Notification Preparation
19. Audit Preparation
20. Feature Tests

At the end provide

- Generated files
- Modified files
- Database changes
- Dashboard changes
- Sidebar changes
- Seeder changes
- Routes added
- Manual commands
- Assumptions made

Stop after Sprint 08.