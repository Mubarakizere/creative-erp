# Creative ERP Enterprise Modules Specification Guide

**Version**: 1.1  
**Document**: 05_MODULES_GUIDE  
**Status**: Approved & Active  

---

# 1. Introduction

This guide provides an exhaustive functional and architectural reference for all major domain modules operating within Creative ERP.

---

# 2. Domain Modules Overview

## 🏢 Module 1: Platform & Organization Governance
- **Purpose**: Governs multi-company tenancy, organizational hierarchy, users, roles, dynamic permissions, system settings, document sequence numbers, activity audit logs, and website CMS.
- **Key Models**: `Company`, `Branch`, `Department`, `User`, `LoginHistory`, `ActivityLog`, `Setting`, `Sequence`, `WebsiteSetting`, `ExpertiseCard`, `WebsiteProject`.
- **Controllers**: `CompanyController`, `BranchController`, `DepartmentController`, `UserController`, `RoleController`, `PermissionController`, `SettingController`.
- **Key Capabilities**:
  - Multi-company data scoping via `CompanyScoped` Eloquent trait.
  - Fine-grained permissions powered by Spatie Laravel Permission.
  - Active user status validation (`Active`, `Inactive`, `Suspended`, `Locked`).
  - Document sequence generator for automated code formatting (`PR-2026-0001`, `PO-2026-0001`, `INV-2026-0001`).

---

## 🏗️ Module 2: Project Management & Site Operations
- **Purpose**: Manages project lifecycle execution, estimated vs. actual budgets, task breakdowns, milestone progress, daily site operations, team role assignments, time tracking, meetings, discussions, and document versioning.
- **Key Models**: `Project`, `ProjectMember`, `Task`, `Milestone`, `Meeting`, `TimeEntry`, `Comment`, `Document`, `DocumentCategory`, `Announcement`.
- **Controllers**: `ProjectController`, `ProjectTeamController`, `TaskController`, `MilestoneController`, `MeetingController`, `TimeEntryController`, `CommentController`, `DocumentController`.
- **Key Capabilities**:
  - Project status pipeline (`Planning`, `Active`, `On Hold`, `Completed`, `Cancelled`, `Closed`).
  - Project-level authorization overrides via `Project::hasPermissionForUser($user, $permission)`.
  - Daily time tracking with live timer widget and timesheet reports.
  - Site meeting scheduling, participant invite status, and minute attachments.
  - Multi-version document upload repository with category tags and download authorization.

---

## 📦 Module 3: Material Requests & Material Issuances
- **Purpose**: Bridges site material needs with warehouse inventory. Manages site engineer material requests, approval workflows, and direct material issuances to project tasks with automated stock deduction.
- **Key Models**: `ProjectMaterialRequest`, `ProjectMaterialRequestItem`, `ProjectMaterialIssue`, `ProjectMaterialIssueItem`, `Inventory`, `WarehouseMovement`.
- **Controllers**: `ProjectMaterialRequestController`, `ProjectMaterialIssueController`.
- **Key Capabilities**:
  - **Material Requests**: Engineers submit item requests with quantities and required dates. Managers approve, reject, or convert requests to Purchase Requisitions.
  - **Material Issuance**: Warehouse keepers issue requested materials directly to a specified project/task.
  - **Stock Deduction**: Validates available stock, creates `WarehouseMovement` record (type: `Issue`), and decreases `Inventory` levels in real-time.

---

## 💸 Module 4: Project Expenses & Labor/Salary Tracking
- **Purpose**: Tracks project-specific costs and categorizes spending into direct project expenses and worker salaries/labor payroll.
- **Key Models**: `ProjectExpense`, `Project`, `User`.
- **Controllers**: `ProjectExpenseController`.
- **Key Capabilities**:
  - Classifies expenses using Eloquent scopes: `scopeLabor()` (Worker Salary, Labor, Payroll) and `scopeDirectExpenses()` (Equipment, Subcontractors, Fuel, Logistics).
  - Automatically recalculates and updates `Project::actual_cost`.
  - Supports multi-currency amounts and receipt document attachments.

---

## 🛒 Module 5: Procurement & Goods Receipts (GRN)
- **Purpose**: Handles end-to-end supply chain purchasing from initial purchase requisition to vendor quotation selection, purchase orders, goods receipt receiving, purchase invoices, and supplier payments.
- **Key Models**: `Supplier`, `SupplierCategory`, `SupplierContact`, `SupplierPerformance`, `PurchaseRequisition`, `PurchaseRequisitionItem`, `SupplierQuotation`, `SupplierQuotationItem`, `PurchaseOrder`, `PurchaseOrderItem`, `GoodsReceipt`, `GoodsReceiptItem`, `PurchaseInvoice`, `PurchaseInvoiceItem`, `SupplierPayment`.
- **Controllers**: `SupplierController`, `PurchaseRequisitionController`, `SupplierQuotationController`, `PurchaseOrderController`, `GoodsReceiptController`, `PurchaseInvoiceController`, `SupplierPaymentController`.
- **Key Capabilities**:
  - Multi-supplier RFQ quotation comparison matrix.
  - PO approval workflows with multi-tiered authority limits.
  - **Goods Receipt (GRN)**: Receiving items into designated warehouse bins, generating stock transactions, and triggering automated GL journal entries (Debit Inventory, Credit AP).
  - **3-Way Match Validation**: Verification across PO, Goods Receipt, and Purchase Invoice.

---

## 🏭 Module 6: Inventory & Advanced Warehouse Management (WMS)
- **Purpose**: Manages physical warehouse storage layout, stock levels, product catalog, variants, movements, transfers, reservations, stock counts, put-away tasks, picking lists, packing, shipments, and customer returns.
- **Key Models**: `Product`, `ProductCategory`, `Brand`, `UnitOfMeasure`, `ProductVariant`, `Warehouse`, `WarehouseZone`, `WarehouseBin`, `Inventory`, `InventoryTransaction`, `InventoryAdjustment`, `InventoryTransfer`, `InventoryReservation`, `StockCount`, `StockCountItem`, `WarehouseMovement`, `WarehouseTask`, `WarehousePicking`, `WarehousePacking`, `WarehouseShipment`, `WarehouseReturn`.
- **Controllers**: `ProductController`, `ProductCategoryController`, `BrandController`, `UnitOfMeasureController`, `WarehouseController`, `WarehouseZoneController`, `WarehouseBinController`, `InventoryAdjustmentController`, `InventoryTransferController`, `InventoryReservationController`, `StockCountController`, `InventoryValuationController`, WMS Task Controllers.
- **Key Capabilities**:
  - Multi-warehouse location control down to Zone and Bin levels.
  - Stock reservations for approved site requests to prevent over-allocation.
  - Inventory valuation reports using Weighted Average and FIFO methods.

---

## 🏛️ Module 7: Financial Accounting & Auto-Posting Engine
- **Purpose**: Implements a double-entry general ledger accounting system, chart of accounts, automated journal entries, fiscal year management, accounting period locks, and financial reporting.
- **Key Models**: `ChartOfAccount`, `Account`, `AccountType`, `Journal`, `JournalEntry`, `GeneralLedger`, `FiscalYear`, `AccountingPeriod`, `ClosingEntry`, `OpeningBalance`, `Invoice`, `Payment`, `CreditNote`, `Refund`, `BankAccount`, `Budget`.
- **Controllers**: `ChartOfAccountController`, `JournalController`, `LedgerController`, `FiscalPeriodController`, `InvoiceController`, `PaymentController`, `CreditNoteController`, `RefundController`, `FinancialReportController`, `BudgetController`, `AnalyticsController`.
- **Key Capabilities**:
  - Double-entry validation ($\sum Debits = \sum Credits$).
  - Period locking to block backdated posting into closed accounting periods.
  - Financial statements: Profit & Loss (P&L), Balance Sheet, Cash Flow, Trial Balance.
  - Automatic posting for Goods Receipts, Customer Invoices, Payments, Refunds, and Asset Depreciations.

---

## 🚜 Module 8: Fixed Assets & Equipment Management
- **Purpose**: Tracks long-term equipment, machinery, site vehicles, and office hardware across acquisition, project assignment, maintenance, location transfers, monthly depreciation, and disposal.
- **Key Models**: `Asset`, `AssetCategory`, `AssetAssignment`, `AssetTransfer`, `AssetMaintenance`, `AssetDepreciation`, `AssetDisposal`.
- **Controllers**: `AssetController`, `AssetCategoryController`, `DepreciationController`, `AssetTransferController`, `AssetMaintenanceController`, `AssetDisposalController`.
- **Key Capabilities**:
  - Automated depreciation schedule generator (Straight Line, Declining Balance) with direct General Ledger posting.
  - Equipment maintenance tracking with scheduled downtime and cost logging.
  - Site-to-site asset transfer requests with approval workflow.

---

## 💼 Module 9: CRM & Sales Pipeline
- **Purpose**: Manages customer leads, corporate accounts, contacts, sales opportunities, quotation generation, and customer receivables.
- **Key Models**: `Lead`, `Account`, `Contact`, `Opportunity`, `Pipeline`, `PipelineStage`, `Activity`, `Quotation`, `QuotationItem`.
- **Controllers**: `LeadController`, `AccountController`, `ContactController`, `OpportunityController`, `PipelineController`, `ActivityController`, `QuotationController`.
- **Key Capabilities**:
  - Drag-and-drop opportunity Kanban board across pipeline stages.
  - Lead conversion to Client Account, Contact, and Opportunity.
  - Sales quotation builder with duplicate, export, and status approval features.

---

## 📖 Module 10: In-App Documentation & Help Center
- **Purpose**: Provides an integrated searchable knowledge base within the ERP user interface for user onboarding and operational reference.
- **Key Models**: `DocumentationCategory`, `DocumentationArticle`.
- **Controllers**: `DocumentationController`, `DocumentationCategoryController`, `DocumentationArticleController`.
- **Key Capabilities**:
  - Searchable article database with category indexing.
  - Rich markdown/HTML article rendering.
