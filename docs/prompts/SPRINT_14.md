# Creative ERP

# Sprint 14 - Calendar & Meetings

Version: 1.0

Status: Ready For Development

---

# Sprint Goal

Build a centralized Enterprise Calendar & Meetings module.

The Calendar will become the scheduling hub of the ERP.

It must display

• Meetings

• Tasks

• Milestones

• Deadlines

• Company Events

• Personal Events

Future compatible with

• Leave Requests

• Attendance

• Payroll

• CRM

• Inventory

• Finance

Do NOT implement those modules.

---

# Read Before Coding

Read

docs/MASTER_DEVELOPMENT_RULES.md

docs/AI_CONTEXT.md

Read ALL previous Sprint documents.

Read

docs/prompts/SPRINT_14.md

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

Current Sprint

Calendar & Meetings

Next Sprint

Time Tracking

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

Events & Listeners

Maintain backward compatibility.

Controllers remain thin.

Business Logic belongs inside Services.

Validation belongs inside Form Requests.

Authorization belongs inside Policies.

Laravel 12 Best Practices only.

---

# Module Purpose

The Calendar is a unified planning system.

It aggregates data from multiple ERP modules instead of duplicating information.

---

# Database

meetings

Columns

id

uuid

company_id

branch_id

project_id (nullable)

title

description

meeting_type

location

meeting_link

start_at

end_at

timezone

status

created_by

updated_by

timestamps

softDeletes

---

meeting_attendees

Columns

id

meeting_id

user_id

attendance_status

response_at

timestamps

---

# Relationships

Meeting

belongsTo Company

belongsTo Branch

belongsTo Project (optional)

belongsToMany Users (Attendees)

belongsTo Creator

belongsTo Updater

Project

hasMany Meetings

User

belongsToMany Meetings

---

# Calendar Views

Generate

Monthly View

Weekly View

Daily View

Agenda View

Upcoming View

All views must reuse the same service layer.

---

# Event Sources

Display automatically

Meetings

Tasks Due Dates

Milestones

Project Deadlines

Future support

Leave Requests

Invoices

Payments

Inventory Deliveries

Do NOT implement future modules.

---

# Meeting Features

Create Meeting

Edit Meeting

Delete Meeting

Restore Meeting

Invite Attendees

Accept Invitation

Decline Invitation

Tentative

Cancel Meeting

Reschedule Meeting

Online Meeting Link

Location

Notes

---

# Meeting Types

Internal

Client Meeting

Project Meeting

Sales Meeting

HR Meeting

Training

Other

---

# Validation

End time must be after start time.

Prevent overlapping meetings for the same attendee.

Timezone required.

Project optional.

Company required.

Branch required.

---

# Permissions

meeting.view

meeting.create

meeting.update

meeting.delete

meeting.restore

meeting.invite

meeting.cancel

meeting.export

---

# Service

Generate

CalendarService

MeetingService

Responsibilities

CRUD

Conflict Detection

Invitation Handling

Calendar Aggregation

Agenda Generation

Upcoming Events

Business logic belongs ONLY here.

---

# UI

Calendar

Agenda

Meeting Details

Meeting Form

Upcoming Meetings

Today's Schedule

Use FullCalendar.js if compatible with the existing stack.

Otherwise use the existing UI components.

Maintain theme consistency.

---

# Dashboard Preparation

Prepare widgets

Today's Meetings

Upcoming Meetings

Today's Deadlines

Upcoming Milestones

Today's Tasks

Do not recreate the Dashboard.
---

# ERP Integration Requirements

Calendar is the central scheduling system of the ERP.

It must integrate with all existing modules.

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

- Meetings Today
- Upcoming Meetings
- Overdue Tasks
- Upcoming Milestones
- Events This Week
- Schedule Conflicts

Cards must display

- Count
- Quick Action
- Status Indicator
- Responsive Design

---

## Dashboard Widgets

Generate

- Today's Schedule
- Upcoming Meetings
- Upcoming Deadlines
- Upcoming Milestones
- Calendar Mini Widget

Display latest 5 records.

---

## Dashboard Charts

Prepare chart architecture.

Charts

Meetings Per Month

Meetings By Type

Attendance Rate

Tasks Due This Week

Milestones Due This Month

Return placeholder datasets.

Charts will be implemented fully later.

---

# Events & Listeners

Extend the Event architecture.

Generate Events

MeetingCreated

MeetingUpdated

MeetingCancelled

MeetingRescheduled

MeetingInvitationSent

MeetingAccepted

MeetingDeclined

Generate Listeners

LogMeetingActivityListener

RefreshDashboardCalendarListener

NotifyMeetingAttendeesListener

DetectMeetingConflictListener

Prepare architecture for

Queues

Broadcasting

Google Calendar Sync

Microsoft Outlook Sync

Webhooks

Do NOT implement external integrations.

---

# Conflict Detection

Prevent

Double-booking

Overlapping meetings

Invalid meeting duration

Duplicate attendees

Warn user before saving.

---

# Agenda

Generate Agenda View.

Display

Time

Meeting

Attendees

Project

Status

Location

Meeting Link

Notes

Today's Tasks

Today's Milestones

Chronological order.

---

# Project Integration

Add Calendar tab.

Display

Project Meetings

Upcoming Deadlines

Milestones

Tasks

Timeline

Quick Meeting

---

# Task Integration

Display

Due Date

Reminder

Related Meetings

Upcoming Schedule

---

# Milestone Integration

Display

Milestone Deadline

Related Meetings

Upcoming Events

---

# User Profile Integration

Display

My Calendar

Today's Schedule

Upcoming Meetings

Invitations

Pending Responses

---

# Sidebar Integration

Update Sidebar

Projects

    Projects

    Project Teams

    Tasks

    Milestones

Collaboration

    Documents

    Discussions

Planning

    Calendar

    Meetings

Maintain permission-based visibility.

---

# Breadcrumbs

Generate

Dashboard

Dashboard / Calendar

Dashboard / Meetings

Dashboard / Meetings / Create

Dashboard / Meetings / View

Dashboard / Meetings / Edit

---

# Global Search

Register

Meetings

Calendar Events

Search by

Meeting Title

Project

Attendee

Location

Meeting Type

Status

---

# Filters

Company

Branch

Project

Meeting Type

Status

Organizer

Attendee

Date Range

---

# Sorting

Support

Newest

Oldest

Upcoming

Recently Updated

Meeting Type

---

# Pagination

25 records per page.

Preserve filters.

---

# Activity Feed

Log

Meeting Created

Meeting Updated

Meeting Cancelled

Meeting Rescheduled

Invitation Sent

Invitation Accepted

Invitation Declined

Display

User

Action

Meeting

Timestamp

---

# Notifications

Prepare notifications

Meeting Reminder

Invitation Received

Meeting Updated

Meeting Cancelled

Upcoming Meeting

Overdue Reminder

Realtime implementation deferred.

---

# Security

Authorized attendees only.

Private meetings visible only to invited users.

Prevent unauthorized invitation edits.

Policies must protect all meeting actions.

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

meeting.view

meeting.create

meeting.update

meeting.delete

meeting.restore

meeting.invite

meeting.cancel

meeting.export

Assign all permissions to

Super Admin

---

# Seeder

Generate realistic data

Internal Meetings

Client Meetings

Project Meetings

Training Sessions

Random attendees

Random statuses

Future and past meetings

---

# Feature Tests

Generate tests

Create Meeting

Update Meeting

Delete Meeting

Restore Meeting

Invite Attendees

Accept Invitation

Decline Invitation

Conflict Detection

Authorization

Validation

Relationships

Dashboard Integration

Sidebar Visibility

Project Integration

Task Integration

Milestone Integration

User Calendar

Search

Filters

Sorting

Pagination

Agenda View

---

# Performance

Avoid N+1 queries.

Use eager loading.

Lazy-load calendar events.

Reuse Blade Components.

Reuse Dashboard Widgets.

Reuse existing Services.

Do not duplicate logic.

---

# API Preparation

Prepare API Resources.

Do NOT generate API Controllers.

Prepare future sync architecture for

Google Calendar

Microsoft Outlook

Apple Calendar

---

# Acceptance Criteria

Sprint is complete only if

✔ Migration succeeds

✔ Seeder succeeds

✔ Calendar loads

✔ Meetings CRUD works

✔ Invitations work

✔ Conflict detection works

✔ Dashboard updated

✔ Sidebar updated

✔ Project integration works

✔ Task integration works

✔ Milestone integration works

✔ User calendar works

✔ Search works

✔ Filters work

✔ Sorting works

✔ Pagination works

✔ Permission Seeder updated

✔ Policies work

✔ Events work

✔ Listeners work

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

✔ CalendarService

✔ MeetingService

✔ Requests

✔ Policy

✔ Controller

✔ Routes

✔ Blade Views

✔ Dashboard Integration

✔ Sidebar Integration

✔ Calendar Views

✔ Project Integration

✔ Task Integration

✔ Milestone Integration

✔ User Integration

✔ Events

✔ Listeners

✔ Agenda

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
6. CalendarService
7. MeetingService
8. StoreMeetingRequest
9. UpdateMeetingRequest
10. MeetingPolicy
11. MeetingController
12. Events
13. Listeners
14. Routes
15. Blade Views
16. Dashboard Integration
17. Sidebar Integration
18. Dashboard Widgets
19. Dashboard Charts
20. Calendar Views
21. Project Integration
22. Task Integration
23. Milestone Integration
24. User Integration
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

Stop after Sprint 14.