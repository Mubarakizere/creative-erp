<?php
$modules = [
    'company', 'branch', 'department', 'user', 'role', 'permission', 'settings',
    'project', 'project_task', 'milestone', 'documentation',
    'document', 'document_category', 'comment', 'meeting', 'calendar', 'time', 'approval', 'workflow', 'notification', 'report', 'dashboard', 'analytics',
    'procurement', 'purchase_order', 'purchase_requisition', 'supplier', 'supplier_payment', 
    'warehouse', 'inventory', 'stock', 'product', 'material_request', 'material_issue', 'goods_receipt',
    'asset', 'maintenance', 'depreciation',
    'account', 'bank_account', 'journal', 'ledger', 'expense', 'budget', 'invoice', 'payment', 'credit_note', 'receipt', 'refund', 'tax',
    'customer', 'lead', 'opportunity', 'quotation', 'crm'
];

$permissions = [];
foreach ($modules as $mod) {
    if (in_array($mod, ['settings', 'calendar', 'dashboard', 'analytics', 'ledger'])) {
        $permissions[] = $mod . '.view';
        if ($mod == 'settings') $permissions[] = $mod . '.manage';
    } else {
        $permissions[] = $mod . '.view';
        $permissions[] = $mod . '.create';
        $permissions[] = $mod . '.update';
        $permissions[] = $mod . '.delete';
        $permissions[] = $mod . '.restore';
        if (in_array($mod, ['user', 'company', 'branch', 'department', 'customer', 'supplier', 'product'])) {
            $permissions[] = $mod . '.activate';
            $permissions[] = $mod . '.deactivate';
        }
        if (in_array($mod, ['material_request', 'purchase_order', 'purchase_requisition', 'quotation', 'invoice', 'payment', 'time'])) {
            $permissions[] = $mod . '.approve';
            $permissions[] = $mod . '.reject';
        }
    }
}
$permissions = array_unique($permissions);
$extra = [
    'asset.manage', 'asset.dispose', 'asset.impair', 'asset.maintenance', 'asset.assign', 'asset.depreciate', 'asset.transfer',
    'comment.reply', 'fiscal.manage', 'journal.reverse', 'quotation.manage', 'material_request.submit', 'material_request.convert_to_procurement',
    'asset_category.create', 'asset_category.update', 'asset_category.view', 'activity.view', 'user.resetPassword', 'settings.manage'
];
$permissions = array_merge($permissions, $extra);
$permissions = array_unique($permissions);
sort($permissions);

$phpCode = "<?php\n\nnamespace Database\Seeders;\n\nuse Illuminate\Database\Seeder;\nuse Spatie\Permission\Models\Role;\nuse Spatie\Permission\Models\Permission;\nuse Spatie\Permission\PermissionRegistrar;\n\nclass RolesAndPermissionsSeeder extends Seeder\n{\n    public function run(): void\n    {\n        // Reset cached roles and permissions\n        app()[PermissionRegistrar::class]->forgetCachedPermissions();\n\n        // 1. Define Permissions\n        \$permissions = [\n";

foreach ($permissions as $p) {
    $phpCode .= "            '$p',\n";
}

$phpCode .= "        ];\n\n        // 2. Create Permissions\n        foreach (\$permissions as \$permission) {\n            Permission::firstOrCreate(['name' => \$permission]);\n        }\n\n        // 3. Define Roles\n        \$roles = [\n            'Super Admin',\n            'Administrator',\n            'CEO',\n            'Finance Manager',\n            'Accountant',\n            'HR Manager',\n            'HR Officer',\n            'Project Manager',\n            'Engineer',\n            'Site Engineer',\n            'Procurement Manager',\n            'Procurement Officer',\n            'Warehouse Manager',\n            'Store Keeper',\n            'Inventory Manager',\n            'Asset Manager',\n            'Sales Manager',\n            'Auditor',\n            'Employee',\n            'Client'\n        ];\n\n        foreach (\$roles as \$roleName) {\n            Role::firstOrCreate(['name' => \$roleName]);\n        }\n\n        // 4. Assign Permissions\n        \$superAdmin = Role::where('name', 'Super Admin')->first();\n        if (\$superAdmin) {\n            \$superAdmin->syncPermissions(Permission::all());\n        }\n        \n        \$admin = Role::where('name', 'Administrator')->first();\n        if (\$admin) {\n            \$admin->syncPermissions(Permission::all());\n        }\n\n        \$warehouseManager = Role::where('name', 'Warehouse Manager')->first();\n        if (\$warehouseManager) {\n            \$warehousePerms = Permission::whereIn('name', [\n                'warehouse.view', 'warehouse.create', 'warehouse.update', 'warehouse.delete',\n                'inventory.view', 'inventory.create', 'inventory.update', 'inventory.delete',\n                'stock.view', 'stock.create', 'stock.update', 'stock.delete',\n                'product.view', 'product.create', 'product.update', 'product.delete',\n                'material_request.view', 'material_issue.view', 'material_issue.create',\n                'goods_receipt.view', 'goods_receipt.create', 'goods_receipt.update'\n            ])->get();\n            \$warehouseManager->syncPermissions(\$warehousePerms);\n        }\n\n        \$projectManager = Role::where('name', 'Project Manager')->first();\n        if (\$projectManager) {\n            \$pmPerms = Permission::where(function(\$q) {\n                \$q->where('name', 'like', 'project.%')\n                  ->orWhere('name', 'like', 'project_task.%')\n                  ->orWhere('name', 'like', 'milestone.%')\n                  ->orWhere('name', 'like', 'material_request.%')\n                  ->orWhere('name', 'like', 'report.%')\n                  ->orWhere('name', 'like', 'document.%');\n            })->get();\n            \$projectManager->syncPermissions(\$pmPerms);\n        }\n\n        \$accountant = Role::where('name', 'Accountant')->first();\n        if (\$accountant) {\n            \$financePerms = Permission::where(function(\$q) {\n                \$q->where('name', 'like', 'account.%')\n                  ->orWhere('name', 'like', 'journal.%')\n                  ->orWhere('name', 'like', 'expense.%')\n                  ->orWhere('name', 'like', 'invoice.%')\n                  ->orWhere('name', 'like', 'payment.%')\n                  ->orWhere('name', 'like', 'tax.%')\n                  ->orWhere('name', 'like', 'report.%');\n            })->get();\n            \$accountant->syncPermissions(\$financePerms);\n        }\n    }\n}\n";

file_put_contents('database/seeders/RolesAndPermissionsSeeder.php', $phpCode);
echo "Seeder replaced.";
