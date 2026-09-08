# Creative ERP - Enterprise Resource Planning System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL_8.0-4479A1?style=for-the-badge&logo=mysql)](https://mysql.com)
[![Build](https://img.shields.io/badge/Vite-6.x-646CFF?style=for-the-badge&logo=vite)](https://vitejs.dev)
[![License](https://img.shields.io/badge/License-Proprietary-blue?style=for-the-badge)]()

**Creative ERP** is a modern, modular, enterprise-grade Resource Planning platform engineered specifically for contracting, civil engineering, construction, architecture, and project-driven enterprises.

It centralizes every aspect of project lifecycle execution—from sales quotations, procurement, and warehouse inventory management to material issuances, labor/expense tracking, fixed asset management, and double-entry financial accounting.

---

## 🚀 Key Modules & Capabilities

### 1. 🏗️ Project Management & Site Operations
- **Project Lifecycle**: Track budgets, actual costs, timelines, milestones, tasks, and completion percentages.
- **Material Requests & Issuances**:
  - Site engineers submit material requests.
  - Multi-level approval workflows for material issuance.
  - Direct issuance from warehouse inventory to project/task sites with automatic stock deduction and transaction logs.
- **Project Expenses & Labor/Salary Tracking**:
  - Classify expenses into Direct Expenses (equipment, subcontracts, site logistics) and Labor Costs (worker salaries, site wages).
  - Real-time project cost rollups against estimated budgets.
- **Time Tracking & Timesheets**: Integrated timer, daily timesheets, and task time allocation.
- **Meetings & Collaboration**: Site meeting agendas, minutes, participant RSVPs, document attachments, and threaded discussion boards with comment pinning.

### 2. 🛒 Procurement & Goods Receipts
- **Supplier Lifecycle**: Supplier profiles, categories, performance metrics, and contact registries.
- **Purchase Requisitions & RFQs**: Requisition creation, multi-supplier RFQs, and automated quotation comparison matrices.
- **Purchase Orders (PO)**: Multi-level PO approval workflow, item tracking, and status monitoring.
- **Goods Receipts (GRN)**: Receive physical goods against POs with warehouse stock auto-updates and line-item inspection.
- **Purchase Invoices & Supplier Payments**: 3-way matching (PO, GRN, Invoice), invoice approvals, and payment processing.

### 3. 📦 Inventory & Advanced Warehouse Management (WMS)
- **Product Catalog**: Products, SKUs, variants, categories, brands, and units of measure.
- **Stock Tracking**: Multi-warehouse stock tracking with location zones and specific bin placements.
- **Warehouse Operations**: Stock transfers, manual adjustments, stock reservations, cycle counts, put-away tasks, picking lists, packing slips, shipment tracking, and customer return processing.
- **Inventory Valuation**: Automated valuation calculations using FIFO / Weighted Average.

### 4. 💼 Double-Entry Financial Accounting Engine
- **Chart of Accounts (COA)**: Hierarchical chart of accounts supporting Assets, Liabilities, Equity, Revenue, and Expense categories.
- **General Ledger & Journals**: Double-entry journal vouchers with manual and automated system posting.
- **Automatic Financial Posting**: Real-time automatic GL posting for Goods Receipts, Customer Invoices, Payments, Refunds, and Asset Depreciations.
- **Fiscal Years & Period Locks**: Fiscal year creation, monthly period locking, and automated closing entries.
- **Financial Reporting & Statements**: Real-time generation of Profit & Loss (P&L), Balance Sheet, Cash Flow Statement, and Trial Balance.

### 5. 🏢 Fixed Assets & Equipment Management
- **Asset Registry**: Track machinery, vehicles, site tools, and office assets with serial numbers and custom attributes.
- **Asset Life Cycle**: Asset assignments to employees/projects, location transfers, scheduled maintenance, and disposal workflows.
- **Depreciation Engine**: Automated monthly depreciation schedule generator with direct General Ledger posting.

### 6. 🤝 CRM & Sales Pipeline
- **Leads & Opportunities**: Pipeline management with interactive drag-and-drop Kanban view.
- **Sales Quotations**: Professional quotation generator with custom line items, approval status, duplicate tool, and export options.
- **Invoicing & Customer Receivables**: Issue invoices, track customer balances, record payments, manage credit notes, and generate customer account statements.

### 7. 🛡️ Enterprise Security & Governance
- **Role-Based Access Control (RBAC)**: Fine-grained permissions powered by Spatie Laravel Permission.
- **Project-Level Role Security**: Custom project member roles (`Project Manager`, `Site Engineer`, `Quantity Surveyor`, etc.) with local permission evaluation (`hasPermissionForUser`).
- **Multi-Company Data Isolation**: Tenant scoping via `CompanyScoped` trait.
- **Audit Logging & Activity Tracking**: Immutable tracking of sensitive user actions and model changes.
- **In-App Documentation**: Built-in searchable Help Center and documentation articles.

---

## 🛠️ Technology Stack

| Layer | Technology |
| :--- | :--- |
| **Framework** | Laravel 12.x |
| **Language** | PHP 8.4+ |
| **Database** | MySQL 8.0+ |
| **Frontend** | Blade, Tailwind CSS, Alpine.js, Chart.js, Axios |
| **Build Tool** | Vite |
| **Permissions** | Spatie Laravel Permission |
| **API Auth** | Laravel Sanctum |
| **Document Export** | DomPDF, Laravel Excel |

---

## 📋 System Architecture & Patterns

Creative ERP enforces a strict service-driven modular architecture:
- **Thin Controllers**: Controllers only handle HTTP request routing and simple view/response assembly.
- **Service Layer**: Business logic lives exclusively within dedicated service classes (`App\Services\...`).
- **Form Requests**: Request validation and payload sanitization are isolated in dedicated `FormRequest` classes.
- **Policies & Gates**: Feature authorization is governed by Laravel Policies and Spatie Permissions.
- **Traits**:
  - `CompanyScoped`: Automatic multi-company data isolation.
  - `HasUuidColumn`: Automatic UUID generation.
  - `LogsActivity`: Audit trail logging.

---

## ⚡ Quick Start & Installation

### Prerequisites
- PHP 8.4 or higher
- Composer 2.x
- Node.js 18+ and npm
- MySQL 8.0+

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/Mubarakizere/creative-erp.git
   cd creative-erp
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Frontend Dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Configure your database credentials in `.env`:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=creative_erp
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run Migrations & Seed Database**
   ```bash
   php artisan migrate --seed
   ```

6. **Create Storage Symbolic Link**
   ```bash
   php artisan storage:link
   ```

7. **Compile Frontend Assets & Run Local Server**
   ```bash
   # Terminal 1: Frontend compiler
   npm run dev

   # Terminal 2: Laravel server
   php artisan serve
   ```

8. **Run Background Queue Worker & Scheduler**
   ```bash
   # Terminal 3: Queue worker
   php artisan queue:work

   # Terminal 4: Scheduler
   php artisan schedule:work
   ```

---

## 📖 Documentation & Guides

Comprehensive documentation is available in the [`docs/`](file:///c:/Users/mouba/creative-erp/docs) directory:

- [Project Rules & Development Standards](file:///c:/Users/mouba/creative-erp/docs/00_PROJECT_RULES.md)
- [Project Vision & Strategic Architecture](file:///c:/Users/mouba/creative-erp/docs/01_PROJECT_VISION.md)
- [Business Analysis & Workflow Specs](file:///c:/Users/mouba/creative-erp/docs/02_BUSINESS_ANALYSIS.md)
- [Authentication & Security Architecture](file:///c:/Users/mouba/creative-erp/docs/03_AUTHENTICATION.md)
- [Environment & Setup Guide](file:///c:/Users/mouba/creative-erp/docs/04_PROJECT_SETUP.md)
- [Enterprise Modules Specification Guide](file:///c:/Users/mouba/creative-erp/docs/05_MODULES_GUIDE.md)
- [REST API Endpoint Reference](file:///c:/Users/mouba/creative-erp/docs/06_API_REFERENCE.md)
- [Release Notes v1.1.0](file:///c:/Users/mouba/creative-erp/docs/releases/v1.1.0-expanded-enterprise.md)

---

## 🧪 Testing

Run feature and unit test suites with PHPUnit:
```bash
php artisan test
```

---

## 📄 License

Proprietary Software. All rights reserved by **Creative ERP Development Team**.
