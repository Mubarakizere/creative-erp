<?php

$dir = __DIR__ . '/database/migrations/';
$files = glob($dir . '*.php');

$replacements = [
    "foreignUuid('tax_id')" => "foreignId('tax_id')",
    "foreignUuid('manager_id')" => "foreignId('manager_id')",
    "foreignUuid('inspected_by')" => "foreignId('inspected_by')",
    "foreignUuid('packed_by')" => "foreignId('packed_by')",
    "foreignUuid('assigned_to')" => "foreignId('assigned_to')",
    "foreignUuid('approved_by')" => "foreignId('approved_by')",
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;
    
    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }

    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "Fixed: " . basename($file) . "\n";
    }
}

echo "All done!\n";
