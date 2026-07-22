# Sprint 23

# Accounting Foundation

Version: 1.0

Priority: Critical

Status: Planned

---

# Objective

Implement the core Accounting Foundation for Creative ERP.

This sprint introduces the financial ledger that powers all future accounting operations.

The implementation must follow double-entry accounting principles and integrate with the existing Finance, CRM, Projects, Reports, Metrics, Workflow, Notifications, and Security architecture.

Controllers must remain thin.

Business logic belongs in Services.

Maintain backward compatibility.

---

# Accounting Workflow

Invoice
        ↓
Journal Entry
        ↓
Ledger Posting
        ↓
Trial Balance
        ↓
Financial Statements

---

# Database

Create:

ChartOfAccount

AccountType

Journal

JournalEntry

GeneralLedger

FiscalYear

AccountingPeriod

OpeningBalance

ClosingEntry

Use UUIDs.

Soft Deletes.

Activity Logging.

---

# Chart of Accounts

Support:

Assets

Liabilities

Equity

Revenue

Expenses

Other Income

Other Expense

Allow nested accounts.

Support account codes.

Allow administrators to create custom accounts.

---

# Account Types

Support:

Current Asset

Fixed Asset

Bank

Cash

Accounts Receivable

Accounts Payable

Equity

Revenue

Expense

Tax

Custom Types

---

# Journal Entries

Implement:

Manual Journal

Automatic Journal

Balanced Entries Only

Debit

Credit

Reference

Memo

Attachments

Approval Workflow

Status:

Draft

Posted

Reversed

Cancelled

---

# General Ledger

Automatically post journal entries.

Maintain running balances.

Track:

Debit

Credit

Balance

Reference

User

Date

Source Module

---

# Double Entry Accounting

Enforce:

Total Debit = Total Credit

Never allow unbalanced entries.

Reject invalid journals.

---

# Fiscal Years

Support:

Create Fiscal Year

Close Fiscal Year

Reopen Fiscal Year (permission)

Current Fiscal Year

Multiple Fiscal Years

---

# Accounting Periods

Support:

Monthly

Quarterly

Yearly

Open

Closed

Locked

Prevent posting into locked periods.

---

# Opening Balances

Support importing opening balances for all accounts.

Track adjustments.

Audit all imports.

---

# Closing Entries

Generate automatic year-end closing entries.

Carry forward retained earnings.

Prepare opening balances for the next fiscal year.

---

# Automatic Posting

Prepare architecture so future modules automatically generate journals:

Invoices

Payments

Refunds

Payroll

Inventory

Assets

Procurement

(No automatic posting beyond current Finance modules is required in this sprint.)

---

# Workflow

Integrate with Workflow Engine.

Require approval for:

Manual Journals

Period Closing

Fiscal Year Closing

Users cannot approve their own journals.

---

# Dashboard

Create Accounting Dashboard widgets.

Total Assets

Total Liabilities

Revenue

Expenses

Net Profit

Journal Entries

Unposted Journals

Open Periods

---

# Metrics

Extend MetricsService.

Create:

AccountingMetrics

Support:

Account Balances

Revenue

Expenses

Net Income

Journal Counts

Ledger Activity

Fiscal Year Status

---

# Reports

Integrate ReportService.

Create:

Trial Balance

General Ledger

Journal Report

Account Activity

Fiscal Year Summary

Opening Balance Report

Closing Report

---

# Search

Extend Global Search.

Support:

Journal Number

Account Code

Account Name

Ledger Reference

Fiscal Year

Accounting Period

---

# Export

Reuse ExportService.

Support:

PDF

Excel

CSV

Print

Trial Balance

General Ledger

Journal Reports

Account Activity

---

# Permissions

Create:

account.view

account.create

account.update

account.delete

journal.view

journal.create

journal.post

journal.reverse

ledger.view

fiscal.manage

period.manage

---

# Policies

Create:

ChartOfAccountPolicy

JournalPolicy

LedgerPolicy

FiscalYearPolicy

AccountingPeriodPolicy

OpeningBalancePolicy

---

# Performance

Prevent N+1 queries.

Cache accounting metrics.

Optimize ledger queries.

Paginate ledger entries.

---

# Security

Respect Architecture Enhancement 02.

Enforce:

Permissions

Policies

Company Isolation

Branch Isolation

Department Isolation

Workflow Authorization

Reports

Exports

Metrics

---

# Testing

Generate comprehensive Feature Tests.

Chart of Accounts

Journal Entries

Balanced Entry Validation

Ledger Posting

Trial Balance

Fiscal Years

Accounting Periods

Opening Balances

Reports

Metrics

Policies

Permissions

Search

Export

Workflow

Run full test suite.

---

# Manual Verification

Verify:

✓ Create Chart of Accounts

✓ Create Journal Entry

✓ Balanced debit/credit validation

✓ Ledger posting

✓ Trial Balance

✓ Fiscal Year

✓ Accounting Periods

✓ Opening Balances

✓ Reports

✓ Metrics

✓ Search

✓ Export

✓ Responsive UI

✓ Feature tests passing

✓ No PHP errors

✓ No JavaScript errors

---

# Acceptance Criteria

Sprint 23 is complete only if:

✓ Double-entry accounting enforced

✓ General Ledger operational

✓ Trial Balance accurate

✓ Fiscal Years implemented

✓ Accounting Periods implemented

✓ Metrics integrated

✓ Reports integrated

✓ Security enforced

✓ Tests passing

✓ Architecture maintained

---

# Stop

Stop after Sprint 23.

Wait for Sprint 24.