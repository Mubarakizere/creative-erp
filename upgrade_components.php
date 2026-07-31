<?php

$files = [
    'resources/views/components/card.blade.php' => [
        'bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md' => 'bg-white rounded-2xl border border-gray-200/60 shadow-sm hover:shadow-md'
    ],
    'resources/views/components/input.blade.php' => [
        "class' => 'block w-full rounded-lg border '" => "class' => 'block w-full rounded-xl border '",
        "' pr-3 py-2.5 disabled:bg-gray-50" => "' pr-3 py-2.5 min-h-[42px] disabled:bg-gray-50",
    ],
    'resources/views/components/select.blade.php' => [
        "class' => 'block w-full rounded-lg border '" => "class' => 'block w-full rounded-xl border '",
        "' py-2.5 pr-10 disabled:bg-gray-50" => "' py-2.5 pr-10 min-h-[42px] disabled:bg-gray-50",
    ],
    'resources/views/components/textarea.blade.php' => [
        "class' => 'block w-full rounded-lg border '" => "class' => 'block w-full rounded-xl border '",
    ],
    'resources/views/components/button.blade.php' => [
        "font-medium rounded-lg transition-all" => "font-medium rounded-xl transition-all",
        "'sm' => 'px-3 py-2 text-sm'" => "'sm' => 'px-3 py-2 min-h-[38px] text-sm'",
        "'md' => 'px-4 py-2.5 text-sm'" => "'md' => 'px-4 py-2.5 min-h-[42px] text-sm'",
        "'lg' => 'px-5 py-3 text-base'" => "'lg' => 'px-5 py-3 min-h-[48px] text-base'",
    ]
];

foreach ($files as $file => $replacements) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    file_put_contents($file, $content);
    echo "Updated $file\n";
}
echo "Global component styles upgraded to premium aesthetic!\n";
