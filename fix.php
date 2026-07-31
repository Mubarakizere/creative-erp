<?php

$path = 'app/Http/Controllers/Admin/Inventory/InventoryReportController.php';

// Read file contents (it might have BOM or be UTF-16LE from powershell)
$content = file_get_contents($path);

// Convert UTF-16LE to UTF-8 if it starts with the BOM or looks like UTF-16
if (substr($content, 0, 2) === chr(0xFF) . chr(0xFE)) {
    $content = substr($content, 2); // remove BOM
    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
}

// Replace currency
$content = str_replace("'$' .", "'RWF ' .", $content);
$content = str_replace("['$', ',']", "['RWF', ' ', ',']", $content);

// Write back strictly as UTF-8
file_put_contents($path, $content);

echo "Encoding and currency fixed!";
