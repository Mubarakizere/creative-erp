# Sprint 28 — Enterprise Fixed Asset Management

Version: 1.0

Priority: High

Status: Planned

---

# Objective

Implement a complete Fixed Asset Management module for Creative ERP.

The module must manage the full asset lifecycle:

Acquisition
→ Registration
→ Assignment
→ Location
→ Depreciation
→ Maintenance
→ Transfer
→ Impairment
→ Disposal

The module must integrate with:

- Procurement
- Inventory
- Accounting
- General Ledger
- Financial Reports
- Workflow
- MetricsService
- ReportService
- ChartService
- ExportService
- Notifications
- Activity Logs
- Global Search
- Enterprise Security

Do not duplicate existing accounting, reporting, metrics, workflow, or authorization logic.

---

# Read Before Coding

Read:

docs/MASTER_DEVELOPMENT_RULES.md

docs/AI_CONTEXT.md

All previous Sprint documents.

Architecture Enhancement 01

Architecture Enhancement 02

Especially analyze:

Sprint 23 — Accounting

Sprint 24 — Financial Reporting

Sprint 25 — Inventory

Sprint 26 — Procurement

Sprint 27 — Warehouse Management

Understand existing:

Accounting Engine

General Ledger

Procurement Engine

Inventory Engine

Workflow Engine

MetricsService

ReportService

ChartService

ExportService

Policies

Permissions

Activity Logs

Dashboard

Notifications

Global Search

---

# Asset Categories

Create configurable asset categories.

Examples:

- Computers
- Cameras
- Vehicles
- Furniture
- Office Equipment
- Machinery
- Buildings
- Production Equipment
- IT Equipment
- Other

Support:

Name

Code

Description

Useful Life

Default Depreciation Method

Default Asset Account

Default Accumulated Depreciation Account

Default Depreciation Expense Account

Status

---

# Fixed Assets

Create Fixed Asset management.

Fields should support:

Asset Number

Asset Name

Category

Description

Serial Number

Barcode

Purchase Date

In-Service Date

Purchase Cost

Residual Value

Useful Life

Depreciation Method

Current Book Value

Accumulated Depreciation

Status

Condition

Location

Warehouse

Branch

Department

Assigned User

Supplier

Purchase Order

Purchase Invoice

Project

Notes

Attachments

---

# Asset Status

Support:

Draft

Pending Approval

Active

Under Maintenance

Transferred

Impaired

Fully Depreciated

Disposed

Sold

Written Off

---

# Asset Acquisition

Assets may originate from:

Procurement

Purchase Invoice

Inventory

Manual Registration

Project

Support asset capitalization.

The system must preserve the source transaction.

---

# Accounting Integration

Integrate with the existing Accounting Engine.

Do not implement a second accounting system.

Support accounting for:

Asset Acquisition

Capitalization

Depreciation

Accumulated Depreciation

Asset Disposal

Asset Sale

Asset Write-Off

Asset Impairment

Asset Revaluation if architecture supports it

---

# Depreciation

Support:

Straight Line

Declining Balance

Double Declining Balance

Units of Production where appropriate

Prepare architecture for future methods.

Depreciation must support:

Useful Life

Residual Value

Start Date

Frequency

Partial Period

Monthly Depreciation

Accumulated Depreciation

Book Value

Fully Depreciated Status

---

# Depreciation Rules

Prevent:

Depreciation before in-service date

Depreciation after disposal

Duplicate depreciation periods

Negative book value

Depreciation below residual value

Invalid useful life

Unauthorized manual depreciation changes

---

# Depreciation Processing

Support:

Preview

Calculate

Approve

Post

Reverse where authorized

Monthly processing

Batch processing

Depreciation history

---

# Asset Assignment

Allow assets to be assigned to:

Users

Departments

Branches

Projects

Locations

Track complete assignment history.

---

# Asset Transfers

Support:

User → User

Department → Department

Branch → Branch

Location → Location

Warehouse → Warehouse

Require authorization.

Preserve historical ownership/location.

---

# Maintenance

Support:

Maintenance records

Maintenance date

Description

Vendor

Cost

Warranty

Next maintenance date

Attachments

Status

Maintenance history

Maintenance costs should be available for reporting.

---

# Warranty

Support:

Warranty Start

Warranty End

Provider

Coverage

Status

Notifications before expiration.

---

# Asset Impairment

Support:

Impairment reason

Impairment amount

Approval

Accounting entry

Audit history

---

# Asset Disposal

Support:

Disposal reason

Disposal date

Disposal method

Sale price

Disposal costs

Gain/Loss

Approval

Accounting integration

Inventory/asset status update

---

# Asset Sale

Support:

Sale price

Buyer

Sale date

Gain/Loss calculation

Accounting integration

---

# Asset Write-Off

Support:

Write-off reason

Approval

Accounting integration

Audit history

---

# Asset Register

Create an enterprise asset register.

Support:

Filtering

Search

Sorting

Category

Status

Branch

Department

Assigned User

Location

Purchase Date

Book Value

---

# Barcode / QR

Prepare support for:

Barcode

QR Code

Asset Label

Future mobile scanning

Asset lookup by barcode.

---

# Dashboard

Create widgets:

Total Assets

Total Asset Value

Net Book Value

Accumulated Depreciation

Monthly Depreciation

Assets Under Maintenance

Assets Fully Depreciated

Assets Near Warranty Expiry

Recent Transfers

Recent Disposals

---

# Metrics

Extend MetricsService.

Create:

AssetMetrics

Support:

Asset Count

Asset Value

Net Book Value

Accumulated Depreciation

Monthly Depreciation

Depreciation by Category

Assets by Department

Assets by Branch

Maintenance Cost

Disposal Value

Asset Utilization

---

# Reports

Extend ReportService.

Create:

Asset Register

Asset Valuation

Depreciation Schedule

Depreciation Expense Report

Asset Acquisition Report

Asset Transfer Report

Maintenance Report

Warranty Expiry Report

Impairment Report

Disposal Report

Asset Gain/Loss Report

---

# Charts

Extend ChartService.

Support:

Asset Value by Category

Depreciation Trend

Asset Acquisition Trend

Maintenance Cost Trend

Assets by Department

Assets by Branch

Disposal Trend

---

# Search

Extend Global Search.

Support:

Asset Number

Asset Name

Serial Number

Barcode

Category

Assigned User

Department

Branch

Location

Supplier

Purchase Order

---

# Export

Reuse ExportService.

Support:

PDF

Excel

CSV

Print

---

# Workflow

Use existing Workflow Engine.

Require approval for:

Asset Acquisition

Asset Transfer

Asset Impairment

Asset Disposal

Asset Write-Off

Asset Sale

Users cannot approve their own requests.

---

# Notifications

Notify authorized users about:

Depreciation Due

Warranty Expiring

Maintenance Due

Asset Transfer Approval

Disposal Approval

Impairment Approval

---

# Permissions

Create:

asset.view

asset.create

asset.update

asset.delete

asset.assign

asset.transfer

asset.depreciate

asset.maintenance

asset.impair

asset.dispose

asset.report

asset.export

asset.manage

---

# Policies

Create:

AssetCategoryPolicy

AssetPolicy

AssetTransferPolicy

AssetMaintenancePolicy

AssetImpairmentPolicy

AssetDisposalPolicy

Every sensitive action must be authorized.

---

# Multi-Tenant Security

Respect Architecture Enhancement 02.

Enforce:

Company Isolation

Branch Isolation

Department Isolation

Role Permissions

Policies

Workflow Authorization

Reports

Metrics

Search

Exports

A user must never see another company's assets.

---

# Performance

Prevent N+1 queries.

Use eager loading.

Optimize depreciation calculations.

Cache appropriate metrics.

Avoid recalculating historical depreciation unnecessarily.

Use database transactions for financial operations.

---

# Audit Logging

Log:

Asset Creation

Asset Updates

Asset Assignment

Asset Transfer

Depreciation Posting

Depreciation Reversal

Maintenance

Impairment

Disposal

Sale

Write-Off

Approval/Rejection

---

# Testing

Create comprehensive Feature Tests covering:

Asset Categories

Assets

Asset Acquisition

Asset Assignment

Asset Transfers

Depreciation

Depreciation Posting

Depreciation Reversal

Maintenance

Warranty

Impairment

Disposal

Asset Sale

Write-Off

Accounting Integration

General Ledger

Workflow

Permissions

Policies

Company Isolation

Branch Isolation

Department Isolation

Metrics

Reports

Search

Export

Notifications

Audit Logs

---

# Critical Financial Tests

Verify:

Asset acquisition creates correct accounting entries.

Depreciation creates correct journal entries.

Accumulated depreciation is correct.

Book value is correct.

Disposal removes the asset correctly.

Gain/Loss on disposal is calculated correctly.

No duplicate journal entries are created.

Transactions are atomic.

---

# Manual Verification

Test as:

Super Admin

Finance Manager

Asset Manager

Department Manager

Employee

User Without Asset Permissions

Verify each role only sees and performs authorized actions.

---

# Acceptance Criteria

Sprint 28 is complete only when:

✓ Asset register operational

✓ Asset categories operational

✓ Acquisition operational

✓ Assignment operational

✓ Transfers operational

✓ Depreciation operational

✓ Maintenance operational

✓ Warranty tracking operational

✓ Impairment operational

✓ Disposal operational

✓ Accounting integration operational

✓ General Ledger integration operational

✓ Workflow integration operational

✓ Reports operational

✓ Metrics operational

✓ Dashboard operational

✓ Search operational

✓ Export operational

✓ Security enforced

✓ Multi-company isolation verified

✓ Audit logging operational

✓ Tests passing

✓ No PHP errors

✓ No JavaScript errors

✓ Existing modules remain functional

---

# Stop

Stop after Sprint 28.

Do NOT begin Sprint 29.

Wait for approval.