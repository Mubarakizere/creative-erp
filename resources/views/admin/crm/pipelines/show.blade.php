<x-layouts.admin title="Pipeline Dashboard">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Pipelines', 'url' => route('admin.crm.pipelines.index')], ['label' => $pipeline->name]]; @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $pipeline->name }}</h1>
                <div class="flex items-center gap-3 mt-1.5">
                    <p class="text-sm font-semibold text-gray-500 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $pipeline->company?->name ?? 'Global Context' }}
                    </p>
                    @if($pipeline->is_active ?? true)
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase tracking-widest border border-emerald-200/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                        </span>
                    @endif
                    @if($pipeline->is_default ?? false)
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 uppercase tracking-widest border border-amber-200/50">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Default
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @can('update', $pipeline)
                <a href="{{ route('admin.crm.pipelines.edit', $pipeline) }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm hover:shadow">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Settings
                </a>
            @endcan
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-600 uppercase tracking-widest">Active Deals</h3>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-gray-900">{{ number_format($activeDeals) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-600 uppercase tracking-widest">Pipeline Value</h3>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-gray-900">{{ number_format($pipelineValue, 2) }}</span>
                    <span class="text-xs font-bold text-gray-500">RWF</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-purple-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-600 uppercase tracking-widest">Win Rate</h3>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-gray-900">{{ $winRate }}%</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-600 uppercase tracking-widest">Stages</h3>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-gray-900">{{ $pipeline->stages_count ?? (method_exists($pipeline, 'stages') ? $pipeline->stages()->count() : 0) }}</span>
                    <span class="text-xs font-bold text-gray-500">Configured</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Pipeline Kanban Visual Mockup --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="bg-gray-50/80 border-b border-gray-100 px-8 py-5 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Pipeline Stages & Flow</h3>
            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Manage Stages
            </button>
        </div>
        <div class="p-8 overflow-x-auto">
            <div class="flex gap-6 min-w-max pb-4">
                @forelse($pipeline->stages as $stage)
                @php
                    $colors = [
                        'gray' => ['border' => 'border-gray-500', 'text' => 'text-gray-600', 'bg' => 'bg-gray-100', 'hover' => 'group-hover:text-gray-500'],
                        'blue' => ['border' => 'border-blue-500', 'text' => 'text-blue-600', 'bg' => 'bg-blue-100', 'hover' => 'group-hover:text-blue-500'],
                        'indigo' => ['border' => 'border-indigo-500', 'text' => 'text-indigo-600', 'bg' => 'bg-indigo-100', 'hover' => 'group-hover:text-indigo-500'],
                        'purple' => ['border' => 'border-purple-500', 'text' => 'text-purple-600', 'bg' => 'bg-purple-100', 'hover' => 'group-hover:text-purple-500'],
                        'amber' => ['border' => 'border-amber-500', 'text' => 'text-amber-600', 'bg' => 'bg-amber-100', 'hover' => 'group-hover:text-amber-500'],
                        'yellow' => ['border' => 'border-yellow-500', 'text' => 'text-yellow-600', 'bg' => 'bg-yellow-100', 'hover' => 'group-hover:text-yellow-500'],
                        'emerald' => ['border' => 'border-emerald-500', 'text' => 'text-emerald-600', 'bg' => 'bg-emerald-100', 'hover' => 'group-hover:text-emerald-500'],
                        'red' => ['border' => 'border-red-500', 'text' => 'text-red-600', 'bg' => 'bg-red-100', 'hover' => 'group-hover:text-red-500'],
                    ];
                    $baseColor = explode('-', str_replace('bg-', '', $stage->color ?? 'bg-gray-500'))[0] ?? 'gray';
                    $theme = $colors[$baseColor] ?? $colors['gray'];
                @endphp
                <div class="w-80 flex flex-col">
                    <div class="flex items-center justify-between mb-4 border-b-2 {{ $theme['border'] }} pb-2">
                        <h4 class="text-sm font-extrabold text-gray-900 uppercase tracking-widest">{{ $stage->name }}</h4>
                        <span class="text-xs font-bold {{ $theme['text'] }} {{ $theme['bg'] }} px-2 py-0.5 rounded-full">{{ $stage->opportunities_count ?? 0 }}</span>
                    </div>
                    <div class="flex-1 bg-gray-50/50 rounded-2xl border border-gray-100 border-dashed p-4 flex flex-col items-center justify-center text-center min-h-[250px] relative group">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center mb-3 text-gray-400 {{ $theme['hover'] }} group-hover:scale-110 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <p class="text-sm font-bold text-gray-600">Drag deals here</p>
                        <p class="text-[10px] font-bold text-gray-400 mt-2">{{ $stage->probability }}% Win Probability</p>
                    </div>
                </div>
                @empty
                <div class="w-full text-center py-12">
                    <p class="text-sm text-gray-500 font-medium">No stages configured yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
