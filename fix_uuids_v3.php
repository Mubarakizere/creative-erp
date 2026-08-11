<?php

$dir = __DIR__ . '/database/migrations/';
$files = glob($dir . '*.php');

foreach ($files as $file) {
    if (strpos($file, 'notifications_table') !== false) {
        continue; // Skip notifications table
    }

    $content = file_get_contents($file);
    $original = $content;

    // Convert UUID primary keys to standard BigInt IDs (auto-increment)
    $content = str_replace(
        "\$table->uuid('id')->primary();", 
        "\$table->id();\n            \$table->uuid('uuid')->unique();", 
        $content
    );

    // Convert all foreignUuid to foreignId
    $content = str_replace("foreignUuid(", "foreignId(", $content);
    
    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Fixed: " . basename($file) . "\n";
    }
}

echo "All done!\n";
