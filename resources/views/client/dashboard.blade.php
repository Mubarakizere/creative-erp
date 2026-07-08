<x-layouts.client title="Client Dashboard">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Client Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500">View your projects and track progress.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-stats-card title="My Projects" value="3" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </x-stats-card>

        <x-stats-card title="Documents" value="12" color="purple">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </x-stats-card>

        <x-stats-card title="Invoices" value="5" color="amber">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
            </svg>
        </x-stats-card>
    </div>

    <div class="mt-8">
        <x-card>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900">Your Projects</h3>
            </x-slot:header>
            <p class="text-sm text-gray-500">Your project details will appear here once connected.</p>
        </x-card>
    </div>
</x-layouts.client>
