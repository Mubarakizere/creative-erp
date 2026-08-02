<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$pattern = '/[ \t]*@if\s*\(\s*session\([\'"](success|error|info|warning|status)[\'"]\)\s*\).*?@endif[ \t]*\n?/s';
$count = 0;

foreach($files as $file) {
    $path = $file[0];
    
    if (strpos(str_replace('\\', '/', $path), 'components/layouts/app.blade.php') !== false) {
        continue;
    }

    $content = file_get_contents($path);
    $new_content = preg_replace($pattern, '', $content, -1, $num_subs);
    
    if ($num_subs > 0) {
        file_put_contents($path, $new_content);
        echo "Cleaned $num_subs alerts in $path\n";
        $count += $num_subs;
    }
}

echo "Total alerts removed: $count\n";
