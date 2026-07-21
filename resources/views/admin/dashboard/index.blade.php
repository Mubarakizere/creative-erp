<x-layouts.admin title="Dashboard">
    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500">Welcome back, {{ auth()->user()->first_name }}! Here's what's happening today.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- CRM Stats --}}
    @if(auth()->user()->can('lead.view') || auth()->user()->can('opportunity.view'))
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
        <x-stats-card title="Total Leads" value="{{ number_format($stats['total_leads'] ?? 0) }}" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </x-stats-card>
        <x-stats-card title="Total Opportunities" value="{{ number_format($stats['total_opportunities'] ?? 0) }}" color="indigo">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </x-stats-card>
        <x-stats-card title="Won Deals" value="{{ number_format($stats['won_deals'] ?? 0) }}" color="green">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </x-stats-card>
        <x-stats-card title="Lost Deals" value="{{ number_format($stats['lost_deals'] ?? 0) }}" color="red">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </x-stats-card>
        <x-stats-card title="Pipeline Value" value="{{ format_currency($stats['pipeline_value'] ?? 0) }}" color="emerald">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </x-stats-card>
        <x-stats-card title="Conversion Rate" value="{{ $stats['conversion_rate'] ?? 0 }}%" color="purple">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l10-16M6 9h.01M18 15h.01"/></svg>
        </x-stats-card>
    </div>
    @endif

    {{-- Quotation Stats --}}
    @if(auth()->user()->can('quotation.view'))
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
        <x-stats-card title="Total Quotations" value="{{ number_format($stats['total_quotations'] ?? 0) }}" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </x-stats-card>
        <x-stats-card title="Draft Quotations" value="{{ number_format($stats['draft_quotations'] ?? 0) }}" color="gray">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </x-stats-card>
        <x-stats-card title="Pending Approvals" value="{{ number_format($stats['pending_quotations'] ?? 0) }}" color="amber">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </x-stats-card>
        <x-stats-card title="Approved" value="{{ number_format($stats['approved_quotations'] ?? 0) }}" color="emerald">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </x-stats-card>
        <x-stats-card title="Accepted" value="{{ number_format($stats['accepted_quotations'] ?? 0) }}" color="indigo">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </x-stats-card>
        <x-stats-card title="Revenue Forecast" value="{{ format_currency($stats['revenue_forecast'] ?? 0) }}" color="purple">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </x-stats-card>
    </div>
    @endif

    {{-- Finance Stats --}}
    @if(auth()->user()->can('invoice.view') || auth()->user()->can('payment.view'))
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
        <x-stats-card title="Total Revenue" value="{{ $stats['total_payments']['value'] ?? '$0.00' }}" color="green">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </x-stats-card>
        <x-stats-card title="Revenue Today" value="{{ $stats['revenue_today']['value'] ?? '$0.00' }}" color="teal">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </x-stats-card>
        <x-stats-card title="Revenue This Month" value="{{ $stats['revenue_this_month']['value'] ?? '$0.00' }}" color="emerald">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </x-stats-card>
        <x-stats-card title="Total Receivables" value="{{ $stats['total_receivables']['value'] ?? '$0.00' }}" color="indigo">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </x-stats-card>
        <x-stats-card title="Overdue (1-30 Days)" value="{{ $stats['aging_30_days']['value'] ?? '$0.00' }}" color="yellow">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </x-stats-card>
        <x-stats-card title="Collection Rate" value="{{ $stats['collection_rate']['value'] ?? '0.0%' }}" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </x-stats-card>
    </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Projects --}}
        @can('project.view')
        <x-stats-card
            title="Total Projects"
            value="{{ number_format($stats['projects']) }}"
            trend="+12%"
            :trend-up="true"
            color="blue"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </x-stats-card>
        @endcan

        {{-- Active Projects --}}
        @can('project.view')
        <x-stats-card
            title="Active Projects"
            value="{{ number_format($stats['active_projects']) }}"
            trend="+5%"
            :trend-up="true"
            color="emerald"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
        </x-stats-card>
        @endcan

        {{-- Clients --}}
        @can('client.view')
        <x-stats-card
            title="Total Clients"
            value="{{ number_format($stats['clients']) }}"
            trend="+8%"
            :trend-up="true"
            color="purple"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </x-stats-card>
        @endcan

        {{-- Users --}}
        @can('user.view')
        <x-stats-card
            title="Total Users"
            value="{{ number_format($stats['users']) }}"
            trend="+3%"
            :trend-up="true"
            color="amber"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </x-stats-card>
        @endcan
    </div>

    {{-- Second Row Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        {{-- Companies --}}
        @can('company.view')
        <x-stats-card
            title="Companies"
            value="{{ number_format($stats['companies']) }}"
            color="cyan"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </x-stats-card>
        @endcan

        {{-- Branches --}}
        @can('branch.view')
        <x-stats-card
            title="Branches"
            value="{{ number_format($stats['branches']) }}"
            color="orange"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </x-stats-card>
        @endcan

        {{-- Departments --}}
        @can('department.view')
        <x-stats-card
            title="Departments"
            value="{{ number_format($stats['departments']) }}"
            color="indigo"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </x-stats-card>
        @endcan

        {{-- Est Budget --}}
        @can('project.view-budget')
        <x-stats-card
            title="Total Est. Budget"
            value="{{ format_currency($stats['total_estimated_budget']) }}"
            color="rose"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </x-stats-card>
        @endcan

        {{-- Act Budget --}}
        @can('project.view-budget')
        <x-stats-card
            title="Total Act. Budget"
            value="{{ format_currency($stats['total_actual_budget']) }}"
            color="emerald"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </x-stats-card>
        @endcan
    </div>

    {{-- Third Row Stats: Project Teams --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
        <x-stats-card title="Total Members" value="{{ number_format($stats['total_team_members']) }}" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Active Members" value="{{ number_format($stats['active_team_members']) }}" color="emerald">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Inactive Members" value="{{ number_format($stats['inactive_team_members']) }}" color="amber">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Project Managers" value="{{ number_format($stats['project_managers']) }}" color="purple">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Engineers" value="{{ number_format($stats['engineers']) }}" color="cyan">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Team Utilization" value="{{ $stats['total_team_members'] > 0 ? round(($stats['active_team_members'] / $stats['total_team_members']) * 100) : 0 }}%" color="indigo">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </x-stats-card>
    </div>

    {{-- Fourth Row Stats: Tasks --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stats-card title="My Assigned Tasks" value="{{ number_format($stats['my_tasks']) }}" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        </x-stats-card>

        <x-stats-card title="Active Tasks" value="{{ number_format($stats['active_tasks']) }}" color="emerald">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
        </x-stats-card>

        <x-stats-card title="Overdue Tasks" value="{{ number_format($stats['overdue_tasks']) }}" color="rose">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>

        <x-stats-card title="Critical Tasks" value="{{ number_format($stats['critical_tasks']) }}" color="orange">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </x-stats-card>
    </div>

    {{-- Fifth Row Stats: Milestones & Documents --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <x-stats-card title="Total Milestones" value="{{ number_format($stats['total_milestones']) }}" color="purple">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
        </x-stats-card>

        <x-stats-card title="Active Milestones" value="{{ number_format($stats['active_milestones']) }}" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
        </x-stats-card>

        <x-stats-card title="Completed Milestones" value="{{ number_format($stats['completed_milestones']) }}" color="emerald">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Total Documents" value="{{ number_format($stats['total_documents']) }}" color="cyan">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Doc Categories" value="{{ number_format($stats['document_categories']) }}" color="orange">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </x-stats-card>
    </div>

    {{-- Sixth Row Stats: Discussions --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <x-stats-card title="Total Discussions" value="{{ number_format($stats['total_discussions']) }}" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
        </x-stats-card>

        <x-stats-card title="Comments Today" value="{{ number_format($stats['comments_today']) }}" color="emerald">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>

        <x-stats-card title="My Mentions" value="{{ number_format($stats['my_mentions']) }}" color="rose">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Active Threads" value="{{ number_format($stats['active_threads']) }}" color="cyan">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Internal Notes" value="{{ number_format($stats['internal_notes']) }}" color="orange">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </x-stats-card>
    </div>

    {{-- Seventh Row Stats: Calendar & Meetings --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stats-card title="Meetings Today" value="{{ number_format($stats['meetings_today']) }}" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </x-stats-card>

        <x-stats-card title="Upcoming Meetings" value="{{ number_format($stats['upcoming_meetings']) }}" color="emerald">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>

        <x-stats-card title="Events This Week" value="{{ number_format($stats['events_this_week']) }}" color="purple">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Schedule Conflicts" value="{{ number_format($stats['schedule_conflicts']) }}" color="rose">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </x-stats-card>
    </div>

    {{-- Eighth Row Stats: Time Tracking --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stats-card title="Hours Today" value="{{ number_format($stats['hours_today'] ?? 0, 1) }}h" color="emerald">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>

        <x-stats-card title="Hours This Week" value="{{ number_format($stats['hours_this_week'] ?? 0, 1) }}h" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        </x-stats-card>

        <x-stats-card title="Active Timers" value="{{ number_format($stats['running_timers_count'] ?? 0) }}" color="rose">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Users Tracking Time" value="{{ number_format($stats['users_tracking_time'] ?? 0) }}" color="purple">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </x-stats-card>
    </div>

    {{-- Workflow & Approvals Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stats-card title="Pending Approvals" value="{{ number_format($stats['pending_approvals'] ?? 0) }}" color="amber">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>

        <x-stats-card title="Approved Today" value="{{ number_format($stats['approved_today'] ?? 0) }}" color="emerald">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>

        <x-stats-card title="Rejected Today" value="{{ number_format($stats['rejected_today'] ?? 0) }}" color="rose">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>

        <x-stats-card title="My Pending Requests" value="{{ number_format($stats['my_pending_requests'] ?? 0) }}" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </x-stats-card>
    </div>

    {{-- Bottom Section: Recent Projects + Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Latest Projects --}}
        <div class="lg:col-span-2 space-y-6">
            @can('project.view')
        <x-card>
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Latest Projects</h3>
                        <a href="{{ route('admin.projects.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</a>
                    </div>
                </x-slot:header>

                <div class="space-y-4">
                    @forelse($latestProjects as $project)
                        <div class="flex items-start gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('admin.projects.show', $project) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">{{ $project->name }}</a>
                                <p class="text-xs text-gray-500 mt-1">{{ $project->company?->name }} • {{ $project->created_at->diffForHumans() }}</p>
                            </div>
                            <x-badge :type="match($project->status) { 'Planning' => 'default', 'In Progress' => 'primary', 'Completed', 'Closed' => 'success', default => 'warning' }">
                                {{ $project->status }}
                            </x-badge>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 py-4 text-center">No projects created yet.</p>
                    @endforelse
                </div>
            </x-card>
        @endcan

            {{-- Recent Activity Feed (Sample Data) --}}
            @can('view-tasks')
        <x-card>
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">My Latest Tasks</h3>
                        <a href="{{ route('admin.projects.tasks.index', ['assigned_to' => auth()->id()]) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</a>
                    </div>
                </x-slot:header>

                <div class="space-y-4">
                    @forelse($myAssignedTasks as $task)
                        <div class="flex items-start gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('admin.projects.tasks.show', $task) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">{{ $task->name }}</a>
                                <p class="text-xs text-gray-500 mt-1">{{ $task->project->name }} • Due: {{ $task->due_date ? $task->due_date->format('M j, Y') : 'N/A' }}</p>
                            </div>
                            <x-badge :type="match($task->status) { 'Pending' => 'default', 'In Progress' => 'primary', 'Completed' => 'success', default => 'warning' }">
                                {{ $task->status }}
                            </x-badge>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 py-4 text-center">No open tasks assigned to you.</p>
                    @endforelse
                </div>
            </x-card>
        @endcan
            
            {{-- Latest Documents Feed --}}
            @can('document.view')
        <x-card>
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Recently Uploaded Documents</h3>
                        <a href="{{ route('admin.documents.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</a>
                    </div>
                </x-slot:header>

                <div class="space-y-4">
                    @forelse($latestDocuments as $document)
                        <div class="flex items-start gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('admin.documents.show', $document) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">{{ Str::limit($document->original_name, 30) }}</a>
                                <p class="text-xs text-gray-500 mt-1">{{ class_basename($document->documentable_type) }} • {{ $document->formatted_size }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $document->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 py-4 text-center">No documents uploaded yet.</p>
                    @endforelse
                </div>
            </x-card>
        @endcan

            {{-- Recent Discussions Feed --}}
            @can('comment.view')
        <x-card>
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Discussions</h3>
                    </div>
                </x-slot:header>

                <div class="space-y-4">
                    @forelse($recentDiscussions as $discussion)
                        <div class="flex items-start gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ Str::limit(strip_tags($discussion->body), 50) }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ class_basename($discussion->commentable_type) }} • by {{ $discussion->user?->first_name ?? 'Unknown User' }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $discussion->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 py-4 text-center">No discussions yet.</p>
                    @endforelse
                </div>
            </x-card>
        @endcan

            {{-- Recent Deals --}}
            @can('opportunity.view')
            <x-card>
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Opportunities</h3>
                        <a href="{{ route('admin.crm.opportunities.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</a>
                    </div>
                </x-slot:header>
                
                <div class="space-y-4">
                    @forelse($recentDeals ?? [] as $deal)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div>
                                <a href="{{ route('admin.crm.opportunities.show', $deal) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">{{ $deal->name }}</a>
                                <p class="text-xs text-gray-500">{{ $deal->account?->name ?? 'No Account' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-900">{{ format_currency($deal->expected_revenue) }}</p>
                                <x-badge type="default" class="text-xs">{{ $deal->status }}</x-badge>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No recent opportunities found.</p>
                    @endforelse
                </div>
            </x-card>
            @endcan
        </div>

        {{-- Right Column: Calendar, Quick Actions & Project Summary --}}
        <div class="space-y-6">
            {{-- Today's Schedule --}}
            @can('calendar.view')
        <x-card>
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Today's Schedule</h3>
                        <a href="{{ route('admin.calendar.agenda') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Agenda</a>
                    </div>
                </x-slot:header>

                <div class="space-y-3">
                    @forelse($todaysSchedule as $event)
                        <a href="{{ $event->url }}" class="block p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors" style="border-left: 3px solid {{ $event->color }};">
                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $event->title }}</h4>
                            <p class="text-xs text-gray-500 mt-1">
                                @if(!$event->allDay)
                                    {{ $event->start->format('g:i A') }} — {{ $event->end?->format('g:i A') }}
                                @else
                                    All Day
                                @endif
                            </p>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 py-4 text-center">Nothing scheduled for today.</p>
                    @endforelse
                </div>
            </x-card>
        @endcan

            {{-- Upcoming Meetings --}}
            @can('meeting.view')
        <x-card>
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Upcoming Meetings</h3>
                        <a href="{{ route('admin.meetings.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</a>
                    </div>
                </x-slot:header>

                <div class="space-y-4">
                    @forelse($upcomingMeetings as $meeting)
                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-50 rounded-lg flex flex-col items-center justify-center border border-blue-100">
                                <span class="text-[10px] font-bold text-blue-600 uppercase">{{ $meeting->start_at->format('M') }}</span>
                                <span class="text-sm font-bold text-blue-700 leading-none">{{ $meeting->start_at->format('j') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('admin.meetings.show', $meeting) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600 truncate block">{{ $meeting->title }}</a>
                                <p class="text-xs text-gray-500 mt-1">{{ $meeting->start_at->format('g:i A') }} • {{ $meeting->formatted_duration }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 py-4 text-center">No upcoming meetings.</p>
                    @endforelse
                </div>
            </x-card>
        @endcan

            {{-- Upcoming Activities --}}
            @can('activity.view')
            <x-card>
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Upcoming Activities</h3>
                        <a href="{{ route('admin.crm.activities.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</a>
                    </div>
                </x-slot:header>
                
                <div class="space-y-4">
                    @forelse($upcomingActivities ?? [] as $activity)
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="mt-1">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $activity->subject }}</p>
                                <p class="text-xs text-gray-500">{{ $activity->type }} • {{ $activity->scheduled_at->format('M j, Y g:i A') }}</p>
                                @if($activity->activityable)
                                    <p class="text-xs text-blue-600 mt-1">Related to: {{ class_basename($activity->activityable_type) }} #{{ $activity->activityable->id }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No upcoming activities.</p>
                    @endforelse
                </div>
            </x-card>
            @endcan

            {{-- Recent Notifications --}}
            @can('notification.view')
                @php
                    $dashboardNotifications = app(\App\Services\NotificationService::class)->getRecentNotifications(auth()->user(), 5);
                @endphp
                <x-notification-widget :notifications="$dashboardNotifications" />
            @endcan

            {{-- Announcements --}}
            @if(config('realtime.features.announcements', true))
                <x-dashboard-announcements />
            @endif

            {{-- Quick Actions --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </x-slot:header>

                <div class="grid grid-cols-2 gap-3">
                    @can('project.create')
        <a href="{{ route('admin.projects.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-blue-50 hover:text-blue-700 text-gray-600 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium">New Project</span>
                    </a>
        @endcan
                    @can('project-team.create')
        <a href="{{ route('admin.projects.team.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-blue-50 hover:text-blue-700 text-gray-600 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium">Assign Member</span>
                    </a>
        @endcan
                    @can('client.create')
        <a href="{{ route('admin.clients.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-emerald-50 hover:text-emerald-700 text-gray-600 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium">New Client</span>
                    </a>
        @endcan
                    @can('user.create')
        <a href="{{ route('admin.users.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-purple-50 hover:text-purple-700 text-gray-600 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium">Add User</span>
                    </a>
        @endcan
                    @can('company.create')
        <a href="{{ route('admin.companies.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-amber-50 hover:text-amber-700 text-gray-600 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium">New Company</span>
                    </a>
        @endcan
                    @can('meeting.create')
        <a href="{{ route('admin.meetings.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-rose-50 hover:text-rose-700 text-gray-600 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium">New Meeting</span>
                    </a>
        @endcan
                </div>
            </x-card>

            {{-- Project Status Summary --}}
            @can('project.view')
        <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Projects by Status</h3>
                </x-slot:header>

                @php
                    $total = $stats['projects'] > 0 ? $stats['projects'] : 1;
                    $activePct = round(($stats['active_projects'] / $total) * 100);
                    $completedPct = round(($stats['completed_projects'] / $total) * 100);
                    $closedPct = round(($stats['closed_projects'] / $total) * 100);
                    $holdPct = round(($stats['on_hold_projects'] / $total) * 100);
                @endphp

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Active</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['active_projects']) }} ({{ $activePct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $activePct }}%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Completed</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['completed_projects']) }} ({{ $completedPct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $completedPct }}%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Closed</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['closed_projects']) }} ({{ $closedPct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $closedPct }}%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">On Hold</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['on_hold_projects']) }} ({{ $holdPct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $holdPct }}%"></div>
                    </div>
                </div>
            </x-card>
        @endcan
        </div>
    </div>

    {{-- Latest Team Members --}}
    <div class="mt-8">
        @can('project-team.view')
        <x-card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recently Assigned Team Members</h3>
                    <a href="{{ route('admin.projects.team.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All Team Members</a>
                </div>
            </x-slot:header>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role & Dept</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Join Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($latestTeamMembers ?? [] as $member)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            @if($member->user->avatar)
                                                <img class="h-8 w-8 rounded-full" src="{{ asset('storage/' . $member->user->avatar) }}" alt="">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                                    {{ $member->user->initials }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $member->user->full_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.projects.show', $member->project_id) }}" class="text-sm text-blue-600 hover:text-blue-900">{{ $member->project->name }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $member->project_role }}</div>
                                    <div class="text-xs text-gray-500">{{ $member->department->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $member->joined_at->format('M j, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No team members assigned yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
        @endcan
    </div>
</x-layouts.admin>
