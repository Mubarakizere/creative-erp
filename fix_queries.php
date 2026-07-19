<?php

$dir = __DIR__ . '/app/Services/Metrics';
$files = glob($dir . '/*Metrics.php');

foreach ($files as $file) {
    if (in_array(basename($file), ['MetricsService.php', 'ReportMetrics.php', 'ChartService.php'])) continue;

    $content = file_get_contents($file);

    // Replace ->query() that follows $this->applyFilters()
    // It looks like: $this->applyFilters(Company::query(), $filters)->query()
    $newContent = preg_replace('/(\$this->applyFilters\([A-Za-z]+::query\(\),\s*\$filters\))->query\(\)/', '$1', $content);

    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        echo "Fixed: " . basename($file) . "\n";
    }
}
echo "Done\n";
