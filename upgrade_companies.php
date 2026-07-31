<?php

$files = [
    'resources/views/admin/companies/show.blade.php',
    'resources/views/admin/companies/settings.blade.php',
    'resources/views/admin/companies/index.blade.php',
    'resources/views/admin/companies/edit.blade.php',
    'resources/views/admin/companies/create.blade.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Simple replacements
    $replacements = [
        'rounded-lg' => 'rounded-xl',
        'rounded-md' => 'rounded-xl',
        'border-gray-300' => 'border-gray-200/60',
    ];
    
    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    
    // Add min-h to the specific raw inputs
    $content = str_replace('py-2.5 px-3', 'py-2.5 px-3 min-h-[42px]', $content);

    file_put_contents($file, $content);
    echo "Cleaned up $file\n";
}
