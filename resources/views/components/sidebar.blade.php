{{-- Admin Sidebar Component --}}
<aside
    :class="sidebarOpen ? 'w-64' : 'w-20'"
    class="fixed inset-y-0 left-0 z-40 bg-sidebar text-white transition-all duration-300 hidden lg:flex lg:flex-col"
>
    {{-- Logo --}}
    <div class="flex items-center h-16 px-4 border-b border-white/10">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20 flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
        </div>
        <span x-show="sidebarOpen" x-transition class="ml-3 text-lg font-bold whitespace-nowrap">
            Creative <span class="text-blue-400">ERP</span>
        </span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto scrollbar-thin py-4 px-3 space-y-1">
        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.dashboard'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.dashboard'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Dashboard</span>
        </a>
        {{-- Calendar --}}
        @can('calendar.view')
        <a href="{{ route('admin.calendar.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.calendar.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.calendar.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Calendar</span>
        </a>
        @endcan

        {{-- Meetings --}}
        @can('viewAny', \App\Models\Meeting::class)
        <a href="{{ route('admin.meetings.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.meetings.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.meetings.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Meetings</span>
        </a>
        @endcan
        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">CRM</p>
        </div>

        {{-- Leads --}}
        @can('viewAny', \App\Models\Lead::class)
        <a href="{{ route('admin.crm.leads.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.crm.leads.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.crm.leads.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Leads</span>
        </a>
        @endcan

        {{-- Opportunities --}}
        @can('viewAny', \App\Models\Opportunity::class)
        <a href="{{ route('admin.crm.opportunities.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.crm.opportunities.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.crm.opportunities.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Opportunities</span>
        </a>
        @endcan

        {{-- Quotations --}}
        @can('viewAny', \App\Models\Quotation::class)
        <a href="{{ route('admin.crm.quotations.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.crm.quotations.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.crm.quotations.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Quotations</span>
        </a>
        @endcan

        {{-- Pipelines --}}
        @can('viewAny', \App\Models\Pipeline::class)
        <a href="{{ route('admin.crm.pipelines.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.crm.pipelines.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.crm.pipelines.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Pipelines</span>
        </a>
        @endcan

        {{-- Accounts --}}
        @can('viewAny', \App\Models\Account::class)
        <a href="{{ route('admin.crm.accounts.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.crm.accounts.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.crm.accounts.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Accounts</span>
        </a>
        @endcan

        {{-- Contacts --}}
        @can('viewAny', \App\Models\Contact::class)
        <a href="{{ route('admin.crm.contacts.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.crm.contacts.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.crm.contacts.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Contacts</span>
        </a>
        @endcan

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Productivity</p>
        </div>

        {{-- Time Tracking --}}
        @can('viewAny', \App\Models\TimeEntry::class)
        <a href="{{ route('admin.time-tracking.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.time-tracking.index'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.time-tracking.index'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Time Tracking</span>
        </a>
        @endcan

        {{-- Timesheets --}}
        @can('time.view')
        <a href="{{ route('admin.time-tracking.timesheet') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.time-tracking.timesheet'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.time-tracking.timesheet'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">My Timesheet</span>
        </a>
        @endcan

        {{-- Reports --}}
        @can('time.view')
        <a href="{{ route('admin.time-tracking.reports') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.time-tracking.reports'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.time-tracking.reports'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Reports</span>
        </a>
        @endcan

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Workflow</p>
        </div>

        {{-- My Approvals --}}
        @can('viewAny', \App\Models\Approval::class)
        <a href="{{ route('admin.approvals.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.approvals.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.approvals.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">My Approvals</span>
        </a>
        @endcan

        {{-- Approval Workflows --}}
        @can('viewAny', \App\Models\ApprovalWorkflow::class)
        <a href="{{ route('admin.workflows.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.workflows.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.workflows.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Workflow Builder</span>
        </a>
        @endcan

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Organization</p>
        </div>

        {{-- Announcements --}}
        @can('viewAny', \App\Models\Announcement::class)
        <a href="{{ route('admin.announcements.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.announcements.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.announcements.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Announcements</span>
        </a>
        @endcan

        {{-- Companies --}}
        @can('viewAny', \App\Models\Company::class)
        <a href="{{ route('admin.companies.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.companies.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.companies.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Companies</span>
        </a>
        @endcan

        {{-- Branches --}}
        @can('viewAny', \App\Models\Branch::class)
        <a href="{{ route('admin.branches.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.branches.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.branches.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Branches</span>
        </a>
        @endcan

        {{-- Departments --}}
        @can('viewAny', \App\Models\Department::class)
        <a href="{{ route('admin.departments.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.departments.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.departments.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Departments</span>
        </a>
        @endcan

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Security</p>
        </div>

        {{-- Roles --}}
        @can('role.view')
        <a href="{{ route('admin.roles.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.roles.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.roles.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Roles</span>
        </a>
        @endcan

        {{-- Permissions --}}
        @can('permission.view')
        <a href="{{ route('admin.permissions.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.permissions.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.permissions.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Permissions</span>
        </a>
        @endcan

        {{-- Users --}}
        @can('viewAny', \App\Models\User::class)
        <a href="{{ route('admin.users.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.users.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.users.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Users</span>
        </a>
        @endcan

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Management</p>
        </div>

        {{-- Clients --}}
        @can('viewAny', \App\Models\Client::class)
        <a href="{{ route('admin.clients.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.clients.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.clients.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Clients</span>
        </a>
        @endcan

        
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

        @can('viewAny', \App\Models\PurchaseInvoice::class)
        <a href="{{ route('admin.procurement.invoices.index') }}"
           class="flex items-center px-4 py-2 mt-1 rounded-md transition-colors {{ request()->routeIs('admin.procurement.invoices.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
            <i data-lucide="file-spreadsheet" class="w-5 h-5 mr-3"></i>
            <span>Purchase Invoices</span>
        </a>
        @endcan

        @can('viewAny', \App\Models\SupplierPayment::class)
        <a href="{{ route('admin.procurement.payments.index') }}"
           class="flex items-center px-4 py-2 mt-1 rounded-md transition-colors {{ request()->routeIs('admin.procurement.payments.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
            <i data-lucide="banknote" class="w-5 h-5 mr-3"></i>
            <span>Supplier Payments</span>
        </a>
        @endcan

        {{-- Projects --}}
        @can('viewAny', \App\Models\Project::class)
        <a href="{{ route('admin.projects.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.projects.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.projects.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Projects</span>
        </a>
        @endcan

        {{-- Project Teams --}}
        @can('viewAny', \App\Models\ProjectMember::class)
        <a href="{{ route('admin.projects.team.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.projects.team.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.projects.team.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Project Teams</span>
        </a>
        @endcan

        {{-- Tasks --}}
        @can('viewAny', \App\Models\Task::class)
        <a href="{{ route('admin.projects.tasks.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.projects.tasks.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.projects.tasks.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Tasks</span>
        </a>
        @endcan

        {{-- Milestones --}}
        @can('viewAny', \App\Models\Milestone::class)
        <a href="{{ route('admin.milestones.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.milestones.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.milestones.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Milestones</span>
        </a>
        @endcan

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Documents</p>
        </div>

        {{-- Document Categories --}}
        @can('viewAny', \App\Models\DocumentCategory::class)
        <a href="{{ route('admin.document-categories.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.document-categories.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.document-categories.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Doc Categories</span>
        </a>
        @endcan

        {{-- Documents --}}
        @can('viewAny', \App\Models\Document::class)
        <a href="{{ route('admin.documents.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.documents.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.documents.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Documents</span>
        </a>
        @endcan

        {{-- Discussions --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed group justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Discussions</span>
            </div>
            <span x-show="sidebarOpen" class="text-[10px] uppercase font-bold bg-gray-700 text-gray-300 px-1.5 py-0.5 rounded">Soon</span>
        </a>

        {{-- Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Resources</p>
        </div>

        {{-- Inventory --}}
        @can('viewAny', \App\Models\Product::class)
        <a href="{{ route('admin.inventory.dashboard') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.inventory.dashboard'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.inventory.dashboard'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Dashboard</span>
        </a>

        <a href="{{ route('admin.inventory.reports.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.inventory.reports.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.inventory.reports.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Reports</span>
        </a>

        <a href="{{ route('admin.inventory.products.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.inventory.products.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.inventory.products.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Products</span>
        </a>

        <a href="{{ route('admin.inventory.categories.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.inventory.categories.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.inventory.categories.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Categories</span>
        </a>

        <a href="{{ route('admin.inventory.brands.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.inventory.brands.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.inventory.brands.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Brands</span>
        </a>

        <a href="{{ route('admin.inventory.units.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.inventory.units.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.inventory.units.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Units of Measure</span>
        </a>

        <a href="{{ route('admin.inventory.warehouses.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.inventory.warehouses.*') || request()->routeIs('admin.inventory.zones.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !(request()->routeIs('admin.inventory.warehouses.*') || request()->routeIs('admin.inventory.zones.*')),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Warehouses</span>
        </a>
        @endcan
        
        @can('viewAny', \App\Models\InventoryAdjustment::class)
        <a href="{{ route('admin.inventory.adjustments.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.inventory.adjustments.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.inventory.adjustments.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Adjustments</span>
        </a>
        @endcan

        {{-- Transfers --}}
        @can('viewAny', \App\Models\InventoryTransfer::class)
        <a href="{{ route('admin.inventory.transfers.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.inventory.transfers.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.inventory.transfers.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Transfers</span>
        </a>
        @endcan

        {{-- Reservations --}}
        @can('viewAny', \App\Models\InventoryReservation::class)
        <a href="{{ route('admin.inventory.reservations.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.inventory.reservations.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.inventory.reservations.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Reservations</span>
        </a>
        @endcan

        {{-- Stock Counts --}}
        <a href="{{ route('admin.inventory.stock-counts.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.inventory.stock-counts.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.inventory.stock-counts.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Stock Counts</span>
        </a>

        {{-- Valuation --}}
        <a href="{{ route('admin.inventory.valuation.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.inventory.valuation.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.inventory.valuation.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Valuation</span>
        </a>

        {{-- Accounting Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Accounting</p>
        </div>

        {{-- Chart of Accounts --}}
        <a href="{{ route('admin.finance.accounting.chart-of-accounts.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.finance.accounting.chart-of-accounts.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.accounting.chart-of-accounts.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Chart of Accounts</span>
        </a>

        {{-- Journal Entries --}}
        <a href="{{ route('admin.finance.accounting.journals.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.finance.accounting.journals.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.accounting.journals.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Journal Entries</span>
        </a>

        {{-- Ledger --}}
        <a href="{{ route('admin.finance.accounting.ledger.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.finance.accounting.ledger.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.accounting.ledger.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">General Ledger</span>
        </a>

        {{-- Fiscal Settings --}}
        <a href="{{ route('admin.finance.accounting.fiscal-periods.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.finance.accounting.fiscal-periods.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.accounting.fiscal-periods.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Fiscal Settings</span>
        </a>

        {{-- Finance Divider --}}
        <div x-show="sidebarOpen" class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Finance</p>
        </div>

        {{-- Budgets --}}
        @can('viewAny', \App\Models\Budget::class)
        <a href="{{ route('admin.finance.budgets.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.finance.budgets.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.budgets.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Budgets</span>
        </a>
        @endcan

        {{-- Invoices --}}
        @can('viewAny', \App\Models\Invoice::class)
        <a href="{{ route('admin.finance.invoices.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.finance.invoices.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.invoices.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Invoices</span>
        </a>
        @endcan

        {{-- Payments --}}
        @can('viewAny', \App\Models\Payment::class)
        <a href="{{ route('admin.finance.payments.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.finance.payments.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.payments.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Payments</span>
        </a>
        @endcan

        {{-- Credit Notes --}}
        @can('viewAny', \App\Models\CreditNote::class)
        <a href="{{ route('admin.finance.credit-notes.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.finance.credit-notes.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.credit-notes.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Credit Notes</span>
        </a>
        @endcan

        {{-- Refunds --}}
        @can('viewAny', \App\Models\Refund::class)
        <a href="{{ route('admin.finance.refunds.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.finance.refunds.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.refunds.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Refunds</span>
        </a>
        @endcan

        {{-- Finance Settings --}}
        @can('create', \App\Models\Payment::class)
        <a href="{{ route('admin.finance.settings') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.finance.settings'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.settings'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Finance Settings</span>
        </a>
        @endcan

        {{-- Reports --}}
        @can('report.view')
        <a href="{{ route('admin.reports.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.reports.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.reports.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Reports</span>
        </a>
        @endcan

        {{-- Settings --}}
        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed group justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Settings</span>
            </div>
            <span x-show="sidebarOpen" class="text-[10px] uppercase font-bold bg-gray-700 text-gray-300 px-1.5 py-0.5 rounded">Soon</span>
        </a>
    </nav>

    {{-- Collapse Toggle --}}
    <div class="border-t border-white/10 p-3">
        <button
            @click="sidebarOpen = !sidebarOpen"
            class="flex items-center justify-center w-full px-3 py-2 rounded-lg text-gray-400 hover:text-white hover:bg-sidebar-hover transition-all duration-200"
        >
            <svg x-show="sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <svg x-show="!sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</aside>

{{-- Mobile Sidebar --}}
<aside
    x-show="mobileMenuOpen"
    x-transition:enter="transition ease-in-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in-out duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar text-white lg:hidden flex flex-col"
    style="display: none;"
>
    {{-- Mobile Logo --}}
    <div class="flex items-center justify-between h-16 px-4 border-b border-white/10">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <span class="ml-3 text-lg font-bold">Creative <span class="text-blue-400">ERP</span></span>
        </div>
        <button @click="mobileMenuOpen = false" class="text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Mobile Navigation (same links) --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <a href="{{ route('admin.dashboard') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.dashboard'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.dashboard'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        @can('calendar.view')
        <a href="{{ route('admin.calendar.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.calendar.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.calendar.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Calendar
        </a>
        @endcan
        @can('viewAny', \App\Models\Meeting::class)
        <a href="{{ route('admin.meetings.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.meetings.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.meetings.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Meetings
        </a>
        @endcan

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">CRM</p>
        </div>

        @can('viewAny', \App\Models\Lead::class)
        <a href="{{ route('admin.crm.leads.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.crm.leads.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.crm.leads.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Leads
        </a>
        @endcan

        @can('viewAny', \App\Models\Opportunity::class)
        <a href="{{ route('admin.crm.opportunities.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.crm.opportunities.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.crm.opportunities.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            Opportunities
        </a>
        @endcan

        @can('viewAny', \App\Models\Quotation::class)
        <a href="{{ route('admin.crm.quotations.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.crm.quotations.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.crm.quotations.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Quotations
        </a>
        @endcan

        @can('viewAny', \App\Models\Pipeline::class)
        <a href="{{ route('admin.crm.pipelines.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.crm.pipelines.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.crm.pipelines.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Pipelines
        </a>
        @endcan

        @can('viewAny', \App\Models\Account::class)
        <a href="{{ route('admin.crm.accounts.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.crm.accounts.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.crm.accounts.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Accounts
        </a>
        @endcan

        @can('viewAny', \App\Models\Contact::class)
        <a href="{{ route('admin.crm.contacts.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.crm.contacts.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.crm.contacts.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Contacts
        </a>
        @endcan
        @can('viewAny', \App\Models\TimeEntry::class)
        <a href="{{ route('admin.time-tracking.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.time-tracking.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.time-tracking.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Time Tracking
        </a>
        @endcan
        @can('viewAny', \App\Models\Announcement::class)
        <a href="{{ route('admin.announcements.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.announcements.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.announcements.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            Announcements
        </a>
        @endcan
        @can('viewAny', \App\Models\Company::class)
        <a href="{{ route('admin.companies.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.companies.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.companies.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Companies
        </a>
        @endcan
        @can('viewAny', \App\Models\Branch::class)
        <a href="{{ route('admin.branches.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.branches.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.branches.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Branches
        </a>
        @endcan

        {{-- Departments --}}
        @can('viewAny', \App\Models\Department::class)
        <a href="{{ route('admin.departments.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.departments.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.departments.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Departments
        </a>
        @endcan

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Workflow</p>
        </div>

        @can('viewAny', \App\Models\Approval::class)
        <a href="{{ route('admin.approvals.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.approvals.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.approvals.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            My Approvals
        </a>
        @endcan

        @can('viewAny', \App\Models\ApprovalWorkflow::class)
        <a href="{{ route('admin.workflows.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.workflows.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.workflows.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Workflow Builder
        </a>
        @endcan

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Security</p>
        </div>

        @can('role.view')
        <a href="{{ route('admin.roles.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.roles.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.roles.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Roles
        </a>
        @endcan

        @can('permission.view')
        <a href="{{ route('admin.permissions.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.permissions.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.permissions.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Permissions
        </a>
        @endcan

        @can('viewAny', \App\Models\User::class)
        <a href="{{ route('admin.users.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.users.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.users.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Users
        </a>
        @endcan

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Management</p>
        </div>

        @can('viewAny', \App\Models\Client::class)
        <a href="{{ route('admin.clients.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.clients.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.clients.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Clients
        </a>
        @endcan

        @can('viewAny', \App\Models\Project::class)
        <a href="{{ route('admin.projects.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.projects.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.projects.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Projects
        </a>
        @endcan

        @can('viewAny', \App\Models\ProjectMember::class)
        <a href="{{ route('admin.projects.team.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.projects.team.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.projects.team.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Project Teams
        </a>
        @endcan

        @can('viewAny', \App\Models\Task::class)
        <a href="{{ route('admin.projects.tasks.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.projects.tasks.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.projects.tasks.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Tasks</span>
        </a>
        @endcan

        {{-- Milestones --}}
        @can('viewAny', \App\Models\Milestone::class)
        <a href="{{ route('admin.milestones.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.milestones.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.milestones.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Milestones</span>
        </a>
        @endcan

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Documents</p>
        </div>

        @can('viewAny', \App\Models\DocumentCategory::class)
        <a href="{{ route('admin.document-categories.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.document-categories.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.document-categories.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 whitespace-nowrap">Doc Categories</span>
        </a>
        @endcan

        @can('viewAny', \App\Models\Document::class)
        <a href="{{ route('admin.documents.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.documents.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.documents.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="ml-3 whitespace-nowrap">Documents</span>
        </a>
        @endcan

        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed group justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="ml-3 whitespace-nowrap">Discussions</span>
            </div>
            <span class="text-[10px] uppercase font-bold bg-gray-700 text-gray-300 px-1.5 py-0.5 rounded">Soon</span>
        </a>

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Accounting</p>
        </div>

        <a href="{{ route('admin.finance.accounting.chart-of-accounts.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.finance.accounting.chart-of-accounts.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.accounting.chart-of-accounts.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Chart of Accounts
        </a>

        <a href="{{ route('admin.finance.accounting.journals.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.finance.accounting.journals.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.accounting.journals.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Journal Entries
        </a>

        <a href="{{ route('admin.finance.accounting.ledger.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.finance.accounting.ledger.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.accounting.ledger.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            General Ledger
        </a>

        <a href="{{ route('admin.finance.accounting.fiscal-periods.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.finance.accounting.fiscal-periods.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.accounting.fiscal-periods.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Fiscal Settings
        </a>

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Finance</p>
        </div>

        @can('viewAny', \App\Models\Budget::class)
        <a href="{{ route('admin.finance.budgets.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.finance.budgets.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.budgets.*'),
        ])>
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
            </svg>
            Budgets
        </a>
        @endcan

        @can('viewAny', \App\Models\Invoice::class)
        <a href="{{ route('admin.finance.invoices.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.finance.invoices.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.invoices.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Invoices
        </a>
        @endcan

        @can('viewAny', \App\Models\Payment::class)
        <a href="{{ route('admin.finance.payments.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.finance.payments.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.payments.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Payments
        </a>
        @endcan

        @can('viewAny', \App\Models\CreditNote::class)
        <a href="{{ route('admin.finance.credit-notes.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.finance.credit-notes.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.credit-notes.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Credit Notes
        </a>
        @endcan

        @can('viewAny', \App\Models\Refund::class)
        <a href="{{ route('admin.finance.refunds.index') }}" @class([
            'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
            'bg-sidebar-active text-white' => request()->routeIs('admin.finance.refunds.*'),
            'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.finance.refunds.*'),
        ])>
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/>
            </svg>
            Refunds
        </a>
        @endcan
        {{-- Reports --}}
        @can('report.view')
        <a href="{{ route('admin.reports.index') }}"
           @class([
               'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group',
               'bg-sidebar-active text-white' => request()->routeIs('admin.reports.*'),
               'text-gray-300 hover:bg-sidebar-hover hover:text-white' => !request()->routeIs('admin.reports.*'),
           ])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="ml-3 whitespace-nowrap">Reports</span>
        </a>
        @endcan
    </nav>
</aside>
