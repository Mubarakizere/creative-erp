# Sprint 20

# Customer Relationship Management (CRM)

Version: 1.0

Priority: High

Status: Planned

---

# Objective

Build the Enterprise CRM module.

The CRM must integrate with:

Projects

Meetings

Documents

Discussions

Notifications

Workflow

Reports

MetricsService

Activity Log

Architecture Enhancement 01

Architecture Enhancement 02

Maintain enterprise architecture.

Controllers remain thin.

Business logic belongs inside Services.

---

# CRM Overview

The CRM manages:

Leads

Contacts

Accounts

Opportunities

Deals

Sales Pipeline

Activities

Notes

Attachments

Future Quotes

Future Invoices

Future Contracts

---

# Database

Create models:

Lead

Contact

Account

Opportunity

Pipeline

PipelineStage

Activity

LeadSource

Industry

Tag

All models use UUIDs.

Support Soft Deletes.

Activity Logging enabled.

---

# Lead Management

Implement:

Create Lead

Edit Lead

Archive Lead

Restore Lead

Convert Lead

Assign Owner

Lead Status

Lead Rating

Expected Value

Probability

Lead Source

Industry

Tags

Custom Fields (JSON)

---

# Contacts

Each Lead may have:

Multiple Contacts

Email

Phone

Position

Department

Address

Social Links

Notes

One Primary Contact

---

# Accounts

Support Companies.

Store:

Company Name

Industry

Website

Tax ID

Address

Billing Address

Shipping Address

Phone

Email

Logo

Notes

---

# Opportunities

Create Opportunities from Leads.

Fields:

Name

Account

Contact

Expected Revenue

Probability

Expected Close Date

Pipeline

Stage

Owner

Status

Description

---

# Pipeline

Create dynamic Pipelines.

Examples:

Sales

Enterprise

Government

International

Future Custom Pipelines

---

# Pipeline Stages

Support:

New

Qualified

Proposal

Negotiation

Won

Lost

Allow administrators to customize stages.

---

# Activities

Track:

Call

Email

Meeting

Visit

Demo

Follow-up

Task

Reminder

Activities integrate with:

Calendar

Notifications

Meetings

Future Email

---

# Relationships

Lead

↓

Account

↓

Opportunity

↓

Project

The conversion flow must preserve history.

---

# Sidebar

Add CRM menu.

Permission driven.

Responsive.

Desktop and mobile.

---

# Dashboard

Add CRM widgets.

Lead Count

Opportunity Value

Won Deals

Lost Deals

Conversion Rate

Pipeline Value

Recent Activities

Upcoming Follow-ups

---

# Metrics

Extend MetricsService.

Do NOT duplicate calculations.

Create:

CRMMetrics

Reuse ReportService.

---

# Permissions

Create:

crm.view

crm.create

crm.update

crm.delete

crm.convert

crm.manage

crm.pipeline

crm.activities

---

# Policies

Create:

LeadPolicy

AccountPolicy

ContactPolicy

OpportunityPolicy

PipelinePolicy

ActivityPolicy

---

# Routes

Resource Controllers.

RESTful routes.

Additional routes:

convert

archive

restore

assign

pipeline

activities

---

# Testing

Generate Feature Tests.

Permissions

Policies

Conversion

Pipeline

Relationships

Metrics

Reports

Dashboard

Security

---

# Manual Verification

Verify:

✓ Lead CRUD

✓ Contact CRUD

✓ Account CRUD

✓ Opportunity CRUD

✓ Pipeline

✓ Conversion

✓ Dashboard

✓ Reports

✓ Metrics

✓ Notifications

✓ Permissions

✓ Responsive UI

✓ No PHP errors

✓ No JavaScript errors

---

---

# Lead Conversion

Implement a complete Lead Conversion Wizard.

A lead may be converted into:

Account

Contact

Opportunity

Project (optional)

During conversion:

- Preserve activity history
- Preserve notes
- Preserve documents
- Preserve discussions
- Preserve attachments
- Preserve meetings
- Preserve workflow history
- Preserve notifications

Never duplicate data unnecessarily.

Maintain complete audit history.

---

# Customer Timeline

Create a unified customer timeline.

Display:

Lead Created

Status Changes

Meetings

Calls

Emails (future)

Tasks

Documents

Projects

Opportunities

Announcements

Discussions

Workflow Approvals

Activity Logs

Newest first.

Infinite scrolling.

Lazy loading.

---

# Notes

Support rich text notes.

Features:

Pinned Notes

Internal Notes

Mentions

Attachments

Version History

Search

---

# Attachments

Allow uploading:

PDF

Word

Excel

Images

Videos

Contracts

Quotes

Specifications

Certificates

Reuse existing Document module.

Do not duplicate storage logic.

---

# Tags

Support unlimited tags.

Examples:

VIP

Government

Returning Customer

High Priority

Strategic

Prospect

Enterprise

Partner

Lost

Won

Tags should be searchable.

---

# Customer Health

Prepare architecture.

Health Score:

Excellent

Good

Warning

Critical

Based on:

Open Opportunities

Recent Activity

Project Status

Meeting Activity

Future Financial Data

Do not implement AI scoring yet.

---

# Reminders

Support reminders.

Examples:

Call customer

Meeting reminder

Follow-up

Renewal

Contract expiry

Birthday

Anniversary

Integrate with Notification Center.

---

# Kanban Pipeline

Create an interactive Kanban board.

Columns:

New

Qualified

Proposal

Negotiation

Won

Lost

Drag & Drop.

Update stage automatically.

Activity Log integration.

Permission aware.

Responsive.

---

# Customer Dashboard

Create profile dashboard.

Include:

Overview

Contacts

Open Opportunities

Projects

Meetings

Documents

Discussions

Activities

Timeline

Reports

Notes

Attachments

Statistics

---

# CRM Metrics

Extend MetricsService.

Create CRMMetrics.

Include:

Total Leads

Qualified Leads

Won Deals

Lost Deals

Pipeline Value

Average Deal Size

Conversion Rate

Sales Velocity

Customer Growth

Top Sales Representatives

Upcoming Follow-ups

Open Activities

Never duplicate calculations.

---

# CRM Reports

Integrate with ReportService.

Create reports:

Lead Performance

Sales Pipeline

Conversion Rate

Opportunity Analysis

Account Activity

Sales Forecast

Representative Performance

Customer Growth

Activity Summary

Reminder Summary

---

# Search

Extend Global Search.

Support:

Lead Name

Account

Contact

Opportunity

Phone

Email

Company

Tags

Owner

Status

Results must respect permissions.

---

# Notifications

Integrate Notification Center.

Notify:

Lead Assigned

Opportunity Created

Stage Changed

Reminder Due

Deal Won

Deal Lost

Lead Converted

Mention Added

Activity Created

---

# Workflow Integration

Support approval workflows for:

Large Opportunities

Discount Requests

Lead Approval

Special Pricing

Future Quote Approval

Reuse Workflow Engine.

---

# Discussions

Integrate Discussion module.

CRM entities support:

Comments

Replies

Internal Notes

Mentions

Pinned Discussions

---

# Meetings

Integrate Meeting module.

Allow:

Schedule Meeting

Customer Meeting

Video Meeting

Meeting Notes

Attendance

Meeting History

---

# Documents

Reuse Document module.

Support:

Contracts

Quotes

Presentations

Invoices (future)

Specifications

Customer Files

Policies

Certificates

---

# Reports Dashboard

Create CRM dashboard widgets.

Pipeline Funnel

Conversion Rate

Lead Sources

Opportunity Value

Upcoming Activities

Recent Deals

Sales Performance

Customer Growth

---

# Export

Support exporting:

Leads

Contacts

Accounts

Opportunities

Activities

Reports

Use ExportService.

Respect permissions.

---

# Security

Respect Architecture Enhancement 02.

CRM data must respect:

Permissions

Policies

Company

Branch

Department

Role

Ownership

Managers should only see their teams where applicable.

---

# Performance

Prevent N+1.

Use eager loading.

Cache CRM metrics.

Paginate large datasets.

Lazy load timelines.

Optimize Kanban queries.

---

# Accessibility

Keyboard navigation.

Accessible forms.

Screen reader labels.

Responsive Kanban.

Accessible tables.

Proper color contrast.

---

# Testing

Generate comprehensive Feature Tests.

Lead Conversion

Kanban

Timeline

Attachments

Notes

Reminders

Pipeline

Reports

Metrics

Search

Notifications

Permissions

Policies

Workflow

Meetings

Documents

Discussions

Export

Performance

Multi-company isolation

Run the complete test suite.

---

# Manual Verification

Verify:

✓ Lead Conversion Wizard

✓ Customer Timeline

✓ Kanban Drag & Drop

✓ Attachments

✓ Notes

✓ Reminders

✓ Notifications

✓ Meetings

✓ Documents

✓ Discussions

✓ Reports

✓ Metrics

✓ Search

✓ Export

✓ Dashboard

✓ Permissions

✓ Policies

✓ Company Isolation

✓ Branch Isolation

✓ Department Isolation

✓ Responsive UI

✓ No PHP Errors

✓ No JavaScript Errors

✓ Feature Tests Passing

---

# Success Criteria

Sprint 20 is complete only if:

✓ Enterprise CRM implemented

✓ Lead conversion complete

✓ Kanban pipeline functional

✓ Customer timeline operational

✓ CRM metrics integrated

✓ CRM reports integrated

✓ Notifications working

✓ Workflow integrated

✓ Meetings integrated

✓ Documents integrated

✓ Discussions integrated

✓ Search integrated

✓ Export integrated

✓ Dashboard complete

✓ Security enforced

✓ Performance optimized

✓ Tests passing

✓ Enterprise architecture maintained

---

# Stop

Stop after Sprint 20.

Wait for Sprint 21.
