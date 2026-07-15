# Creative ERP

# Sprint 10 - Tasks Management

Version: 1.0

Status: Ready For Development

---

# Sprint Goal

Build a complete Enterprise Task Management module.

This module allows organizations to plan, assign, execute, monitor, and complete work within Projects.

Tasks are the operational unit of work inside every Project.

This module will become the foundation for

- Time Tracking
- Project Progress
- Milestones
- Documents
- Discussions
- Issue Tracking
- Approvals
- Reports

This is NOT a simple CRUD.

It is the execution engine of Creative ERP.

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

docs/prompts/SPRINT_09.md

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

✅ Project Teams

Current Sprint

Tasks

Next Sprint

Milestones

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

Maintain backward compatibility.

Controllers remain thin.

Business Logic belongs inside Services.

Validation belongs inside Form Requests.

Authorization belongs inside Policies.

Follow Laravel 12 Best Practices.

Never duplicate code.

---

# Module Purpose

A Task represents a unit of work inside a Project.

Every Task belongs to one Project.

Every Task is assigned to one Project Team Member.

A Task contributes to Project Progress.

Tasks will later connect to

- Time Entries
- Documents
- Comments
- Approvals
- Meetings
- Expenses

Prepare the architecture now.

Do NOT generate these modules.

---

# Features

The module must support

- Tasks List

- Task Details

- Create Task

- Edit Task

- Duplicate Task

- Archive Task

- Restore Task

- Delete Task (Soft Delete)

- Reassign Task

- Search

- Filters

- Pagination

- Export Preparation

---

# Database

Create table

tasks

Columns

id

uuid

project_id

project_member_id

parent_task_id

task_code

title

description

priority

status

progress

estimated_hours

actual_hours

start_date

due_date

completed_at

color

sort_order

is_billable

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

Task

belongsTo Project

belongsTo ProjectMember

belongsTo Parent Task

hasMany Child Tasks

belongsTo Creator

belongsTo Updater

Prepare future relationships

Time Entries

Documents

Comments

Approvals

Attachments

Checklists

Subtasks

Do NOT generate them.

---

# Business Rules

Every Task belongs to one Project.

Every Task belongs to one active Project Team Member.

Tasks cannot be assigned to inactive members.

Completed Tasks become read-only except for Project Managers and Super Admin.

Task Progress

Minimum

0

Maximum

100

Completed Tasks automatically set

Progress

100%

Completed Date

Current Date

A Task may optionally belong to another Task.

Prevent circular parent-child relationships.

---

# Task Status

Allowed values

Backlog

To Do

In Progress

Under Review

Blocked

Completed

Cancelled

---

# Task Priority

Allowed values

Low

Medium

High

Critical

---

# Validation

Project

Required

Project Member

Required

Title

Required

Maximum 255

Task Code

Required

Unique within Project

Description

Nullable

Priority

Required

Status

Required

Progress

Integer

Minimum 0

Maximum 100

Estimated Hours

Nullable

Numeric

Minimum 0

Actual Hours

Nullable

Numeric

Minimum 0

Start Date

Required

Due Date

Nullable

Completed Date

Nullable

Parent Task

Nullable

Notes

Nullable

---

# Permissions

task.view

task.create

task.update

task.delete

task.restore

task.archive

task.assign

task.reassign

task.complete

task.export

task.import

---

# Service

Generate

TaskService

Responsibilities

Create Task

Update Task

Assign Task

Reassign Task

Complete Task

Archive Task

Restore Task

Duplicate Task

Validate Project Rules

Update Project Progress

Prevent Invalid Assignments

Business logic belongs ONLY here.

Controllers remain thin.

---

# UI Pages

Generate

Tasks List

Create Task

Edit Task

Task Details

Task Timeline

Task Activity

---

# Task Form

Fields

Project

Project Team Member

Parent Task

Task Code

Title

Description

Priority

Status

Estimated Hours

Actual Hours

Start Date

Due Date

Color

Billable

Notes

Dynamic Dropdowns

Company

↓

Branch

↓

Project

↓

Project Team Member

↓

Parent Task

Use Alpine.js.

No page reload.

---

# Tasks Table

Columns

Task Code

Title

Project

Assigned To

Priority

Status

Progress

Estimated Hours

Due Date

Actions

Actions

View

Edit

Complete

Duplicate

Archive

Restore
---

# ERP Integration Requirements

The Tasks module is a core operational module.

It must integrate with every completed module without breaking existing functionality.

Do NOT regenerate existing modules.

Reuse the existing architecture.

Maintain backward compatibility.

---

# Dashboard Integration

Extend the existing Dashboard.

Do NOT recreate it.

---

## Statistics Cards

Add Dashboard cards

- Total Tasks
- Active Tasks
- Completed Tasks
- Overdue Tasks
- My Tasks
- Tasks Due Today
- Tasks Due This Week
- Critical Tasks

Cards must display

- Count
- Quick Action
- Status Indicator
- Responsive Design

---

## Dashboard Widgets

Generate

- My Assigned Tasks
- Recently Created Tasks
- Recently Completed Tasks
- Overdue Tasks
- Upcoming Deadlines
- Tasks Waiting Review

Display latest 5 records.

---

## Dashboard Charts

Prepare chart architecture.

Charts

Tasks By Status

Tasks By Priority

Tasks Per Project

Tasks Completed Per Month

Tasks Created Per Month

Project Progress

Return placeholder datasets.

Charts will be fully implemented later.

---

# Kanban Preparation

Prepare architecture for future Kanban Board.

Statuses

Backlog

To Do

In Progress

Under Review

Blocked

Completed

Cancelled

Prepare ordering using

sort_order

Do NOT implement drag-and-drop.

Prepare architecture only.

---

# Gantt Preparation

Prepare architecture for future Gantt Chart.

Required fields already exist

Start Date

Due Date

Parent Task

Progress

Dependencies (future)

No Gantt implementation.

---

# Task Timeline

Generate Timeline page.

Display

Created

Assigned

Updated

Status Changed

Completed

Archived

Restored

Chronological order.

---

# Task Activity

Track

Created

Updated

Assigned

Reassigned

Completed

Archived

Restored

Priority Changed

Status Changed

Estimate Changed

---

# Project Profile Integration

Update Project Profile.

Replace

Tasks (Coming Soon)

with

Tasks

Display

Task Statistics

Open Tasks

Completed Tasks

Overdue Tasks

Progress

Task List

Quick Create

Quick Search

Quick Filters

---

# Task Detail Page

Display

General Information

Assignment

Timeline

Project

Priority

Progress

Estimated Hours

Actual Hours

Activity

Future Tabs

Comments

Attachments

Time Entries

Approvals

Checklist

Meeting Links

Display placeholders only.

---

# Sidebar Integration

Update Sidebar.

Projects

Projects

Project Teams

Tasks

Maintain permission-based visibility.

---

# Breadcrumbs

Generate

Dashboard

Dashboard / Tasks

Dashboard / Tasks / Create

Dashboard / Tasks / Edit

Dashboard / Tasks / View

Dashboard / Projects / Tasks

---

# Global Search

Register Tasks.

Search by

Task Code

Title

Project

Assigned User

Priority

Status

---

# Filters

Company

Branch

Project

Assigned User

Priority

Status

Progress

Due Date

Start Date

Created Date

---

# Sorting

Support sorting

Task Code

Title

Priority

Status

Progress

Due Date

Assigned User

Newest

Oldest

---

# Pagination

25 records per page.

Preserve filters.

---

# Activity Feed

Log

Task Created

Task Updated

Task Assigned

Task Reassigned

Task Completed

Task Archived

Task Restored

Priority Changed

Status Changed

Display

User

Task

Project

Timestamp

---

# Notifications

Prepare notifications.

Task Assigned

Task Reassigned

Task Completed

Task Due Soon

Task Overdue

Status Changed

Priority Changed

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

task.view

task.create

task.update

task.delete

task.restore

task.archive

task.assign

task.reassign

task.complete

task.export

task.import

Assign all permissions to

Super Admin

---

# Seeder

Generate realistic sample Tasks.

Each Project

15–40 Tasks

Random

Priority

Status

Estimated Hours

Progress

Assignment

Parent Tasks

Completion Dates

Use existing Projects.

Use existing Team Members.

---

# Feature Tests

Generate tests

Create Task

Update Task

Complete Task

Archive Task

Restore Task

Duplicate Task

Assignment

Reassignment

Authorization

Validation

Relationships

Dashboard Integration

Sidebar Visibility

Project Integration

Search

Filters

Pagination

Duplicate Task Code Prevention

Completed Task Rules

Inactive Member Assignment Prevention

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

Prepare Resources.

Do NOT generate API Controllers.

---

# Acceptance Criteria

Sprint is complete only if

✔ Migration succeeds

✔ Seeder succeeds

✔ Relationships work

✔ CRUD works

✔ Assignment works

✔ Reassignment works

✔ Dashboard updated

✔ Sidebar updated

✔ Project Profile updated

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

✔ TaskService

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

✔ Kanban Preparation

✔ Gantt Preparation

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
6. TaskService
7. StoreTaskRequest
8. UpdateTaskRequest
9. TaskPolicy
10. TaskController
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

Stop after Sprint 10.