<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentationCategory;
use App\Models\DocumentationArticle;

class DocumentationSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => '1. Getting Started',
                'slug' => 'getting-started',
                'order' => 1,
                'articles' => [
                    ['title' => 'What is Creative Century Engineering?', 'slug' => 'what-is-creative-erp', 'content' => "## What is Creative Century Engineering?\n\nCreative Century Engineering is a comprehensive Enterprise Resource Planning system designed to streamline your business operations. It unifies all major departments including Finance, Procurement, Inventory, Projects, and CRM into one centralized platform.\n\n### Single Company Deployment\n\n> [!IMPORTANT]\n> This installation of Creative Century Engineering operates strictly as a **Single Company Deployment**. While the system supports multiple branches, departments, and warehouses, all operations belong to a single overarching entity. You will not need to configure multi-tenant environments or select a company context during normal operations."],
                    ['title' => 'Dashboard Overview', 'slug' => 'dashboard-overview', 'content' => "## Dashboard Overview\n\nWhen you log in, you are greeted by the Dashboard. It provides a real-time summary of your activities and key performance indicators (KPIs).\n\n### Key Features:\n- **KPI Cards:** High-level metrics such as total revenue, pending approvals, and active projects.\n- **Charts:** Visual representations of your sales, expenses, and inventory.\n- **Recent Activity:** A feed of the latest actions taken by you and your team.\n- **Notifications:** Alerts for items that require your immediate attention."],
                ]
            ],
            [
                'name' => '2. Organization Setup',
                'slug' => 'organization-setup',
                'order' => 2,
                'articles' => [
                    ['title' => 'Branches & Departments', 'slug' => 'branches-departments', 'content' => "## Organization Structure\n\n### Branches\nBranches represent physical locations or regional offices of the company. Each branch can have its own departments and assigned employees.\n\n### Departments\nDepartments categorize your workforce by function (e.g., HR, Finance, Engineering). Users are assigned to departments within specific branches to maintain organizational clarity."],
                ]
            ],
            [
                'name' => '3. CRM (Customer Relationship)',
                'slug' => 'crm',
                'order' => 3,
                'articles' => [
                    ['title' => 'CRM Workflow', 'slug' => 'crm-workflow', 'content' => "## CRM Workflow\n\nOur CRM follows a standard sales pipeline:\n\n**Lead** ↓\n**Opportunity** ↓\n**Quotation** ↓\n**Client**\n\n### Steps:\n1. **Leads:** Enter raw prospective customers.\n2. **Opportunities:** Convert promising leads into opportunities with an expected value and probability.\n3. **Pipelines:** Move opportunities through kanban stages (e.g., Discovery, Proposal, Negotiation).\n4. **Clients:** Once a deal is won, the account officially becomes an active Client."],
                ]
            ],
            [
                'name' => '4. Projects',
                'slug' => 'projects',
                'order' => 4,
                'articles' => [
                    ['title' => 'Managing Projects', 'slug' => 'managing-projects', 'content' => "## Managing Projects\n\nProjects are the core of operational delivery.\n\n### Workflow:\n1. **Create Project:** Define the scope, client, budget, and planned schedule.\n2. **Assign Team:** Add users to the project team with specific project roles.\n3. **Milestones & Tasks:** Break the project down into measurable milestones and actionable tasks.\n4. **Material Requests & Issuances:** Field engineers request materials from inventory, and storekeepers issue requested items directly from warehouses to project tasks with automated stock deduction.\n5. **Project Expenses & Salaries:** Log direct site expenses and worker salary/payroll costs for real-time project cost rollups."],
                    ['title' => 'Material Issuances', 'slug' => 'material-issuances', 'content' => "## Material Issuances\n\nMaterial Issuances transfer materials from warehouse stock directly to project sites and tasks.\n\n### Workflow:\n1. **Request:** Site Engineer submits a Material Request for a project or task.\n2. **Approval:** Request is reviewed and approved by the Warehouse Manager or Project Manager.\n3. **Issue:** A Material Issue document is created selecting the source Warehouse.\n4. **Stock Deduction:** Available stock in the warehouse is automatically reduced, and a Warehouse Movement record is created."],
                ]
            ],
            [
                'name' => '5. Procurement',
                'slug' => 'procurement',
                'order' => 5,
                'articles' => [
                    ['title' => 'Procurement Workflow', 'slug' => 'procurement-workflow', 'content' => "## Procurement Workflow\n\nThe standard flow for purchasing goods is strictly controlled:\n\n**Supplier** ↓\n**Purchase Requisition (PR)** ↓\n*Approval* ↓\n**Purchase Order (PO)** ↓\n**Goods Receipt (GRN)** ↓\n**Purchase Invoice** ↓\n**Payment**\n\n### How to Create a Purchase Requisition\n1. Open the sidebar and click **Procurement**.\n2. Click **Purchase Requisitions**.\n3. Click **Create**.\n4. Select the required items and enter quantities.\n5. Save and submit for approval.\n\nOnce approved, it can be converted into a Purchase Order by the Procurement team."],
                ]
            ],
            [
                'name' => '6. Inventory & Warehouse',
                'slug' => 'inventory-warehouse',
                'order' => 6,
                'articles' => [
                    ['title' => 'Stock Movements', 'slug' => 'stock-movements', 'content' => "## Inventory Management\n\nCreative Century Engineering supports multiple warehouses and bin locations.\n\n### Core Concepts:\n- **Products:** Defined centrally with their units of measure and categories.\n- **Warehouses & Bins:** Physical storage locations.\n- **Movements:** Every change in inventory is logged as a stock movement.\n\n### How to Receive Goods\nGoods Receipt Notes (GRNs) are typically generated from Purchase Orders. \n1. Go to **Procurement > Goods Receipts**.\n2. Select the pending PO.\n3. Verify the physical quantities received against the PO.\n4. Confirm the receipt, which automatically updates the warehouse stock."],
                ]
            ],
            [
                'name' => '7. Finance & Accounting',
                'slug' => 'finance-accounting',
                'order' => 7,
                'articles' => [
                    ['title' => 'Financial Workflows', 'slug' => 'financial-workflows', 'content' => "## Finance & Accounting\n\n### Invoicing\nClient invoices are generated for goods or services delivered. Payments can be recorded against these invoices.\n\n### Accounting\nThe system uses a robust **Chart of Accounts** and **Double-Entry Journal System**.\n- When an invoice is approved, a journal entry is automatically posted.\n- When a payment is recorded, the general ledger is updated instantly.\n\n### Chart of Accounts\nThe structure dictates how financial reporting (Profit & Loss, Balance Sheet) is generated. Only Finance Managers should modify the Chart of Accounts."],
                    [
                        'title' => 'How Chart of Accounts Works',
                        'slug' => 'how-chart-of-accounts-works',
                        'content' => "## Understanding the Chart of Accounts: A Simple Guide\n\n### What is a Chart of Accounts?\n\nThe **Chart of Accounts (COA)** is the master list of all financial accounts used by your business to organize transactions and track financial health.\n\nThink of your Chart of Accounts as a filing cabinet with 5 main drawers. Every single cent that enters or leaves your company is stored in one of these 5 drawers:\n\n1. **Assets**: What your business owns (Cash, Bank Accounts, Equipment, Client Debts).\n2. **Liabilities**: What your business owes to others (Supplier Bills, Bank Loans, Taxes).\n3. **Equity**: Net worth and owner investments.\n4. **Revenue / Income**: Money earned from selling products or engineering services.\n5. **Expenses**: Money spent to operate your business (Rent, Salaries, Utilities, Office Supplies).\n\n---\n\n### Standard Account Numbering System\n\nTo keep accounts organized, accountants assign a numerical code to every account:\n\n| Account Range | Category | Purpose | Example |\n| :--- | :--- | :--- | :--- |\n| **1000 - 1999** | **Assets** | Things owned by the company | `1010 - Bank Account`, `1020 - Accounts Receivable` |\n| **2000 - 2999** | **Liabilities** | Money owed to suppliers/banks | `2010 - Accounts Payable` |\n| **3000 - 3999** | **Equity** | Owner's capital & earnings | `3010 - Retained Earnings` |\n| **4000 - 4999** | **Revenue / Income** | Income from clients & sales | `4010 - Consulting Revenue` |\n| **5000 - 5999** | **Expenses** | Operating costs | `5010 - Salaries Expense`, `5020 - Rent Expense` |\n\n---\n\n### Real-World Business Examples\n\nHere is how your Chart of Accounts organizes real business activities:\n\n#### Example 1: Receiving Cash for a Consulting Service (RWF 2,000,000)\n- **Account Used 1**: `1010 - Bank Account` (Asset increases)\n- **Account Used 2**: `4010 - Service Revenue` (Income increases)\n\n#### Example 2: Paying Monthly Office Rent (RWF 400,000)\n- **Account Used 1**: `5020 - Rent Expense` (Expense increases)\n- **Account Used 2**: `1010 - Bank Account` (Asset decreases)\n\n#### Example 3: Purchasing Office Laptops on Credit from a Supplier (RWF 1,200,000)\n- **Account Used 1**: `1050 - Office Equipment` (Asset increases)\n- **Account Used 2**: `2010 - Accounts Payable` (Liability increases)\n\n---\n\n### Account Hierarchy: Parent & Sub-Accounts\n\nAccounts can be organized in a parent-child hierarchy to make reports cleaner:\n\n- **1000 - Bank Accounts** (Parent Account)\n  - `1010 - Equity Bank (Main)` (Sub-account)\n  - `1015 - Bank of Kigali (Project Fund)` (Sub-account)\n  - `1020 - Petty Cash` (Sub-account)\n\nSub-accounts track detailed balances while automatically rolling up into total summary figures on your **Balance Sheet** and **Profit & Loss** statements.\n\n---\n\n### Managing Accounts in Creative ERP\n\n1. **Creating Accounts**: Go to **Finance > Accounting > Chart of Accounts** and click **New Account**. Enter the unique code, name, and select the category.\n2. **System Accounts**: Critical core accounts (like Accounts Receivable and Accounts Payable) are protected as **System Accounts** so automated invoice and payment workflows run smoothly without interruption.\n3. **Deactivation**: If an account is no longer needed, you can toggle its status to **Inactive** so it no longer appears in dropdown selectors while keeping historical audit reports intact."
                    ],
                    [
                        'title' => 'How Journal Entries Work',
                        'slug' => 'how-journal-entries-work',
                        'content' => "## Understanding Journal Entries: A Simple Guide\n\n### What is a Journal Entry?\n\nA **Journal Entry** is the official financial record of a business transaction. Whenever money moves into, out of, or within your business, a Journal Entry is created to record the details.\n\nIn double-entry accounting, every journal entry MUST balance:\n- **Total Debits = Total Credits**\n\n---\n\n### The Two Sides of Every Transaction: Debit & Credit\n\nEvery transaction affects at least two accounts:\n1. **Debit (Left Side)**: Shows where value came in or where money was spent (e.g., buying assets, paying expenses).\n2. **Credit (Right Side)**: Shows where value came from or how it was paid for (e.g., bank account, supplier liability, revenue).\n\n---\n\n### Real-World Business Examples\n\n#### Example 1: Paying Monthly Office Rent (RWF 500,000 via Bank)\n- **Debit**: `5020 - Rent Expense` (+ RWF 500,000)\n- **Credit**: `1010 - Bank Account` (- RWF 500,000)\n\n#### Example 2: Issuing a Client Invoice for Engineering Services (RWF 5,000,000)\n- **Debit**: `1020 - Accounts Receivable` (+ RWF 5,000,000)\n- **Credit**: `4010 - Service Revenue` (+ RWF 5,000,000)\n\n#### Example 3: Receiving Client Payment into the Bank (RWF 5,000,000)\n- **Debit**: `1010 - Bank Account` (+ RWF 5,000,000)\n- **Credit**: `1020 - Accounts Receivable` (- RWF 5,000,000)\n\n---\n\n### How Automated Journal Entries Work in Creative ERP\n\nIn Creative ERP, you do not need to manually write every single journal entry. The system creates journal entries automatically when you:\n1. Approve a Customer Invoice or Vendor Bill.\n2. Record a Payment against an Invoice or Purchase Order.\n3. Issue Raw Materials from the Central Warehouse to a Project Site.\n\nFor custom adjustments (such as depreciation or bank fee reconciliation), authorized accountants can create manual Journal Entries directly from **Finance > Accounting > Journal Entries**."
                    ],
                    [
                        'title' => 'How Project Financials Connect to Accounting',
                        'slug' => 'how-project-financials-connect-to-accounting',
                        'content' => "## How Project Financials Connect to Accounting\n\n### Overview\n\nIn Creative ERP, every project has its own financial budget and profitability tracking, which connects directly to your company Chart of Accounts and General Ledger.\n\nEvery financial transaction in the system can carry a **Project ID**. When a transaction is tagged with a Project ID, it updates both your main company financial ledgers and your individual project P&L report.\n\n---\n\n### Key Financial Connections\n\n#### 1. Purchasing & Direct Project Expenses\nWhen you buy materials or pay site contractors for a project:\n- **Debit**: Project Expense Account (`5000`) tagged with `Project ID`.\n- **Credit**: Bank/Cash (`1010`) or Accounts Payable (`2100`).\n- *Result*: Increases main company expenses AND updates project actual cost.\n\n#### 2. Warehouse Stock Issuance to Site\nWhen raw materials move from warehouse storage to a project site:\n- **Debit**: Project Material Cost (`5100`) tagged with `Project ID`.\n- **Credit**: Raw Materials Inventory (`1400`).\n- *Result*: Reduces warehouse stock value AND adds actual material cost to the project.\n\n#### 3. Project Invoicing & Client Billing\nWhen billing a client for project milestone completions:\n- **Debit**: Accounts Receivable (`1020`).\n- **Credit**: Project Revenue (`4000`) tagged with `Project ID`.\n- *Result*: Records company client debt AND registers earned project income.\n\n---\n\n### Automated Project Profitability Calculation\n\nThe system continuously aggregates all tagged entries to display real-time project profitability:\n\n**Net Project Profit = Invoiced Revenue - (Direct Expenses + Material Costs + Labor Costs)**"
                    ]
                ]
            ],
            [
                'name' => '8. Assets',
                'slug' => 'assets',
                'order' => 8,
                'articles' => [
                    ['title' => 'Fixed Asset Management', 'slug' => 'fixed-asset-management', 'content' => "## Asset Management\n\nTrack company-owned fixed assets (e.g., Vehicles, Machinery, Computers).\n\n### Lifecycle:\n1. **Create/Acquire:** Register the asset and assign it a category.\n2. **Assignment:** Assign the asset to a specific employee or project.\n3. **Maintenance:** Log maintenance activities and costs.\n4. **Depreciation:** The system automatically calculates depreciation based on the configured method (e.g., Straight Line).\n5. **Disposal:** Retire the asset when it is no longer usable."],
                ]
            ],
            [
                'name' => '9. Settings & Administration',
                'slug' => 'settings-admin',
                'order' => 9,
                'articles' => [
                    ['title' => 'System Settings', 'slug' => 'system-settings', 'content' => "## System Settings\n\nAdministrators control core parameters of the ERP from the Settings menu.\n\n- **Document Numbering:** Configure the prefix and sequence format for all generated documents (e.g., INV-2026-0001).\n- **Date & Time Formats:** Global display settings.\n- **Maintenance Mode:** Temporarily disable access for standard users during upgrades.\n\n> [!WARNING]\n> Changing document numbering sequences mid-year can break audit trails. Always consult with Finance before making numbering changes."],
                    ['title' => 'Roles & Permissions', 'slug' => 'roles-permissions', 'content' => "## Roles & Permissions\n\nCreative Century Engineering uses a granular role-based access control system.\n\n### Key Roles:\n- **Super Admin / Administrator:** Full access to all modules and settings.\n- **CEO / Auditor:** Primarily view-only and approval access across all modules.\n- **Finance Manager / Accountant:** Full access to journals, ledgers, and billing.\n- **Project Manager / Engineer:** Access restricted to project execution and material requests.\n- **Procurement Manager / Officer:** Access to PRs, POs, and Suppliers.\n- **Warehouse Manager / Store Keeper:** Access to stock counts, GRNs, and material issues."],
                ]
            ],
            [
                'name' => '10. Troubleshooting',
                'slug' => 'troubleshooting',
                'order' => 10,
                'articles' => [
                    ['title' => 'Common Issues', 'slug' => 'common-issues', 'content' => "## Troubleshooting Guide\n\n**Issue: I cannot access a specific page.**\n*Cause:* You do not have the required permission assigned to your role.\n*Solution:* Contact your Administrator to update your role permissions.\n\n**Issue: Cannot approve a document.**\n*Cause:* The document may require approval from a higher authority or another department first.\n*Solution:* Check the Approval Workflow status on the document.\n\n**Issue: File upload fails.**\n*Cause:* The file exceeds the maximum allowed size (usually 10MB) or is an unsupported format.\n*Solution:* Compress the file or convert it to PDF/JPG/PNG before trying again."],
                ]
            ]
        ];

        foreach ($categories as $catData) {
            $category = DocumentationCategory::firstOrCreate(
                ['slug' => $catData['slug']],
                [
                    'name' => $catData['name'],
                    'order' => $catData['order'],
                    'is_active' => true,
                    'icon' => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                ]
            );

            foreach ($catData['articles'] as $index => $artData) {
                DocumentationArticle::updateOrCreate(
                    ['slug' => $artData['slug']],
                    [
                        'documentation_category_id' => $category->id,
                        'title' => $artData['title'],
                        'content' => $artData['content'],
                        'order' => $index + 1,
                        'status' => 'published'
                    ]
                );
            }
        }
    }
}
