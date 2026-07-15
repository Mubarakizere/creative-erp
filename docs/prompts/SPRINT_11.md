# Creative ERP

# Sprint 11 - Milestones Management

Version: 1.0

Status: Ready For Development

---

# Sprint Goal

Build a complete Enterprise Milestones Management module.

A Milestone represents a major project deliverable that groups multiple Tasks together.

Milestones allow project managers to monitor overall progress, deadlines, and completion of significant phases within a project.

This module becomes the foundation for

- Project Planning
- Project Scheduling
- Progress Tracking
- Reports
- Billing Preparation
- Time Tracking
- Project Calendar
- Gantt Chart

This is NOT a simple CRUD.

It is the project planning engine of Creative ERP.

---

# Read Before Coding

Read carefully

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

docs/prompts/SPRINT_09.md

docs/prompts/SPRINT_10.md

---

# Current ERP Status

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

✅ Project Teams

✅ Tasks

Current Sprint

Milestones

Next Sprint

Documents & File Management

---

# Important Rules

Do NOT regenerate completed modules.

Reuse

- Dashboard
- Sidebar
- Navigation
- Blade Components
- Layouts
- Services
- Policies
- Requests
- Dashboard Widgets

Maintain backward compatibility.

Controllers remain thin.

Business Logic belongs inside Services.

Validation belongs inside Form Requests.

Authorization belongs inside Policies.

Follow Laravel 12 Best Practices.

Never duplicate code.

---

# Module Purpose

A Milestone represents a major phase of a Project.

A Milestone contains multiple Tasks.

Milestone Progress is automatically calculated from linked Tasks.

Milestones contribute to overall Project Progress.

Prepare architecture for

- Time Tracking
- Reports
- Billing
- Documents
- Calendar
- Gantt
- Resource Planning

Do NOT implement those modules.

---

# Features

The module must support

- Milestones List

- Milestone Details

- Create Milestone

- Edit Milestone

- Archive Milestone

- Restore Milestone

- Duplicate Milestone

- Assign Tasks

- Remove Tasks

- Auto Progress Calculation

- Auto Completion

- Timeline

- Search

- Filters

- Pagination

- Export Preparation

---

# Database

Create table

milestones

Columns

id

uuid

project_id

code

name

description

priority

status

progress

start_date

due_date

completed_at

color

sort_order

notes

created_by

updated_by

timestamps

softDeletes

---

Create pivot table

milestone_task

Columns

id

milestone_id

task_id

timestamps

Unique Constraint

milestone_id + task_id

---

# Relationships

Milestone

belongsTo Project

belongsTo Creator

belongsTo Updater

belongsToMany Tasks

Task

belongsToMany Milestones

Prepare future relationships

Invoices

Payments

Documents

Reports

Time Entries

Calendar Events

Do NOT generate them.

---

# Business Rules

Every Milestone belongs to one Project.

Every Milestone may contain multiple Tasks.

A Task may belong to only one active Milestone.

Milestone Progress

Automatically calculated

Formula

Completed Tasks

÷

Total Tasks

×

100

If all Tasks are completed

Automatically

Status

Completed

Progress

100%

Completed Date

Current Date

If Tasks are reopened

Milestone automatically returns to

In Progress

---

# Milestone Status

Allowed values

Planning

Not Started

In Progress

On Hold

Completed

Cancelled

---

# Milestone Priority

Allowed values

Low

Medium

High

Critical

---

# Validation

Project

Required

Code

Required

Unique within Project

Name

Required

Maximum 255

Description

Nullable

Priority

Required

Status

Required

Progress

Read Only

Start Date

Required

Due Date

Nullable

Completed Date

Read Only

Color

Nullable

Notes

Nullable

---

# Permissions

milestone.view

milestone.create

milestone.update

milestone.delete

milestone.restore

milestone.archive

milestone.assign-task

milestone.remove-task

milestone.complete

milestone.export

milestone.import

---

# Service

Generate

MilestoneService

Responsibilities

Create Milestone

Update Milestone

Archive Milestone

Restore Milestone

Duplicate Milestone

Assign Tasks

Remove Tasks

Calculate Progress

Complete Milestone

Reopen Milestone

Validate Business Rules

Business Logic belongs ONLY inside MilestoneService.

Controllers remain thin.

---

# UI Pages

Generate

Milestones List

Create Milestone

Edit Milestone

Milestone Details

Milestone Timeline

Milestone Activity

---

# Milestone Form

Fields

Project

Code

Name

Description

Priority

Status

Start Date

Due Date

Color

Notes

Task Assignment

Multiple Selection

Searchable

Use Alpine.js.

No page reload.

---

# Milestones Table

Columns

Code

Name

Project

Tasks Count

Progress

Priority

Status

Due Date

Actions

Actions

View

Edit

Duplicate

Archive

Restore
---

# ERP Integration Requirements

Milestones are a core Project Management module.

They must integrate seamlessly with the existing ERP.

Do NOT regenerate completed modules.

Reuse existing architecture.

Maintain backward compatibility.

---

# Dashboard Integration

Extend the existing Dashboard.

Do NOT recreate it.

---

## Statistics Cards

Add Dashboard cards

- Total Milestones
- Active Milestones
- Completed Milestones
- Overdue Milestones
- Milestones Due This Week
- Critical Milestones

Cards must display

- Count
- Quick Action
- Status Indicator
- Responsive Design

---

## Dashboard Widgets

Generate

- Recent Milestones
- Upcoming Milestones
- Recently Completed Milestones
- Milestones Near Deadline
- Projects With Delayed Milestones

Display latest 5 records.

---

## Dashboard Charts

Prepare chart architecture.

Charts

Milestones By Status

Milestones By Priority

Milestones Per Project

Completed Milestones Per Month

Project Completion Overview

Return placeholder datasets only.

Charts will be implemented later.

---

# Project Progress Integration

Automatically update Project Progress.

Project Progress should be calculated from

Milestone Progress

instead of directly from Tasks.

Calculation

Average Progress of all Active Milestones.

If no Milestones exist

Fallback to Task Progress.

---

# Task Integration

Replace the "Assign Tasks" button with an advanced searchable selector.

Support

- Search by Task Code
- Search by Title
- Filter by Status
- Filter by Priority

Prevent assigning the same Task twice.

Prevent assigning archived Tasks.

Prevent assigning Tasks from another Project.

---

# Timeline

Generate Milestone Timeline.

Display

Created

Updated

Task Assigned

Task Removed

Completed

Reopened

Archived

Restored

Chronological order.

---

# Milestone Activity

Track

Created

Updated

Assigned Tasks

Removed Tasks

Completed

Reopened

Archived

Restored

Status Changed

Priority Changed

---

# Project Profile Integration

Update Project Profile.

Replace

Milestones (Coming Soon)

with

Milestones

Display

Milestone Summary

Progress

Open Milestones

Completed Milestones

Milestones Near Deadline

Milestone List

Quick Create

Quick Search

Quick Filters

---

# Milestone Detail Page

Display

General Information

Timeline

Assigned Tasks

Progress

Priority

Status

Dates

Activity

Future Tabs

Documents

Invoices

Time Entries

Approvals

Reports

Display placeholders only.

---

# Sidebar Integration

Update Sidebar

Projects

    Projects

    Project Teams

    Tasks

    Milestones

Maintain permission-based visibility.

---

# Breadcrumbs

Generate

Dashboard

Dashboard / Milestones

Dashboard / Milestones / Create

Dashboard / Milestones / Edit

Dashboard / Milestones / View

Dashboard / Projects / Milestones

---

# Global Search

Register Milestones.

Search by

Code

Name

Project

Priority

Status

---

# Filters

Company

Branch

Project

Priority

Status

Progress

Due Date

Created Date

Completed Date

---

# Sorting

Support sorting

Code

Name

Priority

Progress

Status

Due Date

Newest

Oldest

---

# Pagination

25 records per page.

Preserve filters.

---

# Activity Feed

Log

Milestone Created

Milestone Updated

Task Assigned

Task Removed

Milestone Completed

Milestone Reopened

Milestone Archived

Milestone Restored

Priority Changed

Status Changed

Display

User

Milestone

Project

Timestamp

---

# Notifications

Prepare notifications

Milestone Created

Milestone Assigned

Milestone Due Soon

Milestone Completed

Milestone Overdue

Task Added

Task Removed

Realtime implementation deferred.

---

# Audit Logs

Prepare architecture.

Capture

Old Values

New Values

User

IP Address

Timestamp

Action

---

# Permission Seeder

Update RolesAndPermissionsSeeder.

Register

milestone.view

milestone.create

milestone.update

milestone.delete

milestone.restore

milestone.archive

milestone.assign-task

milestone.remove-task

milestone.complete

milestone.export

milestone.import

Assign all permissions to

Super Admin

---

# Seeder

Generate realistic sample data.

Each Project

3–8 Milestones

Each Milestone

5–20 Tasks

Random

Priority

Status

Progress

Dates

Assignments

Use existing Projects and Tasks.

---

# Feature Tests

Generate tests

Create Milestone

Update Milestone

Archive Milestone

Restore Milestone

Duplicate Milestone

Assign Tasks

Remove Tasks

Auto Progress Calculation

Auto Completion

Reopen Milestone

Authorization

Validation

Relationships

Dashboard Integration

Sidebar Visibility

Project Integration

Search

Filters

Sorting

Pagination

Duplicate Task Prevention

Cross Project Validation

---

# Performance

Avoid N+1 queries.

Use eager loading.

Reuse existing Services.

Reuse Blade Components.

Reuse Dashboard Widgets.

Do not duplicate logic.

---

# API Preparation

Prepare API Resources.

Do NOT generate API Controllers.

---

# Acceptance Criteria

Sprint is complete only if

✔ Migration succeeds

✔ Seeder succeeds

✔ Relationships work

✔ CRUD works

✔ Task Assignment works

✔ Auto Progress works

✔ Auto Completion works

✔ Dashboard updated

✔ Sidebar updated

✔ Project Profile updated

✔ Timeline works

✔ Activity Feed updated

✔ Notifications prepared

✔ Audit prepared

✔ Search works

✔ Filters work

✔ Sorting works

✔ Pagination works

✔ Permission Seeder updated

✔ Policies work

✔ Feature Tests pass

✔ Responsive UI

✔ No PHP errors

✔ No JavaScript errors

✔ No duplicated logic

✔ Backward compatibility maintained

---

# Definition of Done

✔ Migration

✔ Model

✔ Factory

✔ Seeder

✔ Relationships

✔ MilestoneService

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

✔ Timeline

✔ Activity Feed

✔ Notification Preparation

✔ Audit Preparation

✔ Breadcrumbs

✔ Feature Tests

✔ Git Ready

---

# Final Instructions

Before generating code

Analyze the existing ERP.

Detect reusable architecture.

Never regenerate completed modules.

Generate ONLY

1. Migration
2. Model
3. Factory
4. Seeder
5. Relationships
6. MilestoneService
7. StoreMilestoneRequest
8. UpdateMilestoneRequest
9. MilestonePolicy
10. MilestoneController
11. Routes
12. Blade Views
13. Dashboard Integration
14. Sidebar Integration
15. Dashboard Widgets
16. Dashboard Charts
17. Project Profile Integration
18. Timeline
19. Activity Feed
20. Notification Preparation
21. Audit Preparation
22. Feature Tests

Provide

- Generated files
- Modified files
- Database changes
- Dashboard changes
- Sidebar changes
- Seeder changes
- Routes added
- Manual artisan commands
- Assumptions made

Stop after Sprint 11.