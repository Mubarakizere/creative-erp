# Sprint 26

# Enterprise Procurement Management

Version: 1.0

Priority: Critical

Status: Planned

---

# Objective

Implement the Enterprise Procurement Management module.

This module manages the complete purchasing lifecycle from requisition to supplier payment while integrating with Inventory, Accounting, Workflow, Reports, Metrics, Notifications, Dashboard, and Security.

Reuse the existing enterprise architecture.

Controllers remain thin.

Business logic belongs inside Services.

Reuse:

Inventory Engine

Accounting Engine

MetricsService

ReportService

ChartService

ExportService

Workflow Engine

Notification System

Activity Logs

Global Search

Security Architecture

No duplicated business logic.

---

# Procurement Workflow

Purchase Requisition

↓

Approval

↓

Request For Quotation (RFQ)

↓

Supplier Quotations

↓

Supplier Selection

↓

Purchase Order

↓

Approval

↓

Goods Receipt

↓

Inventory Updated

↓

Supplier Invoice

↓

Payment

↓

General Ledger

---

# Database

Create:

Supplier

SupplierCategory

SupplierContact

PurchaseRequisition

PurchaseRequisitionItem

SupplierQuotation

SupplierQuotationItem

PurchaseOrder

PurchaseOrderItem

GoodsReceipt

GoodsReceiptItem

PurchaseInvoice

PurchaseInvoiceItem

SupplierPayment

SupplierPerformance

Support:

UUID

Soft Deletes

Activity Logs

Company Isolation

---

# Suppliers

Support:

Supplier Code

Company Name

Contacts

Email

Phone

Address

Tax Information

Payment Terms

Currency

Status

Rating

Preferred Supplier

Categories

Multiple Contacts

Attachments

---

# Supplier Categories

Support:

Local

International

Manufacturer

Distributor

Wholesaler

Service Provider

Custom Categories

---

# Purchase Requisition

Support:

Draft

Submitted

Approved

Rejected

Cancelled

Items

Notes

Attachments

Workflow Approval

Priority

Required Date

Department

Project

Requested By

---

# Request For Quotation (RFQ)

Generate RFQs from approved requisitions.

Support:

Multiple Suppliers

Expiration Date

Terms

Attachments

Status

Track supplier responses.

---

# Supplier Quotations

Support:

Unit Price

Discount

Tax

Lead Time

Validity

Attachments

Notes

Comparison View

Supplier Ranking

---

# Purchase Orders

Generate Purchase Orders from selected quotations.

Support:

Draft

Approved

Sent

Partially Received

Completed

Cancelled

Revision History

Attachments

Workflow Approval

---

# Goods Receipt

Receive purchased goods.

Support:

Partial Receipt

Complete Receipt

Rejected Items

Damaged Items

Backorders

Automatically update Inventory.

---

# Purchase Invoice

Support:

Invoice Matching

PO Matching

Goods Receipt Matching

Status

Due Date

Tax

Discount

Attachments

---

# Supplier Payments

Integrate with existing Finance module.

Support:

Cash

Bank

Transfer

Cheque

Partial Payments

Full Payments

Outstanding Balance

---

# Accounting Integration

Reuse Accounting Engine.

Automatically create journal entries for:

Goods Receipt

Supplier Invoice

Supplier Payment

Purchase Returns

Inventory Value Changes

No duplicated accounting logic.

---

# Inventory Integration

Automatically:

Increase stock

Update valuation

Track warehouse

Track batches

Track serial numbers

Create inventory transactions

---

# Workflow

Require approvals for:

Purchase Requisition

Purchase Order

Purchase Returns

Large Purchases

Users cannot approve their own requests.

---

# Dashboard

Create widgets:

Open Requisitions

Pending Approvals

Pending Purchase Orders

Goods Awaiting Receipt

Supplier Performance

Monthly Purchases

Purchase Value

Outstanding Supplier Payments

---

# Metrics

Extend MetricsService.

Create:

ProcurementMetrics

SupplierMetrics

Support:

Purchase Value

Supplier Spend

Supplier Performance

Lead Time

Purchase Trends

Pending Orders

Outstanding Payments

---

# Reports

Extend ReportService.

Create:

Purchase Order Report

Supplier Spend Report

Supplier Performance Report

Purchase Analysis

Goods Receipt Report

Purchase Invoice Report

Outstanding Supplier Payments

Lead Time Report

---

# Charts

Extend ChartService.

Support:

Purchase Trends

Supplier Spend

Supplier Ranking

Lead Time

Department Purchases

Monthly Purchases

---

# Search

Extend Global Search.

Support:

Supplier

PO Number

RFQ

Purchase Invoice

Goods Receipt

Purchase Requisition

---

# Export

Reuse ExportService.

Support:

PDF

Excel

CSV

Print

---

# Notifications

Notify:

Requisition Submitted

Approval Required

Purchase Order Approved

Goods Received

Invoice Due

Supplier Payment Completed

Low Supplier Rating

---

# Permissions

Create:

supplier.view

supplier.create

supplier.update

supplier.delete

procurement.view

procurement.create

procurement.approve

purchase_order.view

purchase_order.create

purchase_order.approve

goods_receipt.create

supplier_payment.view

supplier_payment.create

supplier_report.view

---

# Policies

Create:

SupplierPolicy

PurchaseRequisitionPolicy

PurchaseOrderPolicy

GoodsReceiptPolicy

PurchaseInvoicePolicy

SupplierPaymentPolicy

---

# Performance

Prevent N+1 queries.

Use eager loading.

Cache procurement metrics.

Optimize supplier queries.

---

# Security

Respect Architecture Enhancement 02.

Enforce:

Permissions

Policies

Workflow Authorization

Company Isolation

Branch Isolation

Department Isolation

Reports

Metrics

Exports

---

# Testing

Generate comprehensive Feature Tests.

Suppliers

Supplier Categories

Purchase Requisition

RFQ

Supplier Quotations

Purchase Orders

Goods Receipt

Purchase Invoice

Supplier Payments

Accounting Integration

Inventory Integration

Reports

Metrics

Workflow

Permissions

Policies

Search

Export

Run:

php artisan test

---

# Manual Verification

Verify:

✓ Supplier CRUD

✓ Purchase Requisition

✓ RFQ

✓ Supplier Quotation

✓ Purchase Order

✓ Goods Receipt

✓ Inventory Updated

✓ Purchase Invoice

✓ Supplier Payment

✓ Dashboard

✓ Reports

✓ Metrics

✓ Search

✓ Export

✓ Accounting Entries

✓ Responsive UI

✓ Feature tests passing

✓ No PHP errors

✓ No JavaScript errors

---

# Acceptance Criteria

Sprint 26 is complete only if:

✓ Procurement workflow operational

✓ Inventory integration working

✓ Accounting integration working

✓ Workflow approvals operational

✓ Reports integrated

✓ Metrics integrated

✓ Dashboard operational

✓ Security enforced

✓ Tests passing

✓ Architecture maintained

---

# Stop

Stop after Sprint 26.

Wait for Sprint 27.