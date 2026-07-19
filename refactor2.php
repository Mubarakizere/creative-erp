<?php

$dir = __DIR__ . '/app/Services/Metrics';
$files = glob($dir . '/*Metrics.php');
foreach ($files as $file) {
    if (in_array(basename($file), ['MetricsService.php', 'ReportMetrics.php', 'ChartService.php'])) continue;

    $content = file_get_contents($file);

    // Add trait use statement
    if (strpos($content, 'use App\Services\Metrics\Traits\FiltersMetrics;') === false) {
        $content = preg_replace('/namespace App\\\\Services\\\\Metrics;/', "namespace App\\Services\\Metrics;\n\nuse App\\Services\\Metrics\\Traits\\FiltersMetrics;", $content);
        $content = preg_replace('/class (\w+) implements MetricProvider\s*\{/', "class $1 implements MetricProvider\n{\n    use FiltersMetrics;\n", $content);
    }

    // Replace Model:: with $this->applyFilters(Model::query(), $filters)->
    $models = ['Task', 'Project', 'Company', 'Department', 'Branch', 'TimeEntry', 'Document', 'Comment', 'Announcement', 'Notification', 'Client', 'Meeting', 'Milestone', 'User'];
    
    foreach ($models as $model) {
        $content = preg_replace("/(?<!\\\\){$model}::([a-zA-Z])/", "\$this->applyFilters({$model}::query(), \$filters)->$1", $content);
    }

    file_put_contents($file, $content);
    echo "Filtered: " . basename($file) . "\n";
}
echo "Done\n";
