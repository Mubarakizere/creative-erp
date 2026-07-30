<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;

$dbPermissions = Permission::pluck('name')->toArray();

$directories = ['app/Http/Controllers', 'app/Policies', 'routes', 'resources/views', 'app/Services'];
$codePermissions = [];

foreach ($directories as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isDir()) continue;
        if (!preg_match('/\.(php)$/i', $file->getFilename())) continue;
        $content = file_get_contents($file->getPathname());
        
        preg_match_all('/(?:hasPermissionTo|hasAnyPermission|can|@can|authorize)\(\s*[\'"]([a-zA-Z0-9_\.-]+)[\'"]/', $content, $matches1);
        preg_match_all('/middleware\([\'"]permission:([a-zA-Z0-9_\.\|-]+)[\'"]\)/', $content, $matches2);
        
        $found = array_merge($matches1[1], $matches2[1]);
        foreach ($found as $p) {
            $parts = explode('|', $p);
            foreach($parts as $part) {
                $codePermissions[] = trim($part);
            }
        }
    }
}

$codePermissions = array_unique($codePermissions);

$missingInDb = array_diff($codePermissions, $dbPermissions);
// Some permissions might be standard laravel abilities that are not stored in db, like viewAny etc., but we mapped them. Wait, if a policy uses hasPermissionTo('module.viewAny'), it's a bug because we mapped it to 'module.view'.

echo "Permissions in code but not in DB:\n";
foreach ($missingInDb as $m) {
    echo "- $m\n";
}

$orphanInDb = array_diff($dbPermissions, $codePermissions);
echo "\nPermissions in DB but not found explicitly in code:\n";
foreach ($orphanInDb as $o) {
    // Some might be used dynamically or via roles
    echo "- $o\n";
}

