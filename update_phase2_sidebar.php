<?php

$sidebarPath = 'resources/views/components/sidebar.blade.php';
$content = file_get_contents($sidebarPath);

$sidebarLinks = <<<'EOT'

        @can('viewAny', \App\Models\PurchaseOrder::class)
        <a href="{{ route('admin.procurement.pos.index') }}"
           class="flex items-center px-4 py-2 mt-1 rounded-md transition-colors {{ request()->routeIs('admin.procurement.pos.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
            <i data-lucide="shopping-cart" class="w-5 h-5 mr-3"></i>
            <span>Purchase Orders</span>
        </a>
        @endcan

        @can('viewAny', \App\Models\GoodsReceipt::class)
        <a href="{{ route('admin.procurement.receipts.index') }}"
           class="flex items-center px-4 py-2 mt-1 rounded-md transition-colors {{ request()->routeIs('admin.procurement.receipts.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
            <i data-lucide="package-check" class="w-5 h-5 mr-3"></i>
            <span>Goods Receipts</span>
        </a>
        @endcan
EOT;

if (!str_contains($content, 'pos.index')) {
    $pos = strpos($content, "route('admin.procurement.rfqs.index')");
    if ($pos !== false) {
        // Find the end of the RFQ tag
        $endPos = strpos($content, "@endcan", $pos);
        if ($endPos !== false) {
            $endPos += 7; // Length of @endcan
            $content = substr_replace($content, "\n" . $sidebarLinks, $endPos, 0);
            file_put_contents($sidebarPath, $content);
            echo "Added PO and GR links to sidebar\n";
        }
    }
}
