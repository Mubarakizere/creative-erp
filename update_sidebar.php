<?php

$sidebarPath = 'resources/views/components/sidebar.blade.php';
$content = file_get_contents($sidebarPath);

$sidebarLinks = <<<'EOT'

        {{-- Procurement --}}
        @canany(['supplier.view', 'procurement.view'])
        <div class="px-4 mt-6 mb-2">
            <h3 class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Procurement</h3>
        </div>
        @endcanany

        @can('viewAny', \App\Models\Supplier::class)
        <a href="{{ route('admin.procurement.suppliers.index') }}"
           class="flex items-center px-4 py-2 mt-1 rounded-md transition-colors {{ request()->routeIs('admin.procurement.suppliers.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
            <i data-lucide="truck" class="w-5 h-5 mr-3"></i>
            <span>Suppliers</span>
        </a>
        @endcan

        @can('viewAny', \App\Models\PurchaseRequisition::class)
        <a href="{{ route('admin.procurement.requisitions.index') }}"
           class="flex items-center px-4 py-2 mt-1 rounded-md transition-colors {{ request()->routeIs('admin.procurement.requisitions.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
            <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
            <span>Requisitions</span>
        </a>
        @endcan

        @can('viewAny', \App\Models\SupplierQuotation::class)
        <a href="{{ route('admin.procurement.rfqs.index') }}"
           class="flex items-center px-4 py-2 mt-1 rounded-md transition-colors {{ request()->routeIs('admin.procurement.rfqs.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
            <i data-lucide="file-question" class="w-5 h-5 mr-3"></i>
            <span>RFQs</span>
        </a>
        @endcan
EOT;

if (!str_contains($content, '{{-- Procurement --}}')) {
    // Insert after Inventory block
    $inventoryMarker = "{{-- Projects --}}";
    $pos = strpos($content, $inventoryMarker);
    if ($pos !== false) {
        $content = substr_replace($content, $sidebarLinks . "\n\n        ", $pos, 0);
        file_put_contents($sidebarPath, $content);
        echo "Added Procurement links to sidebar\n";
    }
} else {
    echo "Sidebar links already exist\n";
}
