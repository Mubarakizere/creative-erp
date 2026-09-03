<?php

return [
    'Dashboard' => [
        ['route' => 'admin.dashboard', 'label' => 'Overview', 'icon' => 'home', 'permission' => 'dashboard.view', 'active' => 'admin.dashboard'],
        ['route' => 'admin.reports.index', 'label' => 'Analytics', 'icon' => 'chart', 'permission' => 'report.view', 'active' => 'admin.reports.*'],
    ],

    'Sales' => [
        ['route' => 'admin.crm.leads.index', 'label' => 'Leads', 'icon' => 'target', 'permission' => 'lead.view', 'model' => \App\Models\Lead::class, 'active' => 'admin.crm.leads.*'],
        ['route' => 'admin.crm.opportunities.index', 'label' => 'Pipeline', 'icon' => 'funnel', 'permission' => 'opportunity.view', 'model' => \App\Models\Opportunity::class, 'active' => 'admin.crm.opportunities.*'],
        ['route' => 'admin.crm.quotations.index', 'label' => 'Quotations', 'icon' => 'document', 'permission' => 'quotation.view', 'model' => \App\Models\Quotation::class, 'active' => 'admin.crm.quotations.*'],
        ['route' => 'admin.clients.index', 'label' => 'Clients', 'icon' => 'users', 'permission' => 'customer.view', 'model' => \App\Models\Client::class, 'active' => 'admin.clients.*'],
    ],

    'Projects' => [
        ['route' => 'admin.projects.index', 'label' => 'All Projects', 'icon' => 'folder', 'permission' => 'project.view', 'model' => \App\Models\Project::class, 'active' => ['admin.projects.index', 'admin.projects.create', 'admin.projects.edit', 'admin.projects.show', 'admin.projects.timeline']],
        ['route' => 'admin.projects.tasks.index', 'label' => 'Tasks', 'icon' => 'check-list', 'permission' => 'project_task.view', 'model' => \App\Models\Task::class, 'active' => 'admin.projects.tasks.*'],
        ['route' => 'admin.milestones.index', 'label' => 'Milestones', 'icon' => 'flag', 'permission' => 'milestone.view', 'model' => \App\Models\Milestone::class, 'active' => 'admin.milestones.*'],
        ['route' => 'admin.projects.team.index', 'label' => 'Teams', 'icon' => 'team', 'permission' => 'project.view', 'model' => \App\Models\ProjectMember::class, 'active' => 'admin.projects.team.*'],
    ],

    'Materials' => [
        ['route' => 'admin.material-requests.index', 'label' => 'Requests', 'icon' => 'inbox', 'permission' => 'material_request.view', 'active' => 'admin.material-requests.*'],
        ['route' => 'admin.procurement.requisitions.index', 'label' => 'Requisitions', 'icon' => 'clipboard', 'permission' => 'purchase_requisition.view', 'model' => \App\Models\PurchaseRequisition::class, 'active' => 'admin.procurement.requisitions.*'],
        ['route' => 'admin.procurement.pos.index', 'label' => 'Orders', 'icon' => 'cart', 'permission' => 'purchase_order.view', 'model' => \App\Models\PurchaseOrder::class, 'active' => 'admin.procurement.pos.*'],
        ['route' => 'admin.procurement.receipts.index', 'label' => 'Receipts', 'icon' => 'truck', 'permission' => 'goods_receipt.view', 'model' => \App\Models\GoodsReceipt::class, 'active' => 'admin.procurement.receipts.*'],
        ['route' => 'admin.procurement.suppliers.index', 'label' => 'Suppliers', 'icon' => 'building', 'permission' => 'supplier.view', 'model' => \App\Models\Supplier::class, 'active' => 'admin.procurement.suppliers.*'],
    ],

    'Inventory' => [
        ['route' => 'admin.inventory.products.index', 'label' => 'Products', 'icon' => 'box', 'permission' => 'product.view', 'model' => \App\Models\Product::class, 'active' => 'admin.inventory.products.*'],
        ['route' => 'admin.inventory.warehouses.index', 'label' => 'Warehouses', 'icon' => 'warehouse', 'permission' => 'warehouse.view', 'model' => \App\Models\Warehouse::class, 'active' => 'admin.inventory.warehouses.*'],
        ['route' => 'admin.inventory.adjustments.index', 'label' => 'Adjustments', 'icon' => 'adjust', 'permission' => 'stock.view', 'model' => \App\Models\InventoryAdjustment::class, 'active' => 'admin.inventory.adjustments.*'],
        ['route' => 'admin.warehouse.movements.index', 'label' => 'Movements', 'icon' => 'arrows', 'permission' => 'warehouse.view', 'model' => \App\Models\WarehouseMovement::class, 'active' => 'admin.warehouse.movements.*'],
    ],

    'Equipment' => [
        ['route' => 'admin.assets.index', 'label' => 'Register', 'icon' => 'wrench', 'permission' => 'asset.view', 'model' => \App\Models\Asset::class, 'active' => ['admin.assets.index', 'admin.assets.create', 'admin.assets.edit', 'admin.assets.show']],
        ['route' => 'admin.asset-maintenances.index', 'label' => 'Maintenance', 'icon' => 'tool', 'permission' => 'asset.view', 'active' => 'admin.asset-maintenances.*'],
        ['route' => 'admin.asset-transfers.index', 'label' => 'Transfers', 'icon' => 'swap', 'permission' => 'asset.view', 'active' => 'admin.asset-transfers.*'],
        ['route' => 'admin.asset-categories.index', 'label' => 'Categories', 'icon' => 'tag', 'permission' => 'asset.view', 'active' => 'admin.asset-categories.*'],
    ],

    'Finance' => [
        ['route' => 'admin.finance.budgets.index', 'label' => 'Budgets', 'icon' => 'wallet', 'permission' => 'budget.view', 'model' => \App\Models\Budget::class, 'active' => 'admin.finance.budgets.*'],
        ['route' => 'admin.finance.invoices.index', 'label' => 'Invoices', 'icon' => 'receipt', 'permission' => 'invoice.view', 'model' => \App\Models\Invoice::class, 'active' => 'admin.finance.invoices.*'],
        ['route' => 'admin.finance.payments.index', 'label' => 'Payments', 'icon' => 'credit-card', 'permission' => 'payment.view', 'model' => \App\Models\Payment::class, 'active' => 'admin.finance.payments.*'],
        ['route' => 'admin.procurement.payments.index', 'label' => 'Supplier Pay', 'icon' => 'banknote', 'permission' => 'supplier_payment.view', 'model' => \App\Models\SupplierPayment::class, 'active' => 'admin.procurement.payments.*'],
        ['route' => 'admin.finance.credit-notes.index', 'label' => 'Credit Notes', 'icon' => 'minus-circle', 'permission' => 'credit_note.view', 'model' => \App\Models\CreditNote::class, 'active' => 'admin.finance.credit-notes.*'],
        ['route' => 'admin.finance.accounting.chart-of-accounts.index', 'label' => 'Accounts', 'icon' => 'list', 'permission' => 'account.view', 'model' => \App\Models\ChartOfAccount::class, 'active' => 'admin.finance.accounting.chart-of-accounts.*'],
        ['route' => 'admin.finance.accounting.journals.index', 'label' => 'Journals', 'icon' => 'book', 'permission' => 'journal.view', 'model' => \App\Models\Journal::class, 'active' => 'admin.finance.accounting.journals.*'],
    ],

    'Documents' => [
        ['route' => 'admin.documents.index', 'label' => 'Files', 'icon' => 'file', 'permission' => 'document.view', 'model' => \App\Models\Document::class, 'active' => 'admin.documents.*'],
        ['route' => 'admin.document-categories.index', 'label' => 'Categories', 'icon' => 'folder-open', 'permission' => 'document_category.view', 'model' => \App\Models\DocumentCategory::class, 'active' => 'admin.document-categories.*'],
    ],

    'Workspace' => [
        ['route' => 'admin.time-tracking.reports', 'label' => 'Reports', 'icon' => 'bar-chart', 'permission' => 'time.view', 'active' => 'admin.time-tracking.reports'],
        ['route' => 'admin.meetings.index', 'label' => 'Meetings', 'icon' => 'video', 'permission' => 'meeting.view', 'model' => \App\Models\Meeting::class, 'active' => 'admin.meetings.*'],
        ['route' => 'admin.approvals.index', 'label' => 'Approvals', 'icon' => 'check-circle', 'permission' => 'approval.view', 'model' => \App\Models\Approval::class, 'active' => 'admin.approvals.*'],
        ['route' => 'admin.time-tracking.index', 'label' => 'Timesheets', 'icon' => 'clock', 'permission' => 'time.view', 'model' => \App\Models\TimeEntry::class, 'active' => 'admin.time-tracking.index'],
        ['route' => 'admin.announcements.index', 'label' => 'Notices', 'icon' => 'megaphone', 'permission' => 'notification.view', 'model' => \App\Models\Announcement::class, 'active' => 'admin.announcements.*'],
        ['route' => 'admin.calendar.index', 'label' => 'Calendar', 'icon' => 'calendar', 'permission' => 'calendar.view', 'active' => 'admin.calendar.*'],
        ['route' => 'admin.documentation.index', 'label' => 'Help Center', 'icon' => 'help', 'permission' => 'documentation.view', 'active' => 'admin.documentation.*'],
    ],

    'Administration' => [
        ['route' => 'admin.companies.index', 'label' => 'Companies', 'icon' => 'office', 'permission' => 'company.view', 'model' => \App\Models\Company::class, 'active' => 'admin.companies.*'],
        ['route' => 'admin.branches.index', 'label' => 'Branches', 'icon' => 'map-pin', 'permission' => 'branch.view', 'model' => \App\Models\Branch::class, 'active' => 'admin.branches.*'],
        ['route' => 'admin.departments.index', 'label' => 'Departments', 'icon' => 'sitemap', 'permission' => 'department.view', 'model' => \App\Models\Department::class, 'active' => 'admin.departments.*'],
        ['route' => 'admin.users.index', 'label' => 'Users', 'icon' => 'user', 'permission' => 'user.view', 'model' => \App\Models\User::class, 'active' => 'admin.users.*'],
        ['route' => 'admin.roles.index', 'label' => 'Roles', 'icon' => 'shield', 'permission' => 'role.view', 'active' => 'admin.roles.*'],
        ['route' => 'admin.workflows.index', 'label' => 'Workflows', 'icon' => 'git-branch', 'permission' => 'workflow.view', 'model' => \App\Models\ApprovalWorkflow::class, 'active' => 'admin.workflows.*'],
        ['route' => 'admin.website-settings.index', 'label' => 'Website', 'icon' => 'globe', 'permission' => 'settings.manage', 'active' => 'admin.website-settings.*'],
    ],
];
