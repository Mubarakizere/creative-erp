# Creative ERP

# Sprint 15 - Time Tracking & Timesheets

Version: 1.0

Status: Ready For Development

---

# Sprint Goal

Build a professional enterprise Time Tracking system that allows users to log work against Projects and Tasks.

This module will support:

- Manual time entries
- Running timers
- Timesheets
- Billable and non-billable hours
- Approval-ready architecture
- Reporting-ready architecture

Future compatible with

- Payroll
- Client Billing
- Invoicing
- Attendance
- Productivity Analytics

Do NOT implement those modules.

---

# Read Before Coding

Read

docs/MASTER_DEVELOPMENT_RULES.md

docs/AI_CONTEXT.md

Read all previous Sprint documents.

Read

docs/prompts/SPRINT_15.md

---

# Current ERP Status

Completed

✔ Authentication

✔ Dashboard

✔ Companies

✔ Branches

✔ Departments

✔ Roles & Permissions

✔ Users

✔ Clients

✔ Projects

✔ Project Teams

✔ Tasks

✔ Milestones

✔ Documents

✔ Discussions

✔ Calendar

✔ Meetings

Current Sprint

Time Tracking

Next Sprint

Workflow & Approvals

---

# Important Rules

Do NOT regenerate completed modules.

Reuse

Dashboard

Sidebar

Navigation

Blade Components

Layouts

Services

Policies

Requests

Activity Feed

Notifications

Events

Listeners

Maintain backward compatibility.

Controllers remain thin.

Business Logic belongs inside Services.

Validation belongs inside Form Requests.

Authorization belongs inside Policies.

Laravel 12 Best Practices only.

---

# Database

time_entries

Columns

id

uuid

company_id

branch_id

project_id

task_id (nullable)

user_id

description

start_time

end_time

duration_minutes

billable

hourly_rate

status

created_by

updated_by

timestamps

softDeletes

---

# Relationships

TimeEntry

belongsTo Company

belongsTo Branch

belongsTo Project

belongsTo Task

belongsTo User

belongsTo Creator

belongsTo Updater

Project

hasMany TimeEntries

Task

hasMany TimeEntries

User

hasMany TimeEntries

---

# Features

Manual Entry

Start Timer

Stop Timer

Pause Timer

Resume Timer

Daily Timesheet

Weekly Timesheet

Monthly Timesheet

Billable Hours

Non-Billable Hours

Time Summary

Export Preparation

---

# Validation

End time must be after start time.

No overlapping running timers for the same user.

Project required.

Task optional.

Duration calculated automatically.

---

# Permissions

time.view

time.create

time.update

time.delete

time.restore

time.export

time.approve (future)

---

# Services

Generate

TimeTrackingService

TimerService

Responsibilities

CRUD

Timer logic

Duration calculation

Conflict detection

Summary generation

Business logic belongs ONLY here.

---

# UI

Time Entries

Running Timer

My Timesheet

Project Timesheet

User Timesheet

Summary Cards

Use Alpine.js for timer updates.

Reuse existing components.
---

# ERP Integration Requirements

Time Tracking is a core ERP module.

It must integrate seamlessly with Projects, Tasks, Calendar, Meetings, Dashboard, Reports, and future Payroll & Billing.

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

- Hours Logged Today
- Hours Logged This Week
- Billable Hours
- Non-Billable Hours
- Active Timers
- Users Currently Tracking Time

Cards must display

- Count
- Quick Action
- Status Indicator
- Responsive Design

---

## Dashboard Widgets

Generate

- Running Timers
- My Timesheet Today
- Weekly Summary
- Top Active Projects
- Team Activity

Display latest 5 records.

---

## Dashboard Charts

Prepare chart architecture.

Charts

Hours Per Day

Hours Per Week

Hours Per User

Billable vs Non-Billable

Project Hours

Task Hours

Return datasets from real database queries.

---

# Events & Listeners

Extend the Event architecture.

Generate Events

TimeEntryCreated

TimeEntryUpdated

TimeEntryDeleted

TimerStarted

TimerPaused

TimerResumed

TimerStopped

Generate Listeners

LogTimeActivityListener

RefreshDashboardTimeListener

UpdateProjectHoursListener

UpdateTaskHoursListener

Register all events and listeners.

Prepare architecture for

Queues

Broadcasting

Payroll

Client Billing

Invoices

Do NOT implement external integrations.

---

# Timer Rules

A user may only have ONE running timer.

Prevent duplicate running timers.

Automatically calculate duration.

Pause does not create a new entry.

Resume continues the same entry.

Stopping finalizes the entry.

---

# Calendar Integration

Display

Tracked Time

Meeting Duration

Task Duration

Project Work Sessions

Calendar must display time entries.

---

# Project Integration

Add Time Tracking tab.

Display

Project Hours

Billable Hours

Non-Billable Hours

Team Hours

Recent Entries

Running Timers

Summary Cards

---

# Task Integration

Display

Tracked Hours

Assigned User Hours

Running Timer

Recent Entries

---

# User Profile Integration

Display

My Timesheet

Today's Hours

Weekly Hours

Monthly Hours

Running Timer

Time Statistics

---

# Reports

Prepare

Daily Report

Weekly Report

Monthly Report

Project Report

Task Report

User Report

Billable Report

Non-Billable Report

Export architecture only.

---

# Sidebar Integration

Update Sidebar

Planning

Calendar

Meetings

Productivity

Time Tracking

Timesheets

Maintain permission visibility.

---

# Breadcrumbs

Generate

Dashboard

Dashboard / Time Tracking

Dashboard / Time Entries

Dashboard / Timesheets

Dashboard / Reports

---

# Global Search

Register

Time Entries

Projects by Hours

Users by Hours

Tasks by Hours

Search by

Description

Project

Task

User

Duration

Date

---

# Filters

Company

Branch

Project

Task

User

Billable

Status

Date Range

---

# Sorting

Support

Newest

Oldest

Longest Duration

Shortest Duration

Most Recent

Billable First

---

# Pagination

25 records per page.

Preserve filters.

---

# Activity Feed

Log

Timer Started

Timer Paused

Timer Resumed

Timer Stopped

Time Entry Created

Time Entry Updated

Time Entry Deleted

Display

User

Action

Duration

Timestamp

---

# Notifications

Prepare notifications

Timer Running Too Long

Weekly Timesheet Reminder

Missing Time Entry

Overtime Warning

Approval Needed (future)

Realtime implementation deferred.

---

# Security

Users may edit only their own time entries unless authorized.

Managers may view team time.

Super Admin has full access.

Policies must protect all actions.

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

time.view

time.create

time.update

time.delete

time.restore

time.export

time.approve

Assign all permissions to

Super Admin

---

# Seeder

Generate realistic data

Time Entries

Running Timers

Completed Timers

Billable Work

Internal Work

Random Durations

Random Projects

Random Tasks

---

# Feature Tests

Generate tests

Create Entry

Update Entry

Delete Entry

Restore Entry

Start Timer

Pause Timer

Resume Timer

Stop Timer

Duration Calculation

Conflict Detection

Authorization

Validation

Relationships

Dashboard Integration

Sidebar Visibility

Project Integration

Task Integration

User Integration

Calendar Integration

Reports

Search

Filters

Sorting

Pagination

---

# Performance

Avoid N+1 queries.

Use eager loading.

Optimize dashboard queries.

Optimize reports.

Reuse Blade Components.

Reuse existing Services.

Do not duplicate logic.

---

# API Preparation

Prepare API Resources.

Do NOT generate API Controllers.

Prepare future integrations for

Payroll

Client Billing

Invoices

Reporting

---

# Acceptance Criteria

Sprint is complete only if

✔ Migration succeeds

✔ Seeder succeeds

✔ CRUD works

✔ Running timer works

✔ Pause works

✔ Resume works

✔ Stop works

✔ Duration calculated automatically

✔ Dashboard updated

✔ Sidebar updated

✔ Calendar integration works

✔ Project integration works

✔ Task integration works

✔ User integration works

✔ Reports prepared

✔ Search works

✔ Filters work

✔ Sorting works

✔ Pagination works

✔ Events work

✔ Listeners work

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

✔ TimeTrackingService

✔ TimerService

✔ Requests

✔ Policy

✔ Controller

✔ Routes

✔ Blade Views

✔ Dashboard Integration

✔ Sidebar Integration

✔ Calendar Integration

✔ Project Integration

✔ Task Integration

✔ User Integration

✔ Reports

✔ Events

✔ Listeners

✔ Activity Feed

✔ Notification Preparation

✔ Audit Preparation

✔ Global Search

✔ Feature Tests

✔ Git Ready

---

# Final Instructions

Before generating code

Analyze the existing ERP.

Detect reusable architecture.

Never regenerate completed modules.

Generate ONLY

1. Migrations
2. Models
3. Factories
4. Seeders
5. Relationships
6. TimeTrackingService
7. TimerService
8. StoreTimeEntryRequest
9. UpdateTimeEntryRequest
10. TimeEntryPolicy
11. TimeEntryController
12. Events
13. Listeners
14. Routes
15. Blade Views
16. Dashboard Integration
17. Sidebar Integration
18. Dashboard Widgets
19. Dashboard Charts
20. Calendar Integration
21. Project Integration
22. Task Integration
23. User Integration
24. Reports
25. Activity Feed
26. Notification Preparation
27. Audit Preparation
28. Feature Tests

Provide

- Generated files
- Modified files
- Database changes
- Dashboard changes
- Sidebar changes
- Calendar integrations
- Events created
- Listeners created
- Seeder changes
- Routes added
- Manual Artisan commands
- Assumptions made

Stop after Sprint 15.