<?php
$path = 'app/Http/Controllers/Admin/Inventory/InventoryReportController.php';
$content = file_get_contents($path);

// Check if it's UTF-16 by looking for null bytes between ascii characters
if (strpos($content, "\0") !== false) {
    // It's UTF-16LE
    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
    // Strip BOM if present
    if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
        $content = substr($content, 3);
    }
}

file_put_contents($path, $content);
echo "Converted successfully!";
