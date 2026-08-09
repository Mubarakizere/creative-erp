<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$models = collect(\Illuminate\Support\Facades\File::allFiles(app_path('Models')))
    ->map(function($file) { return 'App\\Models\\' . str_replace('/', '\\', $file->getRelativePathname()); })
    ->map(function($class) { return str_replace('.php', '', $class); })
    ->filter(function($class) { return class_exists($class) && is_subclass_of($class, 'Illuminate\Database\Eloquent\Model'); });

$results = [];
foreach ($models as $model) {
    try {
        $instance = new $model;
        $table = $instance->getTable();
        $hasCompanyId = \Illuminate\Support\Facades\Schema::hasColumn($table, 'company_id');
        $traits = class_uses_recursive($model);
        $hasCompanyScoped = in_array('App\Models\Traits\CompanyScoped', $traits);
        $hasCompanyRelation = method_exists($instance, 'company');
        
        $safe = 'No';
        if ($hasCompanyId && $hasCompanyScoped) $safe = 'Yes';
        elseif (!$hasCompanyId) $safe = 'N/A';
        
        $results[] = [
            'Model' => class_basename($model), 
            'Has company_id' => $hasCompanyId ? 'Yes' : 'No', 
            'CompanyScoped' => $hasCompanyScoped ? 'Yes' : 'No', 
            'Company Relationship' => $hasCompanyRelation ? 'Yes' : 'No',
            'Safe?' => $safe
        ];
    } catch (\Exception $e) {}
}
file_put_contents('audit_models.json', json_encode($results, JSON_PRETTY_PRINT));
echo "Done\n";
