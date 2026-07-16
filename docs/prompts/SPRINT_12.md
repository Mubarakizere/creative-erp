# Creative ERP

# Sprint 12 - Documents & File Management

Version 1.0

Status Ready For Development

---

# Sprint Goal

Build a complete Enterprise Document Management System.

This module provides centralized file management across the ERP.

Documents may belong to

• Companies

• Branches

• Departments

• Clients

• Projects

• Tasks

• Milestones

Future compatibility

• Inventory

• Finance

• HR

• CRM

• Purchase Orders

• Sales Orders

Do NOT generate those modules.

---

# Read Before Coding

Read

docs/MASTER_DEVELOPMENT_RULES.md

docs/AI_CONTEXT.md

All previous Sprint documents.

---

# Current ERP Status

Completed

Authentication

Dashboard

Companies

Branches

Departments

Roles & Permissions

Users

Clients

Projects

Project Teams

Tasks

Milestones

Current Sprint

Documents

Next Sprint

Comments & Activity

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

Maintain backward compatibility.

Controllers remain thin.

Business Logic belongs inside Services.

Validation belongs inside Form Requests.

Authorization belongs inside Policies.

Laravel 12 Best Practices only.

---

# Module Purpose

Documents are shared resources.

Every document belongs to one ERP record.

Use Laravel Polymorphic Relationships.

Do NOT create multiple document tables.

One table only.

---

# Database

documents

Columns

id

uuid

documentable_type

documentable_id

folder

file_name

original_name

extension

mime_type

size

disk

path

version

visibility

description

uploaded_by

created_by

updated_by

timestamps

softDeletes

---

# Relationships

Document

morphTo documentable

belongsTo uploader

belongsTo creator

belongsTo updater

Add morphMany(Document::class)

to

Company

Branch

Department

Client

Project

Task

Milestone

---

# Storage

Use Laravel Storage.

Support

local

public

s3 (future)

Do not hardcode disks.

---

# Supported File Types

Images

jpg

jpeg

png

gif

webp

svg

PDF

pdf

Office

doc

docx

xls

xlsx

ppt

pptx

Archives

zip

rar

7z

Video

mp4

mov

avi

Audio

mp3

wav

Maximum upload

100 MB

Configurable later.

---

# Features

Documents List

Upload

Preview

Download

Rename

Replace

Versioning

Soft Delete

Restore

Search

Filters

Pagination

Export Preparation

---

# Validation

Allowed Mime Types

Required

Maximum Size

100 MB

Description

Nullable

Visibility

Private

Internal

Public

Folder

Nullable

Version

Automatic

---

# Permissions

document.view

document.create

document.update

document.delete

document.restore

document.download

document.upload

document.replace

document.export

document.import

---

# Service

Generate

DocumentService

Responsibilities

Upload

Replace

Delete

Restore

Rename

Version Control

Generate Preview URLs

Generate Download URLs

Validate File Type

Validate Size

Business Logic belongs ONLY here.

Controllers remain thin.

---

# UI Pages

Documents List

Upload Document

View Document

Edit Metadata

Document Details
---

# ERP Integration Requirements

Documents are a shared ERP resource.

They must integrate seamlessly with every existing module.

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

- Total Documents
- Uploaded Today
- Storage Used
- Recently Updated Documents
- Documents Pending Review
- Public Documents

Cards must display

- Count
- Quick Action
- Status Indicator
- Responsive Design

---

## Dashboard Widgets

Generate

- Recent Documents
- Recently Uploaded
- Largest Files
- Most Downloaded
- Recently Updated

Display latest 5 records.

---

## Dashboard Charts

Prepare chart architecture.

Charts

Documents Per Module

Documents By File Type

Monthly Uploads

Storage Usage

Documents By Visibility

Return placeholder datasets.

Charts will be implemented later.

---

# Document Versioning

Support document version history.

Every replacement creates

Version 1

Version 2

Version 3

...

Display

Current Version

Previous Versions

Uploaded By

Upload Date

Allow restoring previous versions in future.

Prepare architecture only.

---

# Document Preview

Support preview for

Images

PDF

Text

Office files (future)

Videos

Audio

Generate secure preview URLs.

---

# Project Integration

Add Documents tab.

Replace

Documents (Coming Soon)

with

Documents

Display

Upload Button

Recent Documents

Preview

Download

Delete

Search

Filters

---

# Task Integration

Add Documents tab.

Display

Task Attachments

Upload

Preview

Download

Delete

---

# Milestone Integration

Add Documents tab.

Display

Milestone Files

Upload

Preview

Download

Delete

---

# Client Integration

Add Documents section.

Examples

Contracts

Identification

Invoices (future)

Agreements

---

# Company Integration

Support

Company Logo

Tax Certificate

Registration Certificate

Business License

Brand Assets

---

# Sidebar Integration

Update Sidebar

Projects

    Projects

    Project Teams

    Tasks

    Milestones

Documents

Maintain permission-based visibility.

---

# Breadcrumbs

Generate

Dashboard

Dashboard / Documents

Dashboard / Documents / Upload

Dashboard / Documents / View

Dashboard / Documents / Edit

---

# Global Search

Register Documents.

Search by

Original Name

File Name

Project

Client

Task

Milestone

Uploader

---

# Filters

Company

Branch

Module

Extension

Mime Type

Visibility

Uploader

Upload Date

---

# Sorting

Support

Newest

Oldest

Largest

Smallest

Alphabetical

Recently Downloaded

---

# Pagination

25 records per page.

Preserve filters.

---

# Activity Feed

Log

Document Uploaded

Document Updated

Document Replaced

Document Downloaded

Document Deleted

Document Restored

Visibility Changed

Display

User

Document

Module

Timestamp

---

# Notifications

Prepare notifications

Document Uploaded

Document Replaced

Document Shared

Document Deleted

Document Restored

Realtime implementation deferred.

---

# Security

Support

Private

Internal

Public

Private

Visible only to authorized users.

Internal

Visible to organization members.

Public

Accessible via secure public URL.

Prepare signed URL support.

---

# Audit Logs

Prepare architecture.

Capture

Old Metadata

New Metadata

User

IP Address

Timestamp

Action

---

# Permission Seeder

Update RolesAndPermissionsSeeder.

Register

document.view

document.create

document.update

document.delete

document.restore

document.upload

document.download

document.replace

document.export

document.import

Assign all permissions to

Super Admin

---

# Seeder

Generate realistic sample documents.

Attach documents to

Companies

Clients

Projects

Tasks

Milestones

Randomize

Type

Size

Visibility

Uploader

Version

---

# Feature Tests

Generate tests

Upload

Replace

Download

Delete

Restore

Preview

Validation

Authorization

Relationships

Dashboard Integration

Sidebar Visibility

Project Integration

Task Integration

Milestone Integration

Client Integration

Search

Filters

Sorting

Pagination

Version Increment

Mime Validation

Maximum Size Validation

---

# Performance

Avoid N+1 queries.

Use eager loading.

Lazy-load previews.

Reuse Blade Components.

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

✔ Upload works

✔ Replace works

✔ Download works

✔ Preview works

✔ Dashboard updated

✔ Sidebar updated

✔ Project integration works

✔ Task integration works

✔ Milestone integration works

✔ Client integration works

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

✔ DocumentService

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

✔ Client Integration

✔ Preview

✔ Versioning Preparation

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
6. DocumentService
7. StoreDocumentRequest
8. UpdateDocumentRequest
9. DocumentPolicy
10. DocumentController
11. Routes
12. Blade Views
13. Dashboard Integration
14. Sidebar Integration
15. Dashboard Widgets
16. Dashboard Charts
17. Project Integration
18. Task Integration
19. Milestone Integration
20. Client Integration
21. Company Integration
22. Preview Support
23. Versioning Preparation
24. Activity Feed
25. Notification Preparation
26. Audit Preparation
27. Feature Tests

Provide

- Generated files
- Modified files
- Database changes
- Dashboard changes
- Sidebar changes
- Seeder changes
- Routes added
- Storage configuration changes
- Manual artisan commands
- Assumptions made

Stop after Sprint 12.