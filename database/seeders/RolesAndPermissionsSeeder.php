<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define Permissions
        $permissions = [
            'account.create',
            'account.delete',
            'account.restore',
            'account.update',
            'account.view',
            'activity.view',
            'analytics.view',
            'approval.create',
            'approval.delete',
            'approval.restore',
            'approval.update',
            'approval.view',
            'asset.assign',
            'asset.create',
            'asset.delete',
            'asset.depreciate',
            'asset.dispose',
            'asset.impair',
            'asset.maintenance',
            'asset.manage',
            'asset.restore',
            'asset.transfer',
            'asset.update',
            'asset.view',
            'asset_category.create',
            'asset_category.update',
            'asset_category.view',
            'bank_account.create',
            'bank_account.delete',
            'bank_account.restore',
            'bank_account.update',
            'bank_account.view',
            'branch.activate',
            'branch.create',
            'branch.deactivate',
            'branch.delete',
            'branch.restore',
            'branch.update',
            'branch.view',
            'budget.create',
            'budget.delete',
            'budget.restore',
            'budget.update',
            'budget.view',
            'calendar.view',
            'comment.create',
            'comment.delete',
            'comment.reply',
            'comment.restore',
            'comment.update',
            'comment.view',
            'company.activate',
            'company.create',
            'company.deactivate',
            'company.delete',
            'company.restore',
            'company.update',
            'company.view',
            'credit_note.create',
            'credit_note.delete',
            'credit_note.restore',
            'credit_note.update',
            'credit_note.view',
            'crm.create',
            'crm.delete',
            'crm.pipeline',
            'crm.restore',
            'crm.update',
            'crm.view',
            'customer.activate',
            'customer.create',
            'customer.deactivate',
            'customer.delete',
            'customer.restore',
            'customer.update',
            'customer.view',
            'dashboard.view',
            'department.activate',
            'department.create',
            'department.deactivate',
            'department.delete',
            'department.restore',
            'department.update',
            'department.view',
            'depreciation.create',
            'depreciation.delete',
            'depreciation.restore',
            'depreciation.update',
            'depreciation.view',
            'document.create',
            'document.delete',
            'document.restore',
            'document.update',
            'document.view',
            'document_category.create',
            'document_category.delete',
            'document_category.restore',
            'document_category.update',
            'document_category.view',
            'documentation.create',
            'documentation.delete',
            'documentation.restore',
            'documentation.update',
            'documentation.view',
            'expense.create',
            'expense.delete',
            'expense.restore',
            'expense.update',
            'expense.view',
            'fiscal.manage',
            'goods_receipt.create',
            'goods_receipt.delete',
            'goods_receipt.restore',
            'goods_receipt.update',
            'goods_receipt.view',
            'inventory.create',
            'inventory.delete',
            'inventory.restore',
            'inventory.update',
            'inventory.view',
            'invoice.approve',
            'invoice.create',
            'invoice.delete',
            'invoice.reject',
            'invoice.restore',
            'invoice.update',
            'invoice.view',
            'journal.create',
            'journal.delete',
            'journal.restore',
            'journal.reverse',
            'journal.update',
            'journal.view',
            'lead.create',
            'lead.delete',
            'lead.restore',
            'lead.update',
            'lead.view',
            'ledger.view',
            'maintenance.create',
            'maintenance.delete',
            'maintenance.restore',
            'maintenance.update',
            'maintenance.view',
            'material_issue.create',
            'material_issue.delete',
            'material_issue.restore',
            'material_issue.update',
            'material_issue.view',
            'material_request.approve',
            'material_request.convert_to_procurement',
            'material_request.create',
            'material_request.delete',
            'material_request.reject',
            'material_request.restore',
            'material_request.submit',
            'material_request.update',
            'material_request.view',
            'meeting.create',
            'meeting.delete',
            'meeting.restore',
            'meeting.update',
            'meeting.view',
            'milestone.create',
            'milestone.delete',
            'milestone.restore',
            'milestone.update',
            'milestone.view',
            'notification.announcement',
            'notification.create',
            'notification.delete',
            'notification.restore',
            'notification.update',
            'notification.view',
            'opportunity.create',
            'opportunity.delete',
            'opportunity.restore',
            'opportunity.update',
            'opportunity.view',
            'payment.approve',
            'payment.create',
            'payment.delete',
            'payment.reject',
            'payment.restore',
            'payment.update',
            'payment.view',
            'permission.create',
            'permission.delete',
            'permission.restore',
            'permission.update',
            'permission.view',
            'procurement.create',
            'procurement.delete',
            'procurement.restore',
            'procurement.update',
            'procurement.view',
            'product.activate',
            'product.create',
            'product.deactivate',
            'product.delete',
            'product.restore',
            'product.update',
            'product.view',
            'project.create',
            'project.delete',
            'project.restore',
            'project.update',
            'project.view',
            'project_task.create',
            'project_task.delete',
            'project_task.restore',
            'project_task.update',
            'project_task.view',
            'purchase_order.approve',
            'purchase_order.create',
            'purchase_order.delete',
            'purchase_order.reject',
            'purchase_order.restore',
            'purchase_order.update',
            'purchase_order.view',
            'purchase_requisition.approve',
            'purchase_requisition.create',
            'purchase_requisition.delete',
            'purchase_requisition.reject',
            'purchase_requisition.restore',
            'purchase_requisition.update',
            'purchase_requisition.view',
            'quotation.approve',
            'quotation.create',
            'quotation.delete',
            'quotation.manage',
            'quotation.reject',
            'quotation.restore',
            'quotation.update',
            'quotation.view',
            'receipt.create',
            'receipt.delete',
            'receipt.restore',
            'receipt.update',
            'receipt.view',
            'refund.create',
            'refund.delete',
            'refund.restore',
            'refund.update',
            'refund.view',
            'report.create',
            'report.delete',
            'report.restore',
            'report.update',
            'report.view',
            'role.create',
            'role.delete',
            'role.restore',
            'role.update',
            'role.view',
            'settings.manage',
            'settings.view',
            'stock.create',
            'stock.delete',
            'stock.restore',
            'stock.update',
            'stock.view',
            'supplier.activate',
            'supplier.create',
            'supplier.deactivate',
            'supplier.delete',
            'supplier.restore',
            'supplier.update',
            'supplier.view',
            'supplier_payment.create',
            'supplier_payment.delete',
            'supplier_payment.restore',
            'supplier_payment.update',
            'supplier_payment.view',
            'tax.create',
            'tax.delete',
            'tax.restore',
            'tax.update',
            'tax.view',
            'time.approve',
            'time.create',
            'time.delete',
            'time.reject',
            'time.restore',
            'time.update',
            'time.view',
            'user.activate',
            'user.create',
            'user.deactivate',
            'user.delete',
            'user.resetPassword',
            'user.restore',
            'user.update',
            'user.view',
            'warehouse.create',
            'warehouse.delete',
            'warehouse.restore',
            'warehouse.update',
            'warehouse.view',
            'workflow.create',
            'workflow.delete',
            'workflow.restore',
            'workflow.update',
            'workflow.view',
        ];

        // Auto-discover missing permissions from policies
        $discoveredPermissions = [];
        $files = glob(app_path('Policies/*.php'));
        foreach($files as $file) {
            $content = file_get_contents($file);
            preg_match_all('/hasPermissionTo\(\'([a-zA-Z0-9_\-\.]+)\'\)/', $content, $matches);
            if (!empty($matches[1])) {
                $discoveredPermissions = array_merge($discoveredPermissions, $matches[1]);
            }
        }
        
        foreach (array_unique($discoveredPermissions) as $dp) {
            if (!in_array($dp, $permissions)) {
                $permissions[] = $dp;
            }
        }

        // 2. Create Permissions
        foreach (array_unique($permissions) as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Define Roles
        $roles = [
            'Super Admin',
            'Administrator',
            'CEO',
            'Finance Manager',
            'Accountant',
            'HR Manager',
            'HR Officer',
            'Project Manager',
            'Engineer',
            'Site Engineer',
            'Procurement Manager',
            'Procurement Officer',
            'Warehouse Manager',
            'Store Keeper',
            'Inventory Manager',
            'Asset Manager',
            'Sales Manager',
            'Auditor',
            'Employee',
            'Client'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 4. Assign Permissions
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }
        
        $admin = Role::where('name', 'Administrator')->first();
        if ($admin) {
            $admin->syncPermissions(Permission::all());
        }

        $ceo = Role::where('name', 'CEO')->first();
        if ($ceo) {
            $ceoPerms = Permission::where(function($q) {
                $q->where('name', 'like', '%.view')
                  ->orWhere('name', 'like', '%.approve')
                  ->orWhere('name', 'like', 'dashboard.%')
                  ->orWhere('name', 'notification.announcement')
                  ->orWhere('name', 'like', 'report.%');
            })->get();
            $ceo->syncPermissions($ceoPerms);
        }

        $financeManager = Role::where('name', 'Finance Manager')->first();
        if ($financeManager) {
            $financePerms = Permission::where(function($q) {
                $q->where('name', 'like', 'account.%')
                  ->orWhere('name', 'like', 'journal.%')
                  ->orWhere('name', 'like', 'expense.%')
                  ->orWhere('name', 'like', 'invoice.%')
                  ->orWhere('name', 'like', 'payment.%')
                  ->orWhere('name', 'like', 'tax.%')
                  ->orWhere('name', 'like', 'budget.%')
                  ->orWhere('name', 'like', 'depreciation.%')
                  ->orWhere('name', 'like', 'report.%')
                  ->orWhere('name', 'like', 'dashboard.%');
            })->get();
            $financeManager->syncPermissions($financePerms);
        }

        $accountant = Role::where('name', 'Accountant')->first();
        if ($accountant) {
            $accountantPerms = Permission::where(function($q) {
                $q->where('name', 'like', 'account.%')
                  ->orWhere('name', 'like', 'journal.%')
                  ->orWhere('name', 'like', 'expense.%')
                  ->orWhere('name', 'like', 'invoice.%')
                  ->orWhere('name', 'like', 'payment.%')
                  ->orWhere('name', 'like', 'tax.%')
                  ->orWhere('name', 'like', 'report.%')
                  ->orWhere('name', 'like', 'dashboard.%');
            })->get();
            $accountant->syncPermissions($accountantPerms);
        }

        $hrManager = Role::where('name', 'HR Manager')->first();
        if ($hrManager) {
            $hrPerms = Permission::where(function($q) {
                $q->where('name', 'like', 'department.%')
                  ->orWhere('name', 'like', 'user.%')
                  ->orWhere('name', 'like', 'time.%')
                  ->orWhere('name', 'notification.announcement')
                  ->orWhere('name', 'like', 'dashboard.%');
            })->get();
            $hrManager->syncPermissions($hrPerms);
        }

        $hrOfficer = Role::where('name', 'HR Officer')->first();
        if ($hrOfficer) {
            $hrOfficerPerms = Permission::whereIn('name', [
                'department.view', 'user.view', 'user.create', 'time.view', 'time.approve', 'dashboard.view'
            ])->get();
            $hrOfficer->syncPermissions($hrOfficerPerms);
        }

        $projectManager = Role::where('name', 'Project Manager')->first();
        if ($projectManager) {
            $pmPerms = Permission::where(function($q) {
                $q->where('name', 'like', 'project.%')
                  ->orWhere('name', 'like', 'project_task.%')
                  ->orWhere('name', 'like', 'milestone.%')
                  ->orWhere('name', 'like', 'material_request.%')
                  ->orWhere('name', 'like', 'report.%')
                  ->orWhere('name', 'like', 'document.%')
                  ->orWhere('name', 'like', 'dashboard.%');
            })->get();
            $projectManager->syncPermissions($pmPerms);
        }

        $engineer = Role::where('name', 'Engineer')->first();
        if ($engineer) {
            $engineerPerms = Permission::whereIn('name', [
                'project.view', 'project_task.view', 'project_task.update',
                'material_request.create', 'material_request.view', 'document.view', 'dashboard.view'
            ])->get();
            $engineer->syncPermissions($engineerPerms);
        }

        $siteEngineer = Role::where('name', 'Site Engineer')->first();
        if ($siteEngineer) {
            $siteEngineerPerms = Permission::whereIn('name', [
                'project.view', 'project_task.view', 'project_task.update',
                'material_request.create', 'material_request.view', 'dashboard.view'
            ])->get();
            $siteEngineer->syncPermissions($siteEngineerPerms);
        }

        $procurementManager = Role::where('name', 'Procurement Manager')->first();
        if ($procurementManager) {
            $procurementPerms = Permission::where(function($q) {
                $q->where('name', 'like', 'procurement.%')
                  ->orWhere('name', 'like', 'purchase_order.%')
                  ->orWhere('name', 'like', 'purchase_requisition.%')
                  ->orWhere('name', 'like', 'supplier.%')
                  ->orWhere('name', 'like', 'supplier_payment.%')
                  ->orWhere('name', 'like', 'report.%')
                  ->orWhere('name', 'like', 'dashboard.%');
            })->get();
            $procurementManager->syncPermissions($procurementPerms);
        }

        $procurementOfficer = Role::where('name', 'Procurement Officer')->first();
        if ($procurementOfficer) {
            $poPerms = Permission::where(function($q) {
                $q->where('name', 'like', 'purchase_order.create')
                  ->orWhere('name', 'like', 'purchase_order.view')
                  ->orWhere('name', 'like', 'purchase_requisition.create')
                  ->orWhere('name', 'like', 'purchase_requisition.view')
                  ->orWhere('name', 'like', 'supplier.view')
                  ->orWhere('name', 'like', 'dashboard.%');
            })->get();
            $procurementOfficer->syncPermissions($poPerms);
        }

        $warehouseManager = Role::where('name', 'Warehouse Manager')->first();
        if ($warehouseManager) {
            $warehousePerms = Permission::where(function($q) {
                $q->where('name', 'like', 'warehouse.%')
                  ->orWhere('name', 'like', 'inventory.%')
                  ->orWhere('name', 'like', 'stock.%')
                  ->orWhere('name', 'like', 'product.%')
                  ->orWhere('name', 'like', 'goods_receipt.%')
                  ->orWhere('name', 'like', 'material_issue.%')
                  ->orWhere('name', 'like', 'material_request.view')
                  ->orWhere('name', 'like', 'dashboard.%');
            })->get();
            $warehouseManager->syncPermissions($warehousePerms);
        }

        $inventoryManager = Role::where('name', 'Inventory Manager')->first();
        if ($inventoryManager) {
            $inventoryPerms = Permission::where(function($q) {
                $q->where('name', 'like', 'inventory.%')
                  ->orWhere('name', 'like', 'stock.%')
                  ->orWhere('name', 'like', 'product.%')
                  ->orWhere('name', 'like', 'dashboard.%');
            })->get();
            $inventoryManager->syncPermissions($inventoryPerms);
        }

        $storeKeeper = Role::where('name', 'Store Keeper')->first();
        if ($storeKeeper) {
            $storePerms = Permission::whereIn('name', [
                'warehouse.view', 'inventory.view', 'inventory.update', 'stock.view', 'stock.update',
                'product.view', 'goods_receipt.create', 'goods_receipt.view',
                'material_issue.create', 'material_issue.view', 'dashboard.view'
            ])->get();
            $storeKeeper->syncPermissions($storePerms);
        }

        $assetManager = Role::where('name', 'Asset Manager')->first();
        if ($assetManager) {
            $assetPerms = Permission::where(function($q) {
                $q->where('name', 'like', 'asset.%')
                  ->orWhere('name', 'like', 'asset_category.%')
                  ->orWhere('name', 'like', 'depreciation.%')
                  ->orWhere('name', 'like', 'maintenance.%')
                  ->orWhere('name', 'like', 'dashboard.%');
            })->get();
            $assetManager->syncPermissions($assetPerms);
        }

        $salesManager = Role::where('name', 'Sales Manager')->first();
        if ($salesManager) {
            $salesPerms = Permission::where(function($q) {
                $q->where('name', 'like', 'crm.%')
                  ->orWhere('name', 'like', 'lead.%')
                  ->orWhere('name', 'like', 'opportunity.%')
                  ->orWhere('name', 'like', 'quotation.%')
                  ->orWhere('name', 'like', 'customer.%')
                  ->orWhere('name', 'like', 'dashboard.%');
            })->get();
            $salesManager->syncPermissions($salesPerms);
        }

        $auditor = Role::where('name', 'Auditor')->first();
        if ($auditor) {
            $auditorPerms = Permission::where(function($q) {
                $q->where('name', 'like', '%.view')
                  ->orWhere('name', 'like', 'report.%')
                  ->orWhere('name', 'like', 'dashboard.%');
            })->get();
            $auditor->syncPermissions($auditorPerms);
        }

        $employee = Role::where('name', 'Employee')->first();
        if ($employee) {
            $employeePerms = Permission::whereIn('name', [
                'time.create', 'time.view', 'dashboard.view'
            ])->get();
            $employee->syncPermissions($employeePerms);
        }

        $client = Role::where('name', 'Client')->first();
        if ($client) {
            $clientPerms = Permission::whereIn('name', [
                'project.view', 'invoice.view', 'quotation.view', 'dashboard.view'
            ])->get();
            $client->syncPermissions($clientPerms);
        }
    }
}
