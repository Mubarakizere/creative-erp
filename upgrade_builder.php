<?php

$path = 'app/Http/Controllers/Admin/ReportController.php'; // wait no, views path!
$path = 'resources/views/admin/reports/builder.blade.php';

$content = file_get_contents($path);

$replacements = [
    'rounded-md' => 'rounded-xl',
    'rounded-lg border-gray-300' => 'rounded-xl border-gray-200',
    'rounded-lg shadow-sm' => 'rounded-xl shadow-sm',
    'class="mt-1 block w-full rounded-xl border-gray-300' => 'class="mt-1 block w-full rounded-xl border-gray-200 min-h-[42px]',
    'class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors"' => 'class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors min-h-[42px]"',
    'bg-blue-50 border-blue-300 text-blue-900 transition-colors font-medium"' => 'bg-blue-50/50 border-blue-200 text-blue-900 transition-colors font-medium min-h-[42px]"',
    'bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden' => 'bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden',
    'bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-full flex flex-col relative' => 'bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden h-full flex flex-col relative',
    'class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50"' => 'class="inline-flex items-center px-4 py-2 border border-gray-200/60 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 min-h-[42px] shadow-sm transition-colors"',
    'class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl text-gray-700 bg-gray-100 hover:bg-gray-200"' => 'class="inline-flex items-center px-4 py-2 border border-gray-200/60 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 min-h-[42px] transition-colors"',
    'class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"' => 'class="w-full inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 min-h-[42px] transition-colors"',
];

foreach ($replacements as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}

// Add min-h-[42px] to all the filter inputs individually using regex
$content = preg_replace(
    '/(class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm")/',
    'class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm min-h-[42px] transition-colors"',
    $content
);

file_put_contents($path, $content);
echo "Upgrade script run successfully";
