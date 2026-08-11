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
                    ['title' => 'Managing Projects', 'slug' => 'managing-projects', 'content' => "## Managing Projects\n\nProjects are the core of operational delivery. \n\n### Workflow:\n1. **Create Project:** Define the scope, client, and budget.\n2. **Assign Team:** Add users to the project team.\n3. **Milestones & Tasks:** Break the project down into measurable milestones and actionable tasks.\n4. **Material Requests:** Field engineers can request materials from inventory or procurement directly through the project.\n\n> [!NOTE]\n> Note: Advanced project financial features like Variation Orders, Claims, and Equipment Breakdown are not currently implemented in this version."],
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
