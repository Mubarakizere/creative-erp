# Creative ERP

# Sprint 13 - Comments, Discussions & Collaboration

Version: 1.0

Status: Ready For Development

---

# Sprint Goal

Build a complete Enterprise Collaboration module.

This module enables discussions across all ERP records using a unified architecture.

Comments are polymorphic and can belong to

- Projects
- Tasks
- Milestones
- Documents

Future compatible with

- Clients
- Companies
- Inventory
- HR
- Finance
- CRM
- Purchase Orders
- Sales Orders

Do NOT implement those modules.

---

# Read Before Coding

Read

docs/MASTER_DEVELOPMENT_RULES.md

docs/AI_CONTEXT.md

Read ALL previous Sprint documents.

Read

docs/prompts/SPRINT_13.md

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

Current Sprint

Comments & Discussions

Next Sprint

Calendar & Meetings

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

Maintain backward compatibility.

Controllers remain thin.

Business Logic belongs inside Services.

Validation belongs inside Form Requests.

Authorization belongs inside Policies.

Laravel 12 Best Practices only.

Never duplicate code.

---

# Module Purpose

Comments are shared ERP resources.

Use Laravel Polymorphic Relationships.

One comments table.

Never create multiple comment tables.

---

# Database

comments

Columns

id

uuid

commentable_type

commentable_id

parent_id

user_id

body

is_internal

is_pinned

edited_at

created_by

updated_by

timestamps

softDeletes

---

# Relationships

Comment

morphTo commentable

belongsTo User

belongsTo Parent

hasMany Replies

Add morphMany(Comment::class)

to

Project

Task

Milestone

Document

Prepare architecture for

Client

Company

Invoice

Inventory

HR

CRM

Do NOT implement them.

---

# Business Rules

Every Comment belongs to one ERP record.

Support unlimited replies.

Support nested discussions.

Soft Delete only.

Pinned comments appear first.

Internal comments are only visible to authorized users.

Edited comments display edit timestamp.

---

# Rich Text

Support

Bold

Italic

Underline

Lists

Links

Code Block

Block Quote

Sanitize HTML before saving.

Prevent XSS.

---

# Mentions

Support

@username

Detect automatically.

Prepare notification architecture.

Realtime implementation deferred.

---

# Features

Comments List

Create Comment

Edit Comment

Delete Comment

Restore Comment

Reply

Nested Replies

Pin Comment

Unpin Comment

Internal Note

Search

Pagination

Export Preparation

---

# Permissions

comment.view

comment.create

comment.update

comment.delete

comment.restore

comment.pin

comment.reply

comment.internal

comment.export

---

# Service

Generate

CommentService

Responsibilities

Create

Update

Delete

Restore

Reply

Pin

Unpin

Mention Detection

Permission Validation

Business Logic belongs ONLY here.

Controllers remain thin.

---

# UI Pages

Discussion Panel

Comment Thread

Reply Form

Pinned Comments

Recent Discussions

My Mentions

---

# Comment Form

Fields

Comment

Internal Note

Mention Support

Reply

Pin

Rich Text Editor

Use Alpine.js.

Reuse existing components.

No page reload.

---

# Comments Table

Columns

User

Comment

Module

Replies

Pinned

Internal

Created

Actions

Actions

View

Reply

Edit

Delete

Restore

Pin

Unpin
---

# ERP Integration Requirements

Comments are a shared collaboration resource.

They must integrate seamlessly with every existing ERP module.

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

- Total Discussions
- Comments Today
- My Mentions
- Unread Discussions
- Internal Notes
- Active Threads

Cards must display

- Count
- Quick Action
- Status Indicator
- Responsive Design

---

## Dashboard Widgets

Generate

- Recent Discussions
- My Mentions
- Latest Replies
- Active Conversations
- Recently Pinned Discussions

Display latest 5 records.

---

## Dashboard Charts

Prepare chart architecture.

Charts

Comments Per Module

Comments Per User

Daily Discussions

Monthly Discussions

Mentions Per Month

Return placeholder datasets.

Charts will be fully implemented later.

---

# Events & Listeners

Introduce Laravel Events and Listeners architecture.

Generate Events

CommentCreated

CommentUpdated

CommentDeleted

CommentRestored

CommentPinned

CommentUnpinned

MentionDetected

Generate Listeners

LogActivityListener

RefreshDashboardListener

NotifyMentionedUsersListener

UpdateDiscussionStatisticsListener

Prepare architecture for

Webhooks

Broadcasting

Realtime Notifications

Queues

Do NOT implement broadcasting.

Use Events instead of tightly coupling services.

---

# Discussion Timeline

Generate Discussion Timeline.

Display

Comment Created

Comment Edited

Reply Added

Comment Pinned

Comment Unpinned

Comment Deleted

Comment Restored

Mention Added

Chronological order.

---

# Mention Detection

Support

@username

Automatically detect mentions.

Validate mentioned users exist.

Ignore duplicate mentions.

Prepare notification dispatch.

---

# Project Integration

Add Discussions tab.

Replace

Discussions (Coming Soon)

with

Discussions

Display

Discussion Thread

Pinned Comments

Recent Replies

Quick Comment

Search

Filters

---

# Task Integration

Add Discussions tab.

Display

Task Discussion

Replies

Mentions

Pinned Notes

Internal Notes

---

# Milestone Integration

Add Discussions tab.

Display

Milestone Discussion

Replies

Internal Notes

Pinned Items

---

# Document Integration

Add Discussions tab.

Display

Document Discussion

Replies

Internal Notes

Pinned Items

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

Maintain permission-based visibility.

---

# Breadcrumbs

Generate

Dashboard

Dashboard / Discussions

Dashboard / Discussions / View

Dashboard / Discussions / Thread

---

# Global Search

Register Discussions.

Search by

Comment

Author

Mention

Project

Task

Milestone

Document

---

# Filters

Company

Branch

Project

Author

Pinned

Internal

Date

Module

---

# Sorting

Support

Newest

Oldest

Most Replies

Pinned

Recently Updated

---

# Pagination

25 records per page.

Preserve filters.

---

# Activity Feed

Log

Comment Created

Comment Updated

Comment Deleted

Comment Restored

Reply Added

Comment Pinned

Comment Unpinned

Mention Added

Display

User

Comment

Module

Timestamp

---

# Notifications

Prepare notifications

Mention Received

Reply Received

Comment Pinned

Discussion Updated

Internal Note Added

Realtime implementation deferred.

---

# Security

Internal Notes

Visible only to authorized users.

Deleted comments remain available for audit.

Sanitize HTML.

Prevent XSS.

Prevent unauthorized edits.

Only author or privileged users may edit/delete.

---

# Audit Logs

Prepare architecture.

Capture

Old Content

New Content

User

IP Address

Timestamp

Action

---

# Permission Seeder

Update RolesAndPermissionsSeeder.

Register

comment.view

comment.create

comment.update

comment.delete

comment.restore

comment.pin

comment.reply

comment.internal

comment.export

Assign all permissions to

Super Admin

---

# Seeder

Generate realistic discussion data.

Projects

Tasks

Milestones

Documents

Random

Replies

Mentions

Pinned Comments

Internal Notes

Edited Comments

---

# Feature Tests

Generate tests

Create Comment

Reply

Edit

Delete

Restore

Pin

Unpin

Mention Detection

Authorization

Validation

Relationships

Dashboard Integration

Sidebar Visibility

Project Integration

Task Integration

Milestone Integration

Document Integration

Search

Filters

Sorting

Pagination

Nested Replies

Internal Visibility

---

# Performance

Avoid N+1 queries.

Use eager loading.

Lazy-load replies.

Reuse existing Blade Components.

Reuse Dashboard Widgets.

Reuse existing Services.

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

✔ Nested Replies work

✔ Mentions detected

✔ Pin/Unpin works

✔ Internal Notes work

✔ Dashboard updated

✔ Sidebar updated

✔ Project integration works

✔ Task integration works

✔ Milestone integration works

✔ Document integration works

✔ Events & Listeners work

✔ Activity Feed updated

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

✔ CommentService

✔ Requests

✔ Policy

✔ Controller

✔ Routes

✔ Blade Views

✔ Dashboard Integration

✔ Sidebar Integration

✔ Project Integration

✔ Task Integration

✔ Milestone Integration

✔ Document Integration

✔ Events

✔ Listeners

✔ Timeline

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

1. Migration
2. Model
3. Factory
4. Seeder
5. Relationships
6. CommentService
7. StoreCommentRequest
8. UpdateCommentRequest
9. CommentPolicy
10. CommentController
11. Events
12. Listeners
13. Routes
14. Blade Views
15. Dashboard Integration
16. Sidebar Integration
17. Dashboard Widgets
18. Dashboard Charts
19. Project Integration
20. Task Integration
21. Milestone Integration
22. Document Integration
23. Mention Detection
24. Timeline
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
- Events created
- Listeners created
- Seeder changes
- Routes added
- Manual artisan commands
- Assumptions made

Stop after Sprint 13.