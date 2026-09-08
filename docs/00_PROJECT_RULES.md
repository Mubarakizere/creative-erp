# Creative ERP Development Rules & Standards

**Version**: 1.1  
**Status**: Active  
**Master Standard**: Yes  

---

# 1. Project Overview & Architecture Vision

Creative ERP is a modular, scalable, multi-company Enterprise Resource Planning (ERP) solution designed for contracting, civil engineering, architecture, and project-based enterprises.

The platform must adhere to the following architecture principles:
- **Modular Architecture**: Features are grouped logically into domain modules (Projects, Inventory, Procurement, Accounting, Assets, CRM, CMS).
- **Multi-Tenant Isolation**: Every tenant entity operates under complete company isolation enforced by the `CompanyScoped` model trait.
- **Service-Driven Business Logic**: Controllers MUST NOT contain raw database transactions or complex calculations. All domain logic is encapsulated inside dedicated Service classes (`App\Services\...`).
- **REST & Web Coexistence**: Domain logic in services is consumed by both Blade Web Controllers and REST API Controllers.

---

# 2. Technology Stack

### Backend
- **Framework**: Laravel 12.x
- **Language**: PHP 8.4+
- **Database**: MySQL 8.0+
- **API Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission + Native Laravel Policies

### Frontend
- **Templating**: Laravel Blade
- **Styling**: Tailwind CSS
- **Interactivity**: Alpine.js
- **Charts & Dashboards**: Chart.js
- **HTTP Client**: Axios
- **Asset Bundler**: Vite 6.x

---

# 3. Coding Standards & Best Practices

1. **PSR-12**: All PHP code must adhere strictly to PSR-12 code style guidelines.
2. **Controller Scoping**:
   - Web Controllers live in `App\Http\Controllers\Admin\...` or domain namespaces.
   - API Controllers live in `App\Http\Controllers\API\...`.
   - Controllers must remain lightweight, delegating to Form Requests for input validation and Services for execution.
3. **Form Requests**: All mutating actions (`store`, `update`) MUST use dedicated Form Request classes (`App\Http\Requests\...`).
4. **Policies & Authorization**:
   - Controllers must authorize actions via Policies (`$this->authorize('update', $model)`) or middleware (`middleware('can:permission_name')`).
   - Project-level authorization must utilize `Project::hasPermissionForUser($user, $permission)` to support project role overrides.
5. **Database Transactions**: Any operation mutating multiple models (e.g. Goods Receipt creating stock & GL entries, or Material Issue decreasing inventory) MUST be wrapped inside `DB::transaction()`.

---

# 4. Database & Model Conventions

1. **Required Columns**: Every primary domain model MUST include:
   - `id` (BigIncrements / Primary Key)
   - `uuid` (UUID string indexed for external API reference)
   - `company_id` (Foreign Key to `companies`, nullable for global entities)
   - `created_by` & `updated_by` (Foreign Key to `users`)
   - `created_at` & `updated_at` (Timestamps)
   - `deleted_at` (SoftDeletes where appropriate)
2. **Model Traits**:
   - `CompanyScoped`: Automatically scope Eloquent queries to the current authenticated user's `company_id`.
   - `HasUuidColumn`: Automatically populate UUID fields during model creation.
   - `LogsActivity`: Automatically record audit logs for creation, updates, and deletion.
3. **Foreign Keys & Constraints**: All foreign keys must enforce explicit ON DELETE / ON UPDATE actions (e.g. `cascadeOnDelete()`, `restrictOnDelete()`).

---

# 5. Domain Module Standards

### A. Material Requests & Issuances Workflow
- Material requests are submitted by site engineers or project managers.
- Material Issuances (`ProjectMaterialIssue`) draw inventory from a designated `Warehouse` for a `Project` / `Task`.
- **Validation**: Material issues MUST verify that available warehouse stock is greater than or equal to the requested quantity.
- **Stock Movement**: Material issuance MUST emit a `WarehouseMovement` record of type `Issue` and update `Inventory` stock levels.

### B. Project Expenses & Labor/Salary Tracking
- Expenses linked to projects (`ProjectExpense`) must define category types (`Worker Salary`, `Labor`, `Payroll`, `Equipment`, `Materials`, `Subcontractor`, etc.).
- `ProjectExpense::scopeLabor()` and `ProjectExpense::scopeDirectExpenses()` MUST be used for reporting rollup distinctions.
- Expenses update the `actual_cost` of the parent `Project`.

### C. Procurement & Goods Receipts (GRN)
- Requisitions -> RFQs -> Supplier Quotations -> Purchase Orders -> Goods Receipts -> Purchase Invoices -> Supplier Payments.
- Goods Receipts MUST update warehouse stock levels and create corresponding `InventoryTransaction` records.
- 3-Way Matching MUST be enforced before approving Purchase Invoices.

### D. Financial Accounting Engine
- General Ledger (`GeneralLedger`) uses double-entry accounting where Total Debits equal Total Credits.
- Closed `AccountingPeriod` records prevent any backdated journal entries or automated posting.
- Automatic posting triggers exist for:
  - Goods Receipt -> Inventory Debit / Accounts Payable Credit
  - Customer Invoice -> Accounts Receivable Debit / Revenue Credit
  - Customer Payment -> Cash/Bank Debit / Accounts Receivable Credit
  - Asset Depreciation -> Depreciation Expense Debit / Accumulated Depreciation Credit

### E. Fixed Assets Lifecycle
- Assets MUST be registered under an `AssetCategory` defining depreciation method (Straight Line, Declining Balance) and useful life.
- Asset transfers, disposals, and maintenance MUST be logged with approval status.
- Automated monthly depreciation jobs generate `AssetDepreciation` records and post corresponding GL journals.

---

# 6. User Interface & Frontend Rules

1. **Design System**: Use responsive, modern, dark-mode ready components.
2. **Feedback & Alerts**: All form submissions must present clear success or error alerts via session flash messages or inline Alpine.js notifications.
3. **Data Grids**: Index pages MUST provide:
   - Search bar with instant filtering.
   - Status filters & date pickers.
   - Pagination controls.
   - Export buttons (PDF, Excel, CSV).
4. **No Business Logic in Blade**: Blade views must only present data passed from controllers or view composers.

---

# 7. Documentation Rule

Every newly added feature, route, or model MUST update the repository documentation files (`README.md`, `docs/05_MODULES_GUIDE.md`, `docs/06_API_REFERENCE.md`) before merging or releasing.
