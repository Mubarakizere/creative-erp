<?php

$dir = __DIR__ . '/app/Services/Metrics';
$files = glob($dir . '/*Metrics.php');
foreach ($files as $file) {
    if (basename($file) === 'MetricsService.php' || basename($file) === 'ReportMetrics.php') continue;
    
    $content = file_get_contents($file);
    
    // Replace public function cards(): array
    $content = preg_replace('/public function cards\(\)\s*:\s*array/', 'public function cards(array $filters = []): array', $content);
    
    // Replace public function widgets(): array
    $content = preg_replace('/public function widgets\(\)\s*:\s*array/', 'public function widgets(array $filters = []): array', $content);
    
    // Replace public function reports(): array
    $content = preg_replace('/public function reports\(\)\s*:\s*array/', 'public function reports(array $filters = []): array', $content);
    
    file_put_contents($file, $content);
    echo "Refactored: " . basename($file) . "\n";
}
echo "Done\n";
