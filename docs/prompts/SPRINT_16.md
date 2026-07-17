# Creative ERP

# Sprint 16 - Workflow & Approvals Engine

Version: 1.0

Status: Ready For Development

---

# Sprint Goal

Build a reusable Enterprise Workflow & Approval Engine.

This module will provide a generic approval system that can be attached to any ERP record.

Initial supported modules

- Time Entries
- Documents
- Meetings

Future compatible with

- Expenses
- Invoices
- Purchase Orders
- Leave Requests
- Payroll
- Contracts
- Inventory Adjustments
- Asset Requests
- CRM Deals

Do NOT implement those modules.

---

# Read Before Coding

Read

docs/MASTER_DEVELOPMENT_RULES.md

docs/AI_CONTEXT.md

Read all previous Sprint documents.

Read

docs/prompts/SPRINT_16.md

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

✔ Time Tracking

✔ Metrics Layer

Current Sprint

Workflow & Approvals

Next Sprint

Notification Center

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

MetricsService

Maintain backward compatibility.

Controllers remain thin.

Business Logic belongs inside Services.

Validation belongs inside Form Requests.

Authorization belongs inside Policies.

Laravel 12 Best Practices only.

---

# Module Purpose

Create a generic approval engine using Laravel polymorphic relationships.

One approval system.

Many modules.

No duplicated approval tables.

---

# Database

approval_workflows

Columns

id

uuid

company_id

name

description

module

is_active

created_by

updated_by

timestamps

softDeletes

---

workflow_steps

Columns

id

workflow_id

step_order

name

approver_role_id (nullable)

approver_user_id (nullable)

is_required

created_at

updated_at

---

approvals

Columns

id

uuid

approvable_type

approvable_id

workflow_id

current_step_id

status

submitted_by

submitted_at

completed_at

created_at

updated_at

---

approval_actions

Columns

id

approval_id

workflow_step_id

user_id

action

comment

acted_at

created_at

updated_at

---

# Relationships

ApprovalWorkflow

hasMany WorkflowSteps

Approval

morphTo approvable

belongsTo ApprovalWorkflow

belongsTo WorkflowStep

hasMany ApprovalActions

WorkflowStep

belongsTo ApprovalWorkflow

belongsTo Role

belongsTo User

ApprovalAction

belongsTo Approval

belongsTo WorkflowStep

belongsTo User

---

# Approval Status

Draft

Pending

In Review

Approved

Rejected

Cancelled

Returned

---

# Approval Actions

Submit

Approve

Reject

Return

Cancel

Resubmit

---

# Business Rules

A workflow contains ordered steps.

A record may have only one active approval.

Required steps must be completed before moving forward.

Approvals may be role-based or user-based.

When all required steps are approved

Status becomes Approved.

If any required step is rejected

Status becomes Rejected.

Returned sends the approval back to the submitter.

---

# Initial Workflow Templates

Time Entry Approval

Manager Approval

Document Approval

Manager Review

Executive Approval

Meeting Approval

Manager Approval

---

# Permissions

workflow.view

workflow.create

workflow.update

workflow.delete

workflow.activate

workflow.assign

approval.view

approval.submit

approval.approve

approval.reject

approval.return

approval.cancel

---

# Services

Generate

WorkflowService

ApprovalService

Responsibilities

Create workflows

Update workflows

Activate workflows

Submit approvals

Approve

Reject

Return

Cancel

Advance steps

Determine next approver

Business logic belongs ONLY here.

---

# UI

Workflow Builder

Workflow Steps

Approval Queue

My Approvals

Approval Details

Approval Timeline

Use Alpine.js for step management.

Reuse existing components.
# Sprint 16 — Workflow & Approvals (Part 2)

---

# Dashboard Integration

Extend the existing Dashboard.

Do NOT recreate it.

Add new Dashboard cards:

- Pending Approvals
- Approved Today
- Rejected Today
- Returned for Revision
- My Pending Requests
- Average Approval Time

Each card must include:

- Count
- Status indicator
- Quick action
- Permission-aware visibility

---

# Dashboard Widgets

Create widgets:

- My Pending Approvals
- Recently Approved
- Recently Rejected
- Approval Activity Feed
- Requests Awaiting My Decision

Limit each widget to the latest 5 records.

---

# Metrics Integration

Integrate with the centralized MetricsService.

Add methods for:

- pendingApprovals()
- approvedToday()
- rejectedToday()
- averageApprovalTime()
- approvalsByModule()
- approvalsByUser()
- approvalsByStatus()

Do NOT duplicate calculations.

---

# Sidebar Integration

Update Sidebar.

Create a new section:

Workflow

- My Approvals
- Approval Requests
- Approval History

Visibility must respect permissions.

---

# Global Search

Register searchable entities:

- Approval Requests
- Workflow Records

Search by:

- Reference Number
- Module
- Status
- Requester
- Approver
- Comments

---

# Activity Feed

Log activities:

- Request Submitted
- Request Approved
- Request Rejected
- Returned for Revision
- Approval Cancelled

Display:

- User
- Action
- Module
- Timestamp

---

# Notifications Preparation

Prepare notification architecture.

Generate notifications for:

- Approval Requested
- Approval Assigned
- Request Approved
- Request Rejected
- Request Returned
- Approval Reminder

Do NOT implement realtime broadcasting yet.

Prepare notification classes only.

---

# Audit Trail

Every workflow action must store:

- Previous Status
- New Status
- Action
- User
- Comment
- IP Address
- Browser/User Agent
- Timestamp

The audit history must never be deleted.

---

# Security

Policies must enforce:

- Requesters cannot approve their own requests.
- Users only see approvals they are authorized to access.
- Managers approve only their own organizational scope.
- Super Admin has full visibility.

---

# Approval Rules

Support:

- Single-step approval
- Multi-step approval
- Sequential approval
- Parallel approval (prepare architecture)
- Escalation (prepare architecture)
- Delegation (prepare architecture)

Only implement:

- Single-step
- Sequential multi-step

Prepare the remaining architecture for future sprints.

---

# Workflow States

Support:

Draft

↓

Submitted

↓

Pending Approval

↓

Approved

OR

Rejected

OR

Returned for Revision

↓

Resubmitted

↓

Approved

---

# Integration

Prepare reusable integration points for:

Projects

Tasks

Time Tracking

Documents

Meetings

Expenses (future)

Purchase Orders (future)

Leave Requests (future)

Invoices (future)

Contracts (future)

The workflow engine must be generic and reusable.

---

# Events

Generate:

WorkflowSubmitted

WorkflowApproved

WorkflowRejected

WorkflowReturned

WorkflowCancelled

ApprovalAssigned

---

# Listeners

Generate:

LogWorkflowActivity

RefreshWorkflowMetrics

NotifyNextApprover

NotifyRequester

UpdateDashboardMetrics

Prepare queue compatibility.

---

# Reports Preparation

Prepare datasets for:

Pending Requests

Approval Duration

Approval Performance

Approvals by Department

Approvals by User

Approvals by Module

Export architecture only.

---

# Filters

Support:

Company

Branch

Department

Requester

Approver

Module

Status

Date Range

Priority

---

# Sorting

Support:

Newest

Oldest

Pending First

Approved First

Rejected First

Longest Waiting

---

# Pagination

25 records per page.

Preserve filters.

---

# Performance

Use eager loading.

Avoid N+1 queries.

Reuse MetricsService.

Reuse existing Blade components.

Keep controllers thin.

All business logic belongs inside WorkflowService.

---

# Feature Tests

Generate tests for:

- Create Workflow
- Submit Workflow
- Approve Workflow
- Reject Workflow
- Return for Revision
- Resubmit Workflow
- Sequential Approval
- Authorization
- Validation
- Dashboard Integration
- Sidebar Visibility
- Metrics Integration
- Activity Feed
- Notifications
- Audit Trail

---

# Acceptance Criteria

Sprint is complete only if:

✔ Migration succeeds

✔ Seeder succeeds

✔ Workflow CRUD works

✔ Submit works

✔ Approve works

✔ Reject works

✔ Return works

✔ Sequential approval works

✔ Dashboard updated

✔ Sidebar updated

✔ Metrics updated

✔ Activity feed updated

✔ Notifications prepared

✔ Audit trail complete

✔ Policies enforced

✔ Feature tests pass

✔ Responsive UI

✔ No PHP errors

✔ No JavaScript errors

✔ Backward compatibility maintained

---

# Definition of Done

Generate only:

1. Migrations
2. Models
3. Factories
4. Seeders
5. Relationships
6. WorkflowService
7. ApprovalService
8. Requests
9. Policies
10. Controllers
11. Routes
12. Blade Views
13. Dashboard Integration
14. Sidebar Integration
15. Metrics Integration
16. Events
17. Listeners
18. Notifications
19. Audit Trail
20. Reports Preparation
21. Feature Tests

---

# Final Instructions

Before generating code:

Analyze the entire ERP.

Reuse existing architecture.

Do NOT regenerate completed modules.

Do NOT duplicate business logic.

Integrate with:

- MetricsService
- DashboardService
- Activity Feed
- Notification architecture
- Audit system
- Dashboard
- Sidebar
- Global Search

Provide:

- Generated files
- Modified files
- Database changes
- Routes added
- Dashboard changes
- Sidebar changes
- Metrics integration
- Events created
- Listeners created
- Notifications created
- Artisan commands
- Manual verification checklist
- Assumptions made

Stop after Sprint 16.