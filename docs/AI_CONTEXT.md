# Creative ERP - AI Development Context & Knowledge Guide

**Version**: 1.1  
**Status**: Active & Complete  
**Last Updated**: September 2026  

---

# 1. Project Context Overview

Creative ERP is a production-grade Enterprise Resource Planning system built for project-driven companies, engineering firms, contracting organizations, and construction enterprises.

It combines site operations, warehouse inventory management, material issuance workflows, project expense & salary tracking, procurement, fixed asset management, CRM, and double-entry financial accounting into a single Laravel 12 application.

---

# 2. Technology Stack Specifications

- **Backend Framework**: Laravel 12.x
- **PHP Version**: 8.4+
- **Database Engine**: MySQL 8.0+
- **Frontend Stack**: Blade, Tailwind CSS, Alpine.js, Chart.js, Axios
- **Asset Bundler**: Vite 6.x
- **Authentication**: Custom Session Authentication (Blade Web) & Laravel Sanctum (REST API)
- **Role Permissions**: Spatie Laravel Permission + Project-level role permission overrides (`Project::hasPermissionForUser`)
- **Document Export**: DomPDF, Laravel Excel

---

# 3. Development Status & Completed Modules

### Completed Enterprise Modules (20+):
1. ✅ **Multi-Tenant Platform Core**: Companies, Branches, Departments, Users, Spatie Roles & Permissions.
2. ✅ **Project Management**: Projects, Milestones, Tasks, Team Members, Meetings, Time Tracking, Discussions, Announcements.
3. ✅ **Material Requests & Material Issuances**: Site Engineer material requests, multi-level approvals, direct warehouse material issuances with stock deduction and transaction tracking.
4. ✅ **Project Expenses & Worker Salaries**: Expense tracking distinguishing between Direct Expenses (equipment, subcontracts) and Labor Costs (worker salaries/payroll).
5. ✅ **Procurement & Goods Receipts (GRN)**: Suppliers, Purchase Requisitions, RFQs, Supplier Quotations matrix, Purchase Orders, Goods Receipts (stock auto-update), Purchase Invoices, Supplier Payments.
6. ✅ **Inventory & Advanced WMS**: Products, SKUs, Variants, Categories, Brands, Warehouses, Zones, Bins, Stock Movements, Adjustments, Transfers, Reservations, Stock Counts, Valuation, Put-Away, Picking, Packing, Shipments, Returns.
7. ✅ **Double-Entry Financial Accounting Engine**: Chart of Accounts, General Ledger, Journal Entries, Automatic Posting Engine, Fiscal Years, Monthly Period Closing & Locks, Financial Reports (P&L, Balance Sheet, Cash Flow).
8. ✅ **Fixed Assets Lifecycle**: Asset Registry, Categories, Assignments, Transfers, Maintenance, Disposals, Automated Monthly Depreciation Schedules with GL Posting.
9. ✅ **CRM & Sales**: Leads, Accounts, Contacts, Opportunities (Kanban), Sales Pipelines, Quotations, Invoices, Customer Payments, Credit Notes, Refunds.
10. ✅ **In-App Documentation & Help Center**: Categories, Articles, Search interface.
11. ✅ **Website CMS**: Website Settings, Expertise Cards, Website Projects.
12. ✅ **Global Search & Metrics Engine**: Centralized search across models, KPIs, executive dashboard widgets.

---

# 4. Model Architecture & Key Entities

The system contains 120+ Eloquent models organized into key domains:

- **Core & Multi-Tenancy**: `Company`, `Branch`, `Department`, `User`, `LoginHistory`, `ActivityLog`, `Setting`, `Sequence`.
- **Project Domain**: `Project`, `ProjectMember`, `Task`, `Milestone`, `Meeting`, `TimeEntry`, `Comment`, `Document`.
- **Material Domain**: `ProjectMaterialRequest`, `ProjectMaterialRequestItem`, `ProjectMaterialIssue`, `ProjectMaterialIssueItem`.
- **Expense Domain**: `ProjectExpense` (with `scopeLabor` and `scopeDirectExpenses`).
- **Procurement Domain**: `Supplier`, `SupplierCategory`, `SupplierContact`, `PurchaseRequisition`, `SupplierQuotation`, `PurchaseOrder`, `GoodsReceipt`, `PurchaseInvoice`, `SupplierPayment`.
- **Inventory & WMS**: `Product`, `ProductCategory`, `ProductVariant`, `Warehouse`, `WarehouseZone`, `WarehouseBin`, `Inventory`, `InventoryTransaction`, `InventoryAdjustment`, `InventoryTransfer`, `InventoryReservation`, `StockCount`, `WarehouseMovement`, `WarehouseTask`, `WarehousePicking`, `WarehousePacking`, `WarehouseShipment`, `WarehouseReturn`.
- **Financial Accounting**: `ChartOfAccount`, `Account`, `AccountType`, `Journal`, `JournalEntry`, `GeneralLedger`, `FiscalYear`, `AccountingPeriod`, `ClosingEntry`, `OpeningBalance`, `Invoice`, `Payment`, `CreditNote`, `Refund`, `BankAccount`.
- **Fixed Assets**: `Asset`, `AssetCategory`, `AssetAssignment`, `AssetTransfer`, `AssetMaintenance`, `AssetDepreciation`, `AssetDisposal`.
- **CRM Domain**: `Lead`, `Account`, `Contact`, `Opportunity`, `Pipeline`, `PipelineStage`, `Activity`, `Quotation`.
- **Help Documentation**: `DocumentationCategory`, `DocumentationArticle`.

---

# 5. Core Architectural Traits & Rules for AI Development

When generating or modifying code for Creative ERP, ALWAYS enforce the following conventions:

1. **`CompanyScoped` Trait**: Apply to all tenant-owned models to enforce multi-company data isolation.
2. **`HasUuidColumn` Trait**: Apply to models with a UUID column to automatically generate UUIDs.
3. **`LogsActivity` Trait**: Apply to domain models requiring audit logging.
4. **Service Pattern**: Never place complex database queries or business operations inside controllers or Blade views. Move logic to `App\Services\...`.
5. **Form Requests**: Always create or update `App\Http\Requests\...` for request validation.
6. **Policy Checks**: Ensure authorization checks use `$this->authorize()` or Policy classes.
7. **Project Roles**: Use `$project->hasPermissionForUser($user, 'permission.name')` when checking project-specific actions.
8. **Double-Entry Balance**: Financial journal entries must verify $\sum Debits == \sum Credits$.
