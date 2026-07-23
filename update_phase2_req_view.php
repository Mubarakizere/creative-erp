<?php

$reqShowPath = 'resources/views/admin/procurement/requisitions/show.blade.php';

// First check if file exists
if (file_exists($reqShowPath)) {
    $content = file_get_contents($reqShowPath);
    if (!str_contains($content, 'Compare Quotations')) {
        $btn = <<<'EOT'
        <div class="mt-4">
            <a href="{{ route('admin.procurement.requisitions.compare', $requisition->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded inline-block">Compare Quotations</a>
        </div>
EOT;
        $content = str_replace('</div>
</x-layouts.admin>', $btn . "\n    </div>\n</x-layouts.admin>", $content);
        file_put_contents($reqShowPath, $content);
        echo "Updated requisitions/show.blade.php\n";
    }
}
