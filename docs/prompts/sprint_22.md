# Sprint 22

# Payments & Receivables

Version: 1.0

Priority: Critical

Status: Planned

---

# Objective

Implement the Payments & Receivables module.

This sprint completes the Sales-to-Cash workflow by introducing invoices, customer payments, receipts, outstanding balances, refunds, and credit notes.

Maintain enterprise architecture.

Controllers remain thin.

Business logic belongs inside Services.

Reuse existing architecture whenever possible.

---

# Workflow

Quotation

↓

Approved

↓

Invoice

↓

Payment

↓

Receipt

↓

Receivable Closed

---

# Database

Create:

Invoice

InvoiceItem

Payment

PaymentAllocation

Receipt

CreditNote

Refund

PaymentMethod

BankAccount

Currency

ExchangeRate

Support UUIDs.

Soft Deletes.

Activity Logging.

---

# Invoice Management

Implement:

Create Invoice

Generate from Approved Quotation

Manual Invoice

Edit Draft

Preview

Print

Duplicate

Cancel

Void

Archive

Restore

Version History

Statuses:

Draft

Issued

Sent

Viewed

Partially Paid

Paid

Overdue

Cancelled

Voided

---

# Invoice Items

Reuse quotation items where possible.

Support:

Description

Quantity

Unit

Unit Price

Discount

Tax

Subtotal

Total

Automatic calculations.

---

# Payment Recording

Support:

Full Payment

Partial Payment

Multiple Payments

Advance Payment

Overpayment

Manual Allocation

Automatic Allocation

Remaining Balance

---

# Receipts

Automatically generate receipts after successful payment.

Support:

PDF Receipt

Print

Email Preparation

Receipt Number

Payment Reference

---

# Credit Notes

Implement:

Full Credit

Partial Credit

Credit Against Invoice

Credit Balance

Apply to Future Invoice

Audit History

---

# Refunds

Support:

Full Refund

Partial Refund

Refund Reason

Approval Workflow

Activity Logging

---

# Payment Methods

Support:

Cash

Bank Transfer

Cheque

Mobile Money

Credit Card

Debit Card

Other

Administrators may create additional methods.

---

# Bank Accounts

Manage company bank accounts.

Fields:

Bank Name

Account Name

Account Number

Currency

Branch

SWIFT

IBAN

Status

---

# Multi-Currency

Prepare architecture.

Support:

Invoice Currency

Payment Currency

Exchange Rates

Base Currency

Future automatic exchange rate providers.

---

# Aging

Calculate:

Current

30 Days

60 Days

90 Days

120+

Outstanding

Overdue

---

# Customer Statements

Generate statements showing:

Invoices

Payments

Credits

Refunds

Outstanding Balance

Running Balance

---

# CRM Integration

Invoices belong to:

Customer

Account

Opportunity

Quotation

Maintain complete history.

---

# Project Integration

Allow invoices to reference projects.

Prepare for future project billing.

---

# Workflow

Integrate approval workflow for:

Invoice Approval

Refund Approval

Credit Note Approval

Users cannot approve their own requests.

---

# Notifications

Notify:

Invoice Created

Invoice Sent

Payment Received

Partial Payment

Invoice Overdue

Refund Approved

Credit Note Issued

Receipt Generated

---

# Dashboard

Create finance widgets.

Invoices Issued

Outstanding Amount

Overdue Amount

Payments Today

Payments This Month

Average Collection Time

Receivables Aging

---

# Metrics

Extend MetricsService.

Create:

InvoiceMetrics

PaymentMetrics

ReceivableMetrics

Support:

Revenue

Outstanding

Paid

Overdue

Average Payment Time

Collection Rate

Monthly Revenue

Cash Received

---

# Reports

Integrate ReportService.

Create:

Invoice Summary

Payment Summary

Outstanding Receivables

Aging Report

Revenue Report

Collection Report

Customer Statements

---

# Search

Extend Global Search.

Support:

Invoice Number

Receipt Number

Customer

Payment Reference

Bank Transaction

Invoice Status

---

# Export

Reuse ExportService.

Support:

Invoices

Receipts

Statements

Payments

PDF

Excel

CSV

Print

---

# Permissions

Create:

invoice.view

invoice.create

invoice.update

invoice.delete

invoice.send

invoice.void

invoice.approve

payment.view

payment.create

payment.refund

receipt.view

receipt.export

bank.manage

---

# Policies

Create:

InvoicePolicy

PaymentPolicy

ReceiptPolicy

RefundPolicy

CreditNotePolicy

BankAccountPolicy

---

# Performance

Prevent N+1 queries.

Cache finance metrics.

Optimize receivable calculations.

Paginate statements.

---

# Security

Respect Architecture Enhancement 02.

Enforce:

Permissions

Policies

Workflow

Company Isolation

Branch Isolation

Department Isolation

Exports

Reports

Metrics

---

# Testing

Generate comprehensive Feature Tests.

Invoice CRUD

Invoice from Quotation

Partial Payment

Multiple Payments

Receipts

Credit Notes

Refunds

Customer Statements

Reports

Metrics

Permissions

Policies

Search

Export

Workflow

Security

Run full test suite.

---

# Manual Verification

Verify:

✓ Invoice from quotation

✓ Manual invoice

✓ Partial payment

✓ Full payment

✓ Multiple payments

✓ Receipt generation

✓ Credit notes

✓ Refunds

✓ Aging

✓ Customer statements

✓ Dashboard

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

Sprint 22 is complete only if:

✓ Enterprise invoice system implemented

✓ Payment engine operational

✓ Receipts generated

✓ Partial payments supported

✓ Credit notes implemented

✓ Refunds implemented

✓ Aging calculations accurate

✓ Reports integrated

✓ Metrics integrated

✓ CRM integration complete

✓ Security enforced

✓ Tests passing

✓ Architecture maintained

---

# Stop

Stop after Sprint 22.

Wait for Sprint 23.