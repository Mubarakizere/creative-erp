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

        // 2. Create Permissions
        foreach ($permissions as $permission) {
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

        $warehouseManager = Role::where('name', 'Warehouse Manager')->first();
        if ($warehouseManager) {
            $warehousePerms = Permission::whereIn('name', [
                'warehouse.view', 'warehouse.create', 'warehouse.update', 'warehouse.delete',
                'inventory.view', 'inventory.create', 'inventory.update', 'inventory.delete',
                'stock.view', 'stock.create', 'stock.update', 'stock.delete',
                'product.view', 'product.create', 'product.update', 'product.delete',
                'material_request.view', 'material_issue.view', 'material_issue.create',
                'goods_receipt.view', 'goods_receipt.create', 'goods_receipt.update'
            ])->get();
            $warehouseManager->syncPermissions($warehousePerms);
        }

        $projectManager = Role::where('name', 'Project Manager')->first();
        if ($projectManager) {
            $pmPerms = Permission::where(function($q) {
                $q->where('name', 'like', 'project.%')
                  ->orWhere('name', 'like', 'project_task.%')
                  ->orWhere('name', 'like', 'milestone.%')
                  ->orWhere('name', 'like', 'material_request.%')
                  ->orWhere('name', 'like', 'report.%')
                  ->orWhere('name', 'like', 'document.%');
            })->get();
            $projectManager->syncPermissions($pmPerms);
        }

        $accountant = Role::where('name', 'Accountant')->first();
        if ($accountant) {
            $financePerms = Permission::where(function($q) {
                $q->where('name', 'like', 'account.%')
                  ->orWhere('name', 'like', 'journal.%')
                  ->orWhere('name', 'like', 'expense.%')
                  ->orWhere('name', 'like', 'invoice.%')
                  ->orWhere('name', 'like', 'payment.%')
                  ->orWhere('name', 'like', 'tax.%')
                  ->orWhere('name', 'like', 'report.%');
            })->get();
            $accountant->syncPermissions($financePerms);
        }
    }
}
