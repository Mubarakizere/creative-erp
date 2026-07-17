# Architecture Enhancement 02

# Enterprise Security & Authorization Audit

Version: 1.0

Status: CRITICAL

Priority: P0

---

# Objective

Perform a complete security and authorization audit of the entire ERP.

This is NOT a feature sprint.

This is a full enterprise security review.

Every authenticated user must only access resources they are explicitly authorized to use.

No user should gain access merely because they are authenticated.

---

# Read Before Coding

Read

docs/MASTER_DEVELOPMENT_RULES.md

docs/AI_CONTEXT.md

Read all Sprint documents.

Analyze the existing Laravel application before making changes.

---

# Authentication Review

Verify

- Login
- Logout
- Remember Me
- Password Reset
- Email Verification
- Session Handling
- Session Regeneration
- CSRF Protection
- Rate Limiting
- Failed Login Handling

Do not break existing authentication.

---

# Authorization Review

Audit the complete authorization system.

Verify

- Spatie Roles
- Spatie Permissions
- Permission Middleware
- Policies
- Gates
- Authorization Traits
- Blade @can directives
- Controller authorization
- Route middleware
- Service authorization

No module should bypass authorization.

---

# Super Admin

Super Admin is the ONLY role that bypasses permission checks.

Use Gate::before() correctly.

No other role should bypass permissions.

---

# Users Without Roles

Review the desired behavior.

If a user has no assigned role:

- They may authenticate.
- They must NOT access ERP modules.
- Show a minimal dashboard or "Access Pending" page.
- Deny access to protected routes.

---

# Routes

Audit every route.

Verify

- auth middleware
- verified middleware
- permission middleware
- policy authorization

No admin route should rely only on auth middleware.

---

# Controllers

Audit every Admin controller.

Verify

authorize()

authorizeResource()

Policy usage

No controller action should skip authorization.

Controllers must remain thin.

---

# Policies

Audit every policy.

Including

CompanyPolicy

BranchPolicy

DepartmentPolicy

UserPolicy

RolePolicy

ClientPolicy

ProjectPolicy

TaskPolicy

MilestonePolicy

DocumentPolicy

CommentPolicy

MeetingPolicy

TimeEntryPolicy

WorkflowPolicy

Every action must be protected.

---

# Blade Views

Audit every Blade view.

Buttons

Links

Actions

Cards

Widgets

Sidebar

Dashboard

Forms

Tables

All visibility must use

@can

@canany

@cannot

or policy helpers.

Never rely only on hidden routes.

---

# Sidebar

Audit the complete sidebar.

Every menu item must respect permissions.

Unauthorized modules must never appear.

Nested menus must also be protected.

---

# Dashboard

Audit Dashboard.

Cards

Charts

Widgets

Quick Actions

Statistics

Buttons

Only display data the user is authorized to view.

MetricsService must respect authorization scope.

---

# Metrics Security

Review every Metrics provider.

Ensure statistics respect

Company

Branch

Department

Role

Permissions

Users must never see metrics outside their scope.

---

# Services

Audit all Services.

Services must never assume authorization.

Controllers or services must explicitly enforce authorization.

---

# Multi-Tenant Security

Verify isolation by

Company

Branch

Department

No data leakage between organizations.

---

# Global Search

Search results must respect permissions.

Users must never discover records they cannot access.

---

# Workflow

Audit approval permissions.

Users cannot approve their own requests.

Only authorized approvers may approve.

---

# Time Tracking

Users edit only their own entries unless permitted.

Managers only see team entries.

---

# Documents

Internal documents require proper permissions.

Private documents remain private.

---

# Discussions

Internal comments remain restricted.

Mentions must not leak information.

---

# API Preparation

Ensure future API Resources inherit the same authorization rules.

Prepare for API reuse.

---

# Events

Verify events never expose unauthorized information.

---

# Notifications

Only send notifications to authorized recipients.

---

# Logging

Audit security logging.

Log

Failed authorization

Permission denied

Policy failures

Privilege escalation attempts

---

# Testing

Generate comprehensive tests.

Authentication

Authorization

Permission Middleware

Policies

Blade Authorization

Route Protection

Sidebar Visibility

Dashboard Visibility

Metrics Isolation

Company Isolation

Branch Isolation

Department Isolation

Workflow Authorization

Time Entry Authorization

Document Authorization

Discussion Authorization

Global Search Authorization

Unauthorized Access

Users Without Roles

Super Admin

Manager

Employee

Future Client

Run full test suite.

---

# Manual Verification

Provide a checklist for testing with:

- Super Admin
- Manager
- Employee
- User with no role

Describe the expected UI and accessible modules for each.

---

# Deliverables

Provide:

1. Generated files
2. Modified files
3. Security issues found
4. Security issues fixed
5. Policies audited
6. Routes audited
7. Controllers audited
8. Blade views audited
9. Sidebar audit
10. Dashboard audit
11. Metrics audit
12. Multi-tenant audit
13. Test results
14. Manual verification checklist
15. Security recommendations

---

# Acceptance Criteria

Complete only when:

✔ Users only access permitted modules

✔ Sidebar respects permissions

✔ Dashboard respects permissions

✔ Routes are protected

✔ Controllers authorize actions

✔ Policies enforced

✔ Blade uses authorization helpers

✔ Metrics isolated correctly

✔ Search respects permissions

✔ Multi-company isolation verified

✔ Users without roles cannot access ERP modules

✔ Super Admin retains full access

✔ No privilege escalation

✔ Feature tests pass

✔ No PHP errors

✔ No JavaScript errors

✔ Backward compatibility maintained

---

# Stop

Stop after Architecture Enhancement 02.

Wait for the next instruction.