# Sprint 21

# Sales Documents (Quotations & Invoice Preparation)

Version: 1.0

Priority: Critical

Status: Planned

---

# Objective

Implement the Sales Documents module.

This sprint establishes the sales document workflow that bridges CRM and Finance.

The module must integrate seamlessly with:

CRM

Projects

Workflow Engine

Documents

Notifications

MetricsService

ReportService

Activity Logs

ExportService

Architecture Enhancement 01

Architecture Enhancement 02

Controllers must remain thin.

Business logic belongs inside Services.

Maintain backward compatibility.

---

# Sales Workflow

The workflow for this sprint is:

Lead

↓

Opportunity

↓

Quotation

↓

Approval

↓

Accepted

↓

Invoice Draft

(The actual invoice and payment lifecycle will be implemented in Sprint 22.)

---

# Database

Create:

Quotation

QuotationItem

QuotationTemplate

Tax

PaymentTerm

QuotationStatus

QuotationApproval

Use UUIDs.

Soft Deletes.

Activity Logging.

---

# Quotations

Implement:

Create Quotation

Edit Quotation

Duplicate Quotation

Archive

Restore

Delete

Preview

Print

PDF Export

Email Preparation (future)

Version History

Status Management

Statuses:

Draft

Pending Approval

Approved

Rejected

Sent

Viewed

Accepted

Declined

Expired

Converted

---

# Quotation Items

Each quotation supports multiple items.

Fields:

Product / Service

Description

Quantity

Unit

Unit Price

Discount

Tax

Subtotal

Total

Sort Order

Automatic calculations.

---

# Tax Engine

Support:

Fixed Tax

Percentage Tax

Multiple Taxes

Tax Inclusive

Tax Exclusive

Future regional tax support.

---

# Discounts

Support:

Line Discount

Global Discount

Percentage

Fixed Amount

Automatic recalculation.

---

# Payment Terms

Support:

Due on Receipt

7 Days

14 Days

30 Days

45 Days

60 Days

Custom Terms

Reusable across quotations.

---

# Templates

Create quotation templates.

Examples:

Standard

Consulting

Development

Maintenance

Training

Support

Administrators can create custom templates.

---

# CRM Integration

Allow creating quotations directly from:

Opportunity

Lead (if permitted)

Account

Contact

Automatically populate:

Customer

Address

Contact

Currency

Owner

Notes

---

# Project Integration

Allow quotation conversion into Project after acceptance.

Do not create the project automatically.

Prepare architecture only.

---

# Approval Workflow

Integrate Workflow Engine.

Support approval based on:

Quotation Value

Department

Company Rules

Role

Approver

Users cannot approve their own quotation.

---

# Documents

Reuse Document module.

Allow attaching:

Specifications

Contracts

Images

Reference Documents

Proposals

Certificates

---

# Discussions

Reuse Discussion module.

Allow comments on quotations.

Support:

Replies

Mentions

Internal Notes

Pinned Discussions

---

# Notifications

Notify:

Quotation Created

Quotation Submitted

Approved

Rejected

Sent

Viewed

Accepted

Declined

Converted

---

# Dashboard

Create Sales Dashboard widgets.

Draft Quotations

Pending Approval

Approved

Accepted

Expired

Pipeline Value

Average Quotation

Recent Quotations

---

# Metrics

Extend MetricsService.

Create:

QuotationMetrics

Metrics:

Total Quotations

Draft

Approved

Pending

Accepted

Declined

Expired

Conversion Rate

Average Value

Revenue Forecast

Do not duplicate calculations.

---

# Reports

Integrate with ReportService.

Provide reports:

Quotation Summary

Approval Summary

Sales Pipeline

Quotation Conversion

Revenue Forecast

Top Customers

Top Sales Representatives

---

# Search

Extend Global Search.

Support:

Quotation Number

Customer

Reference

Project

Status

Owner

Results must respect permissions.

---

# Export

Reuse ExportService.

Support:

PDF

Excel

CSV

Print

Exports must include:

Header

Company Logo

Customer Information

Items

Taxes

Discounts

Totals

Terms

Footer

---

# Permissions

Create:

quotation.view

quotation.create

quotation.update

quotation.delete

quotation.approve

quotation.send

quotation.convert

quotation.export

quotation.manage

---

# Policies

Create:

QuotationPolicy

QuotationTemplatePolicy

TaxPolicy

PaymentTermPolicy

---

# Routes

RESTful resource routes.

Additional routes:

duplicate

preview

print

approve

reject

send

accept

decline

convert

archive

restore

---

# Performance

Prevent N+1 queries.

Use eager loading.

Cache quotation metrics.

Optimize totals calculation.

---

# Security

Respect Architecture Enhancement 02.

Users only access quotations they are authorized to view.

Approvals enforce Workflow permissions.

Exports respect authorization.

Multi-company isolation remains mandatory.

---

# Testing

Generate Feature Tests for:

Quotation CRUD

Items

Tax Calculations

Discount Calculations

Totals

Approval Workflow

Policies

Permissions

Reports

Metrics

Search

Export

Notifications

Discussion Integration

Document Integration

Multi-company isolation

Run full test suite.

---

# Manual Verification

Verify:

✓ Create quotation

✓ Multiple line items

✓ Automatic totals

✓ Taxes

✓ Discounts

✓ Payment terms

✓ Templates

✓ Approval workflow

✓ Notifications

✓ Reports

✓ Metrics

✓ Search

✓ Export PDF

✓ Export Excel

✓ Export CSV

✓ Discussions

✓ Documents

✓ Responsive UI

✓ Feature tests passing

✓ No PHP errors

✓ No JavaScript errors

---

# Acceptance Criteria

Sprint 21 is complete only if:

✓ Enterprise quotation system implemented

✓ Approval workflow operational

✓ CRM integration complete

✓ Metrics integrated

✓ Reports integrated

✓ Export integrated

✓ Notifications integrated

✓ Documents integrated

✓ Discussions integrated

✓ Security enforced

✓ Tests passing

✓ Architecture maintained

---

# Stop

Stop after Sprint 21.

Wait for Sprint 22.