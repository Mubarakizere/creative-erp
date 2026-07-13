# Creative ERP

# Sprint 09 - Project Teams Management

Version: 1.0

Status: Ready For Development

---

# Sprint Goal

Build a complete Enterprise Project Team Management module.

This sprint extends the Projects module by allowing multiple users to participate in a project with different responsibilities.

This module must become the foundation for all future collaboration modules including:

- Tasks
- Milestones
- Documents
- Time Tracking
- Meetings
- Approvals
- Project Discussions
- Project Notifications

This is NOT a simple CRUD.

It is the collaboration engine of Creative ERP.

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

docs/prompts/SPRINT_08.md

docs/prompts/ERP_INTEGRATION_REVIEW.md

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

✅ Projects

Current Sprint

Project Teams

Next Sprint

Tasks

---

# Important Rules

Do NOT regenerate completed modules.

Reuse

- Existing Dashboard
- Existing Sidebar
- Existing Layouts
- Existing Components
- Existing Services
- Existing Policies
- Existing Requests

Maintain backward compatibility.

Use Laravel 12 Best Practices.

Controllers must remain thin.

Business Logic belongs inside Services.

Validation belongs inside Form Requests.

Authorization belongs inside Policies.

Never duplicate code.

---

# Module Purpose

A Project Team represents all people assigned to work on a project.

A user may belong to multiple Projects.

A Project contains multiple Team Members.

Each Team Member has a Project Role.

Project Roles are independent from System Roles.

Example

System Role

Manager

Project Role

Site Engineer

---

# Features

The module must support

- Team Members List
- Assign Member
- Edit Assignment
- Remove Member
- Activate Member
- Deactivate Member
- Search Members
- Filters
- Pagination
- Export Preparation

---

# Database

Create table

project_members

Columns

id

uuid

project_id

user_id

project_role

department_id

joined_at

left_at

allocation_percentage

hourly_rate

status

notes

created_by

updated_by

timestamps

softDeletes

Use

UUID

Soft Deletes

Foreign Keys

Indexes

---

# Relationships

Project

hasMany Project Members

ProjectMember

belongsTo Project

belongsTo User

belongsTo Department

User

belongsToMany Projects

through project_members

Prepare future relationships

Tasks

Documents

Time Entries

Approvals

Meetings

Discussions

Do NOT generate them.

---

# Project Roles

Allow

Project Manager

Assistant Project Manager

Architect

Engineer

Site Engineer

Civil Engineer

Electrical Engineer

Mechanical Engineer

Quantity Surveyor

Procurement Officer

Accountant

HR Representative

Quality Controller

Safety Officer

Supervisor

Foreman

Technician

Viewer

Administrator

Allow custom roles in the future.

---

# Business Rules

A user can belong to multiple Projects.

A Project can have unlimited Team Members.

A Project must have only ONE active Project Manager.

Allocation Percentage

Minimum

1

Maximum

100

A user cannot be assigned twice to the same Project.

Inactive Members cannot receive Tasks.

Removed Members remain in history.

---

# Validation

Project

Required

User

Required

Department

Required

Project Role

Required

Allocation Percentage

Required

Numeric

Minimum

1

Maximum

100

Hourly Rate

Nullable

Numeric

Joined Date

Required

Left Date

Nullable

Status

Required

Notes

Nullable

---

# Permissions

project-team.view

project-team.create

project-team.update

project-team.delete

project-team.restore

project-team.assign

project-team.remove

project-team.activate

project-team.deactivate

project-team.export

project-team.import

---

# Service

Generate

ProjectTeamService

Responsibilities

Assign Member

Update Assignment

Remove Member

Restore Member

Activate Member

Deactivate Member

Validate Project Rules

Prevent Duplicate Members

Enforce Single Active Project Manager

Business logic belongs ONLY here.

Controllers remain thin.

---

# UI Pages

Generate

Project Team List

Assign Member

Edit Assignment

Member Profile

Project Team Overview

---

# Assignment Form

Fields

Project

User

Department

Project Role

Allocation Percentage

Hourly Rate

Joined Date

Status

Notes

Dynamic Dropdowns

Company

↓

Branch

↓

Project

↓

User

↓

Department

Use Alpine.js.

No page reload.

---

# Team Members Table

Columns

Avatar

Member Name

Department

Project Role

Allocation %

Hourly Rate

Joined Date

Status

Actions

Actions

View

Edit

Deactivate

Activate

Remove

Restore
---

# ERP Integration Requirements

This sprint extends the existing Projects module.

Do NOT create isolated CRUD functionality.

The Project Teams module must integrate seamlessly with all existing modules.

Maintain backward compatibility.

Update only where integration is required.

---

# Dashboard Integration

Update the existing Dashboard.

Do NOT recreate it.

Extend it.

---

## Statistics Cards

Add new Dashboard cards

- Total Team Members
- Active Team Members
- Inactive Team Members
- Project Managers
- Engineers
- Team Utilization

Cards should display

- Total Count
- Active Count
- Quick Link
- Professional Icon
- Responsive Design

Cards update automatically.

---

## Dashboard Widgets

Generate

Latest Team Members

Recently Assigned Members

Upcoming Member Join Dates

Team Distribution

Recent Team Activity

Each widget displays

Latest five records

Status

Project

Assigned Date

Quick View button

---

## Team Overview Widget

Display

Total Projects With Teams

Projects Without Teams

Average Team Size

Largest Team

Smallest Team

Prepare architecture for charts.

---

## Allocation Widget

Display

Average Allocation %

Overallocated Members

Available Members

Prepare datasets.

---

## Dashboard Charts

Prepare chart architecture.

Charts

Team Members Per Project

Members By Department

Members By Role

Allocation Overview

Projects By Team Size

Return placeholder datasets.

---

## Quick Actions

Update Dashboard Quick Actions

Assign Member

Create Project

Create Client

Create User

Only display actions the authenticated user has permission to use.

---

# Sidebar Integration

Update Sidebar

Projects

    Projects

    Project Teams

Use permission-based visibility.

Do not duplicate navigation.

---

# Breadcrumbs

Generate

Dashboard

Dashboard / Projects

Dashboard / Projects / Team

Dashboard / Projects / Team / Assign

Dashboard / Projects / Team / Edit

Dashboard / Projects / Team / Member

---

# Navigation Integration

Update navigation.

Maintain active menu highlighting.

Desktop

Tablet

Mobile

All navigation must remain responsive.

---

# Project Profile Integration

Update Project Profile.

Add new tabs

Overview

Team

Timeline

Budget

Activity

Documents (Coming Soon)

Tasks (Coming Soon)

Milestones (Coming Soon)

Meetings (Coming Soon)

Time Tracking (Coming Soon)

Inventory (Coming Soon)

Finance (Coming Soon)

The Team tab displays

Avatar

Member Name

Department

Project Role

Allocation

Status

Join Date

Actions

---

# Global Search

Register Team Members.

Allow search by

Member Name

Project

Department

Project Role

Status

---

# Activity Feed

Log

Member Assigned

Assignment Updated

Member Removed

Member Restored

Member Activated

Member Deactivated

Manager Changed

Display

User

Project

Department

Date

Action

---

# Audit Logs

Prepare architecture.

Audit

Assignment

Update

Removal

Restoration

Status Change

Manager Change

Capture

User

Old Values

New Values

IP Address

Timestamp

---

# Notifications

Prepare notifications

Member Assigned

Project Manager Changed

Member Removed

Assignment Updated

Member Activated

Member Deactivated

Use Laravel Notifications.

Realtime implementation deferred.

---

# Permission Seeder

Update RolesAndPermissionsSeeder.

Register

project-team.view

project-team.create

project-team.update

project-team.delete

project-team.restore

project-team.assign

project-team.remove

project-team.activate

project-team.deactivate

project-team.export

project-team.import

Assign all permissions to

Super Admin

---

# Search

Search by

Project

Member Name

Department

Project Role

Status

---

# Filters

Company

Branch

Project

Department

Project Role

Status

Joined Date

Allocation %

---

# Pagination

Use Laravel Pagination.

25 records per page.

Preserve filters.

---

# API Preparation

Prepare API Resources.

Do not generate API Controllers.

---

# Routes

Generate authenticated admin routes.

Protect using

Authentication

Policies

Permissions

Generate

Resource Routes

Additional Routes

Assign Member

Remove Member

Restore Member

Activate Member

Deactivate Member

Change Role

Transfer Project Manager

---

# Seeder

Generate realistic sample data.

Every Project receives

5–10 Team Members

Use existing Users.

Distribute roles realistically.

Ensure only one active Project Manager per Project.

Generate

Allocation

Departments

Join Dates

Statuses

---

# Feature Tests

Generate Feature Tests

Assign Member

Update Assignment

Remove Member

Restore Member

Activate Member

Deactivate Member

Transfer Project Manager

Validation

Authorization

Relationships

Duplicate Prevention

Allocation Validation

Dashboard Integration

Permission Enforcement

Navigation Visibility

---

# Acceptance Criteria

Sprint is complete only if

Migration succeeds

Relationships work

Assignments work

Duplicate assignment prevented

Single active Project Manager enforced

Dashboard updated

Project Profile Team tab works

Sidebar updated

Activity Feed updated

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

✔ ProjectTeamService

✔ Requests

✔ Policy

✔ Controller

✔ Routes

✔ Blade Views

✔ Dashboard Integration

✔ Sidebar Integration

✔ Team Tab

✔ Dashboard Widgets

✔ Dashboard Charts

✔ Activity Feed

✔ Notification Preparation

✔ Audit Preparation

✔ Breadcrumbs

✔ Feature Tests

✔ Git Ready

---

# Final Instructions

Before generating code

Analyze the entire project.

Detect existing architecture.

Reuse existing modules.

Never regenerate completed functionality.

Generate ONLY

1. Migration
2. Model
3. Factory
4. Seeder
5. Relationships
6. ProjectTeamService
7. StoreProjectMemberRequest
8. UpdateProjectMemberRequest
9. ProjectTeamPolicy
10. ProjectTeamController
11. Routes
12. Blade Views
13. Dashboard Integration
14. Sidebar Integration
15. Team Dashboard Widgets
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

Stop after Sprint 09.