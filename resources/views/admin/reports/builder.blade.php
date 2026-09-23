<x-layouts.admin title="{{ $template ? 'Edit Report Template' : 'Report Studio Builder' }}">
    <div x-data="reportStudio(
        @js($template ? $template->type : 'project_summary'),
        @js($template ? ($template->filters ?? []) : []),
        @js($template ? ($template->layout ?? []) : []),
        @js($template ? $template->name : ''),
        @js($template ? $template->description : '')
    )" 
    @keydown.window.ctrl.enter="previewReport"
    @keydown.window.meta.enter="previewReport"
    class="space-y-6 pb-12">

        <!-- Top Studio Action Bar -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 sm:p-5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                <!-- Left: Title & Module Badge -->
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                                {{ $template ? 'Edit Report: ' . $template->name : 'Report Studio Builder' }}
                            </h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60" x-text="getModuleName(type)"></span>
                            @if($template)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Saved Template</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">Draft</span>
                            @endif
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            Select data source, filter criteria, customize columns, and preview live ERP metrics.
                        </p>
                    </div>
                </div>

                <!-- Right: Studio Controls -->
                <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                    <button type="button" 
                            @click="resetAll" 
                            title="Reset all settings to defaults"
                            class="inline-flex items-center px-3 py-2 text-xs sm:text-sm font-medium rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                        <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset
                    </button>

                    <button type="button" 
                            @click="previewReport" 
                            :disabled="loading"
                            class="inline-flex items-center px-4 py-2 border border-slate-200 text-xs sm:text-sm font-semibold rounded-xl text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition-all focus:ring-2 focus:ring-blue-500/20 disabled:opacity-60">
                        <svg x-show="!loading" class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg x-show="loading" class="animate-spin w-4 h-4 mr-1.5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="loading ? 'Generating...' : 'Run Preview'"></span>
                        <kbd class="hidden sm:inline-block ml-2 px-1.5 py-0.5 text-[10px] font-mono text-slate-400 bg-slate-100 rounded border border-slate-200">Ctrl+Enter</kbd>
                    </button>

                    <button type="button" 
                            @click="submitReportForm" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-xs sm:text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-500/20 transition-all focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        {{ $template ? 'Save Changes' : 'Save Template' }}
                    </button>

                    <a href="{{ route('admin.reports.index') }}" 
                       class="inline-flex items-center px-3 py-2 text-xs sm:text-sm font-medium rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl shadow-sm">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-rose-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-rose-800">Please correct the following errors:</h3>
                        <ul class="list-disc pl-5 mt-1 text-xs sm:text-sm text-rose-700 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Studio Split Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT: Studio Configurator Panel (5 cols) -->
            <div class="lg:col-span-5 space-y-4">
                <form id="reportForm" 
                      action="{{ $template ? route('admin.reports.update', $template) : route('admin.reports.store') }}" 
                      method="POST" 
                      class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
                    @csrf
                    @if($template) @method('PUT') @endif

                    <!-- Hidden JSON Payload for Layout -->
                    <input type="hidden" name="layout" :value="JSON.stringify(layout)">
                    <input type="hidden" name="type" :value="type">

                    <!-- Tab Header Navigation -->
                    <div class="bg-slate-50/80 border-b border-slate-200 px-3 pt-3 flex gap-1 overflow-x-auto custom-scrollbar">
                        <button type="button" 
                                @click="activeTab = 'source'"
                                :class="activeTab === 'source' ? 'bg-white text-blue-600 shadow-sm border-slate-200' : 'text-slate-500 hover:text-slate-900 border-transparent'"
                                class="px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-t-xl border border-b-0 transition-all flex items-center gap-1.5 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6"></path></svg>
                            Source & Info
                        </button>

                        <button type="button" 
                                @click="activeTab = 'filters'"
                                :class="activeTab === 'filters' ? 'bg-white text-blue-600 shadow-sm border-slate-200' : 'text-slate-500 hover:text-slate-900 border-transparent'"
                                class="px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-t-xl border border-b-0 transition-all flex items-center gap-1.5 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Filters
                            <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] font-bold" 
                                  :class="activeFilterCount > 0 ? 'bg-blue-100 text-blue-700' : 'bg-slate-200 text-slate-600'" 
                                  x-text="activeFilterCount"></span>
                        </button>

                        <button type="button" 
                                @click="activeTab = 'columns'"
                                :class="activeTab === 'columns' ? 'bg-white text-blue-600 shadow-sm border-slate-200' : 'text-slate-500 hover:text-slate-900 border-transparent'"
                                class="px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-t-xl border border-b-0 transition-all flex items-center gap-1.5 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                            Columns
                            <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700" x-text="layout.columns.length"></span>
                        </button>

                        <button type="button" 
                                @click="activeTab = 'visuals'"
                                :class="activeTab === 'visuals' ? 'bg-white text-blue-600 shadow-sm border-slate-200' : 'text-slate-500 hover:text-slate-900 border-transparent'"
                                class="px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-t-xl border border-b-0 transition-all flex items-center gap-1.5 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                            Chart & Sort
                        </button>
                    </div>

                    <!-- Tab Content Container -->
                    <div class="p-5 space-y-5 flex-1 min-h-[520px]">

                        <!-- TAB 1: Source & Info -->
                        <div x-show="activeTab === 'source'" class="space-y-5">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                    Report Title <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       x-model="name"
                                       required 
                                       placeholder="e.g. Q3 Active Projects Financial Overview" 
                                       class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 transition-colors">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                    Description & Scope
                                </label>
                                <textarea name="description" 
                                          x-model="description"
                                          rows="2" 
                                          placeholder="Optional context about the report audience, metrics, or schedule..." 
                                          class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 transition-colors"></textarea>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                                        Data Source (Module) <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="text-[11px] text-blue-600 font-medium">Auto-configures columns & filters</span>
                                </div>
                                <select x-model="type" 
                                        @change="onModuleChange" 
                                        class="block w-full rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm bg-blue-50/40 border-blue-200 text-blue-950 font-medium py-2.5 transition-colors">
                                    <optgroup label="💼 Projects & Workflows">
                                        <option value="project_summary">Projects (Health, Budget & Progress)</option>
                                        <option value="task_summary">Tasks & Milestones</option>
                                        <option value="time_summary">Time Tracking & Timesheets</option>
                                        <option value="user_productivity">Staff Productivity & Workload</option>
                                        <option value="approval_summary">Approvals & Workflow Sign-offs</option>
                                        <option value="meetings">Corporate Meetings & Attendance</option>
                                    </optgroup>
                                    <optgroup label="💵 Finance & Accounting">
                                        <option value="invoice_summary">Client Invoices & Billing</option>
                                        <option value="payment_summary">Payments Received & Channels</option>
                                        <option value="aging_report">Accounts Receivable Aging</option>
                                        <option value="revenue_report">Revenue Trends & Collections</option>
                                        <option value="profit_and_loss">Profit & Loss Statement</option>
                                        <option value="balance_sheet">Balance Sheet Statement</option>
                                        <option value="budget_analysis">Project Budget vs. Actual Spend</option>
                                        <option value="expense_analysis">Operating Expenses Analysis</option>
                                    </optgroup>
                                    <optgroup label="🛒 Procurement & Suppliers">
                                        <option value="purchase_orders">Purchase Orders</option>
                                        <option value="purchase_invoices">Supplier Bills & Invoices</option>
                                        <option value="goods_receipts">Goods Receipts (GRN)</option>
                                        <option value="supplier_spend">Supplier Spend Analytics</option>
                                        <option value="supplier_performance">Supplier Performance & Ratings</option>
                                        <option value="outstanding_supplier_payments">Outstanding Supplier Payables</option>
                                        <option value="lead_time_report">Procurement Lead Times</option>
                                    </optgroup>
                                    <optgroup label="📦 Warehouse & Logistics">
                                        <option value="stock_on_hand">Current Stock on Hand</option>
                                        <option value="inventory_valuation">Total Inventory Valuation</option>
                                        <option value="low_stock">Low Stock & Reorder Alerts</option>
                                        <option value="movement_report">Warehouse Stock Movements</option>
                                        <option value="warehouse_utilization">Warehouse Utilization & Capacity</option>
                                        <option value="bin_utilization">Bin Location Occupancy</option>
                                        <option value="cycle_count_report">Cycle Count Audits</option>
                                        <option value="warehouse_productivity">Fulfillment Productivity</option>
                                    </optgroup>
                                    <optgroup label="📈 CRM & Sales">
                                        <option value="crm_pipeline">Deals & Opportunities Pipeline</option>
                                        <option value="crm_leads">Leads & Sources</option>
                                        <option value="crm_conversions">Lead Conversions</option>
                                        <option value="quotation_summary">Client Quotations</option>
                                        <option value="sales_forecast">Sales Projections & Forecast</option>
                                        <option value="clients">Client Directory</option>
                                    </optgroup>
                                    <optgroup label="🏢 Governance & Company">
                                        <option value="organizations">Companies & Branches</option>
                                        <option value="documents">Document Repository</option>
                                        <option value="executive">Executive Summary Dashboard</option>
                                    </optgroup>
                                </select>
                            </div>

                            <!-- Module Quick Card Info -->
                            <div class="rounded-xl p-3.5 bg-slate-50 border border-slate-200/70 text-xs text-slate-600 space-y-1.5">
                                <div class="font-semibold text-slate-800 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Module Details
                                </div>
                                <p x-text="getModuleDescription(type)"></p>
                            </div>
                        </div>

                        <!-- TAB 2: Filters -->
                        <div x-show="activeTab === 'filters'" class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Dynamic Filter Criteria</h3>
                                <button type="button" 
                                        @click="clearFilters" 
                                        class="text-xs text-rose-600 hover:text-rose-800 font-medium">
                                    Clear Filters
                                </button>
                            </div>

                            <!-- Date Range Quick Presets -->
                            <div class="space-y-2">
                                <label class="block text-xs font-semibold text-slate-600">Date Range Presets</label>
                                <div class="grid grid-cols-3 gap-1.5">
                                    <button type="button" @click="applyDatePreset('this_month')" class="px-2 py-1 text-xs font-medium rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-center">This Month</button>
                                    <button type="button" @click="applyDatePreset('this_quarter')" class="px-2 py-1 text-xs font-medium rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-center">This Quarter</button>
                                    <button type="button" @click="applyDatePreset('this_year')" class="px-2 py-1 text-xs font-medium rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-center">This Year</button>
                                    <button type="button" @click="applyDatePreset('last_30_days')" class="px-2 py-1 text-xs font-medium rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-center">Last 30 Days</button>
                                    <button type="button" @click="applyDatePreset('today')" class="px-2 py-1 text-xs font-medium rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-center">Today</button>
                                    <button type="button" @click="applyDatePreset('all_time')" class="px-2 py-1 text-xs font-medium rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-center">All Time</button>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <div>
                                        <label class="block text-[11px] text-slate-500">Date From</label>
                                        <input type="date" x-model="filters.date_from" name="filters[date_from]" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-slate-500">Date To</label>
                                        <input type="date" x-model="filters.date_to" name="filters[date_to]" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Organization Filters -->
                            <div class="space-y-3 pt-3 border-t border-slate-100">
                                <template x-if="availableFilters.includes('company_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700">Company</label>
                                        <select x-model="filters.company_id" name="filters[company_id]" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">All Companies</option>
                                            @foreach($options['companies'] as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('branch_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700">Branch (Cascading)</label>
                                        <select x-model="filters.branch_id" name="filters[branch_id]" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">All Branches</option>
                                            @foreach($options['branches'] as $b)
                                                <option value="{{ $b->id }}" x-show="!filters.company_id || filters.company_id == {{ $b->company_id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('department_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700">Department</label>
                                        <select x-model="filters.department_id" name="filters[department_id]" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">All Departments</option>
                                            @foreach($options['departments'] as $d)
                                                <option value="{{ $d->id }}" x-show="!filters.branch_id || filters.branch_id == {{ $d->branch_id }}">{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>
                            </div>

                            <!-- Entity Filters (Project / Client / Supplier / Warehouse / User) -->
                            <div class="space-y-3 pt-3 border-t border-slate-100">
                                <template x-if="availableFilters.includes('project_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700">Project</label>
                                        <select x-model="filters.project_id" name="filters[project_id]" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">All Projects</option>
                                            @foreach($options['projects'] as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('client_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700">Client / Customer</label>
                                        <select x-model="filters.client_id" name="filters[client_id]" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">All Clients</option>
                                            @foreach($options['clients'] as $cl)
                                                <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('supplier_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700">Supplier / Vendor</label>
                                        <select x-model="filters.supplier_id" name="filters[supplier_id]" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">All Suppliers</option>
                                            @foreach($options['suppliers'] as $s)
                                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('warehouse_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700">Warehouse Facility</label>
                                        <select x-model="filters.warehouse_id" name="filters[warehouse_id]" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">All Warehouses</option>
                                            @foreach($options['warehouses'] as $w)
                                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('user_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700">Staff / Assignee</label>
                                        <select x-model="filters.user_id" name="filters[user_id]" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">All Users</option>
                                            @foreach($options['users'] as $u)
                                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('status')">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700">Status</label>
                                        <select x-model="filters.status" name="filters[status]" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Any Status</option>
                                            <option value="Active">Active</option>
                                            <option value="Pending">Pending</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Completed">Completed</option>
                                            <option value="On Hold">On Hold</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Paid">Paid</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('priority')">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700">Priority</label>
                                        <select x-model="filters.priority" name="filters[priority]" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Any Priority</option>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                            <option value="Critical">Critical</option>
                                        </select>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- TAB 3: Columns -->
                        <div x-show="activeTab === 'columns'" class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Visible Columns</h3>
                                    <p class="text-[11px] text-slate-500">Toggle fields to include in tabular view</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="selectAllColumns" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Select All</button>
                                    <span class="text-slate-300">|</span>
                                    <button type="button" @click="resetColumns" class="text-xs text-slate-500 hover:text-slate-700 font-medium">Default</button>
                                </div>
                            </div>

                            <!-- Column Pills Selector -->
                            <div class="space-y-2 max-h-[380px] overflow-y-auto pr-1 custom-scrollbar">
                                <template x-for="col in availableColumns" :key="col.field">
                                    <div @click="toggleColumn(col.field)"
                                         :class="layout.columns.includes(col.field) ? 'border-blue-500 bg-blue-50/50 text-blue-900 ring-1 ring-blue-500/20' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
                                         class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all">
                                        <div class="flex items-center gap-2.5">
                                            <div :class="layout.columns.includes(col.field) ? 'bg-blue-600 text-white' : 'border border-slate-300 bg-white'"
                                                 class="w-4 h-4 rounded flex items-center justify-center transition-colors">
                                                <svg x-show="layout.columns.includes(col.field)" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span class="text-xs font-medium" x-text="col.label"></span>
                                        </div>
                                        <span class="text-[10px] font-mono text-slate-400" x-text="col.field"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- TAB 4: Visuals & Sort -->
                        <div x-show="activeTab === 'visuals'" class="space-y-5">
                            
                            <!-- Visual Chart Selector Cards -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Preferred Visualization</label>
                                <div class="grid grid-cols-2 gap-2">
                                    
                                    <div @click="setChartType('table')" 
                                         :class="layout.chartType === 'table' ? 'border-blue-600 bg-blue-50/40 ring-1 ring-blue-600/30' : 'border-slate-200 hover:bg-slate-50'"
                                         class="p-3 rounded-xl border cursor-pointer transition-all flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-slate-900">Table Only</div>
                                            <div class="text-[10px] text-slate-500">Tabular records</div>
                                        </div>
                                    </div>

                                    <div @click="setChartType('bar')" 
                                         :class="layout.chartType === 'bar' ? 'border-blue-600 bg-blue-50/40 ring-1 ring-blue-600/30' : 'border-slate-200 hover:bg-slate-50'"
                                         class="p-3 rounded-xl border cursor-pointer transition-all flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-slate-900">Bar Chart</div>
                                            <div class="text-[10px] text-slate-500">Comparative metrics</div>
                                        </div>
                                    </div>

                                    <div @click="setChartType('line')" 
                                         :class="layout.chartType === 'line' ? 'border-blue-600 bg-blue-50/40 ring-1 ring-blue-600/30' : 'border-slate-200 hover:bg-slate-50'"
                                         class="p-3 rounded-xl border cursor-pointer transition-all flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-slate-900">Line Chart</div>
                                            <div class="text-[10px] text-slate-500">Trends over time</div>
                                        </div>
                                    </div>

                                    <div @click="setChartType('doughnut')" 
                                         :class="layout.chartType === 'doughnut' ? 'border-blue-600 bg-blue-50/40 ring-1 ring-blue-600/30' : 'border-slate-200 hover:bg-slate-50'"
                                         class="p-3 rounded-xl border cursor-pointer transition-all flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-slate-900">Doughnut</div>
                                            <div class="text-[10px] text-slate-500">Distribution ratios</div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Grouping & Ordering -->
                            <div class="space-y-3 pt-3 border-t border-slate-100">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700">Group By Dimension</label>
                                    <select x-model="layout.groupBy" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">None (Flat List)</option>
                                        <option value="company_id">Company</option>
                                        <option value="branch_id">Branch</option>
                                        <option value="department_id">Department</option>
                                        <option value="status">Status</option>
                                        <option value="priority">Priority</option>
                                        <option value="month">Month / Period</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700">Sort Field</label>
                                        <select x-model="layout.sortBy" @change="maybeAutoPreview" class="mt-1 block w-full rounded-xl border-slate-200 text-xs py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Default (ID)</option>
                                            <option value="created_at">Created Date</option>
                                            <option value="name">Name / Title</option>
                                            <option value="status">Status</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700">Order</label>
                                        <div class="mt-1 grid grid-cols-2 gap-1">
                                            <button type="button" 
                                                    @click="layout.sortDirection = 'asc'; maybeAutoPreview()" 
                                                    :class="layout.sortDirection === 'asc' ? 'bg-blue-600 text-white font-semibold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                                    class="py-2 text-xs rounded-lg transition-colors text-center">
                                                Asc &uarr;
                                            </button>
                                            <button type="button" 
                                                    @click="layout.sortDirection = 'desc'; maybeAutoPreview()" 
                                                    :class="layout.sortDirection === 'desc' ? 'bg-blue-600 text-white font-semibold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                                    class="py-2 text-xs rounded-lg transition-colors text-center">
                                                Desc &darr;
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Bottom Configurator Bar -->
                    <div class="bg-slate-50/90 border-t border-slate-200 p-4 flex items-center justify-between">
                        <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Ready to build</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="previewReport" 
                                    class="px-3 py-1.5 text-xs font-semibold rounded-xl text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                                Preview Now
                            </button>
                            <button type="submit" 
                                    class="px-4 py-1.5 text-xs font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors">
                                {{ $template ? 'Save Changes' : 'Save Report' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- RIGHT: Interactive Live Canvas (7 cols) -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col h-full relative min-h-[680px]">
                    
                    <!-- Canvas Toolbar -->
                    <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-blue-600"></div>
                            <h2 class="text-sm font-bold text-slate-900">Live Preview Canvas</h2>
                            <span x-show="lastUpdated" class="text-[11px] text-slate-400 font-mono" x-text="'Updated ' + lastUpdated"></span>
                        </div>

                        <!-- Canvas Controls -->
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-1.5 cursor-pointer text-xs text-slate-600 select-none">
                                <input type="checkbox" x-model="autoPreview" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 h-3.5 w-3.5">
                                <span>Auto-refresh</span>
                            </label>
                            <button type="button" 
                                    @click="previewReport" 
                                    class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Reload Preview">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Filter Tags Bar (showing applied filters) -->
                    <div x-show="activeFilterChips.length > 0" class="px-5 py-2.5 bg-blue-50/30 border-b border-blue-100 flex items-center gap-2 flex-wrap text-xs">
                        <span class="text-slate-500 text-[11px] font-medium">Applied:</span>
                        <template x-for="chip in activeFilterChips" :key="chip.key">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-white border border-blue-200 text-blue-800 shadow-2xs">
                                <span x-text="chip.label + ': ' + chip.value"></span>
                                <button type="button" @click="removeFilter(chip.key)" class="hover:text-rose-600 ml-0.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </span>
                        </template>
                        <button type="button" @click="clearFilters" class="text-[11px] text-blue-600 hover:underline ml-auto font-medium">Clear All</button>
                    </div>

                    <!-- Canvas Main Area -->
                    <div class="flex-1 p-5 relative bg-slate-50/40">
                        
                        <!-- Loading Overlay -->
                        <div x-show="loading" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute inset-0 bg-white/70 backdrop-blur-xs z-20 flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 text-white animate-pulse">
                                <svg class="animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </div>
                            <p class="mt-3 text-xs font-semibold text-slate-800">Processing ERP records...</p>
                            <span class="text-[11px] text-slate-500">Aggregating KPIs and building visual data</span>
                        </div>

                        <!-- Rendered HTML Container -->
                        <div id="previewContainer" class="h-full w-full" x-html="previewHtml"></div>

                        <!-- Empty State (when previewHtml is blank) -->
                        <div x-show="!previewHtml && !loading" class="h-full flex flex-col items-center justify-center py-16 px-4 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-50 to-indigo-50 border border-blue-200/50 flex items-center justify-center text-blue-600 mb-4 shadow-sm">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Ready to Generate Report Preview</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm">Configure your module, criteria, and metrics on the left, then click Run Preview to generate live ERP analysis.</p>

                            <!-- Quick Starter Templates -->
                            <div class="mt-6 flex flex-wrap items-center justify-center gap-2 max-w-lg">
                                <button type="button" @click="loadPresetTemplate('project_summary')" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-blue-50 hover:border-blue-200 text-xs text-slate-700 font-medium transition-colors shadow-2xs">
                                    🚀 Projects Health & Progress
                                </button>
                                <button type="button" @click="loadPresetTemplate('invoice_summary')" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-blue-50 hover:border-blue-200 text-xs text-slate-700 font-medium transition-colors shadow-2xs">
                                    💰 Invoicing & Receivables
                                </button>
                                <button type="button" @click="loadPresetTemplate('stock_on_hand')" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-blue-50 hover:border-blue-200 text-xs text-slate-700 font-medium transition-colors shadow-2xs">
                                    📦 Warehouse Stock on Hand
                                </button>
                                <button type="button" @click="loadPresetTemplate('purchase_orders')" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-blue-50 hover:border-blue-200 text-xs text-slate-700 font-medium transition-colors shadow-2xs">
                                    🛒 Purchase Orders & Spend
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportStudio', (initialType, initialFilters, initialLayout, initialName, initialDesc) => ({
                type: initialType || 'project_summary',
                name: initialName || '',
                description: initialDesc || '',
                activeTab: 'source',
                filters: initialFilters && typeof initialFilters === 'object' ? initialFilters : {},
                layout: initialLayout && Object.keys(initialLayout).length ? initialLayout : {
                    columns: [],
                    groupBy: '',
                    sortBy: '',
                    sortDirection: 'asc',
                    chartType: 'table'
                },
                autoPreview: true,
                loading: false,
                previewHtml: '',
                lastUpdated: '',
                activeCharts: [],

                init() {
                    if (!this.name) {
                        this.name = this.generateDefaultName();
                    }
                    if (this.layout.columns.length === 0) {
                        this.resetColumns();
                    }
                    // Auto-load initial preview
                    this.$nextTick(() => {
                        this.previewReport();
                    });
                },

                generateDefaultName() {
                    const month = new Date().toLocaleString('default', { month: 'short' });
                    const year = new Date().getFullYear();
                    return `${this.getModuleName(this.type)} (${month} ${year})`;
                },

                get activeFilterCount() {
                    let count = 0;
                    for (const [key, val] of Object.entries(this.filters)) {
                        if (val !== '' && val !== null && val !== undefined) count++;
                    }
                    return count;
                },

                get activeFilterChips() {
                    const chips = [];
                    for (const [key, val] of Object.entries(this.filters)) {
                        if (val !== '' && val !== null && val !== undefined) {
                            let label = key.replace('_id', '').replace('_', ' ').toUpperCase();
                            chips.push({ key, label, value: val });
                        }
                    }
                    return chips;
                },

                removeFilter(key) {
                    delete this.filters[key];
                    this.filters = { ...this.filters };
                    this.maybeAutoPreview();
                },

                clearFilters() {
                    this.filters = {};
                    this.maybeAutoPreview();
                },

                resetAll() {
                    this.filters = {};
                    this.layout = {
                        columns: [],
                        groupBy: '',
                        sortBy: '',
                        sortDirection: 'asc',
                        chartType: 'table'
                    };
                    this.resetColumns();
                    this.previewReport();
                },

                onModuleChange() {
                    if (!this.name || this.name.includes('Report') || this.name.includes('Overview') || this.name.includes('Summary')) {
                        this.name = this.generateDefaultName();
                    }
                    this.filters = {};
                    this.resetColumns();
                    this.previewReport();
                },

                setChartType(chart) {
                    this.layout.chartType = chart;
                    this.maybeAutoPreview();
                },

                applyDatePreset(preset) {
                    const now = new Date();
                    const formatDate = (d) => d.toISOString().split('T')[0];

                    if (preset === 'today') {
                        const todayStr = formatDate(now);
                        this.filters.date_from = todayStr;
                        this.filters.date_to = todayStr;
                    } else if (preset === 'this_month') {
                        const start = new Date(now.getFullYear(), now.getMonth(), 1);
                        this.filters.date_from = formatDate(start);
                        this.filters.date_to = formatDate(now);
                    } else if (preset === 'this_quarter') {
                        const quarterStartMonth = Math.floor(now.getMonth() / 3) * 3;
                        const start = new Date(now.getFullYear(), quarterStartMonth, 1);
                        this.filters.date_from = formatDate(start);
                        this.filters.date_to = formatDate(now);
                    } else if (preset === 'this_year') {
                        const start = new Date(now.getFullYear(), 0, 1);
                        this.filters.date_from = formatDate(start);
                        this.filters.date_to = formatDate(now);
                    } else if (preset === 'last_30_days') {
                        const start = new Date();
                        start.setDate(start.getDate() - 30);
                        this.filters.date_from = formatDate(start);
                        this.filters.date_to = formatDate(now);
                    } else if (preset === 'all_time') {
                        delete this.filters.date_from;
                        delete this.filters.date_to;
                    }
                    this.filters = { ...this.filters };
                    this.maybeAutoPreview();
                },

                loadPresetTemplate(moduleType) {
                    this.type = moduleType;
                    this.onModuleChange();
                },

                selectAllColumns() {
                    this.layout.columns = this.availableColumns.map(c => c.field);
                    this.maybeAutoPreview();
                },

                resetColumns() {
                    const cols = this.availableColumns;
                    this.layout.columns = cols.slice(0, 6).map(c => c.field);
                    this.maybeAutoPreview();
                },

                toggleColumn(field) {
                    if (this.layout.columns.includes(field)) {
                        this.layout.columns = this.layout.columns.filter(c => c !== field);
                    } else {
                        this.layout.columns.push(field);
                    }
                    this.maybeAutoPreview();
                },

                maybeAutoPreview() {
                    if (this.autoPreview) {
                        this.previewReport();
                    }
                },

                submitReportForm() {
                    const form = document.getElementById('reportForm');
                    if (form) form.submit();
                },

                previewReport() {
                    this.loading = true;

                    fetch('{{ route('admin.reports.preview') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            type: this.type,
                            filters: this.filters,
                            layout: this.layout
                        })
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Preview request failed with status ' + res.status);
                        return res.text();
                    })
                    .then(html => {
                        this.previewHtml = html;
                        const now = new Date();
                        this.lastUpdated = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                        this.$nextTick(() => {
                            this.initCanvases();
                        });
                    })
                    .catch(err => {
                        console.error('Preview error:', err);
                        this.previewHtml = `
                            <div class="p-8 text-center bg-rose-50 rounded-2xl border border-rose-200">
                                <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <h4 class="text-sm font-bold text-rose-900">Failed to render live preview</h4>
                                <p class="text-xs text-rose-700 mt-1">${err.message || 'Please verify report filters and layout.'}</p>
                            </div>
                        `;
                    })
                    .finally(() => {
                        this.loading = false;
                    });
                },

                initCanvases() {
                    // Destroy previous charts to avoid canvas reuse error
                    if (this.activeCharts && this.activeCharts.length) {
                        this.activeCharts.forEach(c => {
                            try { c.destroy(); } catch (e) {}
                        });
                        this.activeCharts = [];
                    }

                    if (typeof Chart === 'undefined') return;

                    const container = document.getElementById('previewContainer');
                    if (!container) return;

                    const canvases = container.querySelectorAll('canvas[data-chart-widget]');
                    canvases.forEach(canvas => {
                        const type = canvas.dataset.chartType || 'bar';
                        let labels = [];
                        let values = [];
                        try { labels = JSON.parse(canvas.dataset.chartLabels || '[]'); } catch (e) {}
                        try { values = JSON.parse(canvas.dataset.chartValues || '[]'); } catch (e) {}

                        const colors = ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#6366f1'];

                        const chart = new Chart(canvas.getContext('2d'), {
                            type: type === 'area' ? 'line' : type,
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Volume',
                                    data: values,
                                    backgroundColor: (type === 'pie' || type === 'doughnut') ? colors : 'rgba(37, 99, 235, 0.75)',
                                    borderColor: '#2563eb',
                                    borderWidth: 1.5,
                                    fill: type === 'area',
                                    tension: 0.3
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: (type === 'pie' || type === 'doughnut'),
                                        position: 'bottom'
                                    }
                                }
                            }
                        });
                        this.activeCharts.push(chart);
                    });
                },

                getModuleName(mod) {
                    const names = {
                        project_summary: 'Projects Health',
                        task_summary: 'Tasks & Milestones',
                        time_summary: 'Time Tracking',
                        user_productivity: 'Staff Productivity',
                        approval_summary: 'Workflows & Approvals',
                        meetings: 'Corporate Meetings',
                        invoice_summary: 'Invoices & Billing',
                        payment_summary: 'Payments Received',
                        aging_report: 'Receivables Aging',
                        revenue_report: 'Revenue Trends',
                        profit_and_loss: 'Profit & Loss',
                        balance_sheet: 'Balance Sheet',
                        budget_analysis: 'Budget vs Spend',
                        expense_analysis: 'Operating Expenses',
                        purchase_orders: 'Purchase Orders',
                        purchase_invoices: 'Supplier Bills',
                        goods_receipts: 'Goods Receipts',
                        supplier_spend: 'Supplier Spend',
                        supplier_performance: 'Supplier Ratings',
                        outstanding_supplier_payments: 'Supplier Payables',
                        lead_time_report: 'Procurement Lead Time',
                        stock_on_hand: 'Stock on Hand',
                        inventory_valuation: 'Inventory Valuation',
                        low_stock: 'Low Stock Alerts',
                        movement_report: 'Stock Movements',
                        warehouse_utilization: 'Warehouse Capacity',
                        bin_utilization: 'Bin Location Occupancy',
                        cycle_count_report: 'Cycle Count Audits',
                        warehouse_productivity: 'Fulfillment Productivity',
                        crm_pipeline: 'Deals Pipeline',
                        crm_leads: 'Leads & Opportunities',
                        crm_conversions: 'Lead Conversions',
                        quotation_summary: 'Sales Quotations',
                        sales_forecast: 'Sales Forecast',
                        clients: 'Client Directory',
                        organizations: 'Organizations',
                        documents: 'Documents Repository',
                        executive: 'Executive Dashboard'
                    };
                    return names[mod] || mod;
                },

                getModuleDescription(mod) {
                    const desc = {
                        project_summary: 'Analyze project progression, milestones, customer relations, and financial progress across branches.',
                        task_summary: 'Monitor tasks by project, assignee status, deadlines, and urgency level.',
                        time_summary: 'Inspect billable hours, staff time allocations, and project timesheets.',
                        invoice_summary: 'Review client invoices, balances due, payment status, and total revenue billed in RWF.',
                        payment_summary: 'Track collections, payment transaction channels, dates, and amounts.',
                        purchase_orders: 'Track vendor purchase requisitions, PO fulfillment, and total procurement commitments.',
                        stock_on_hand: 'Audit active physical inventory quantities, unit values, and warehouse locations.',
                        inventory_valuation: 'Aggregate monetary valuation of company assets stored across warehousing sites.'
                    };
                    return desc[mod] || 'Extract structured analytics and tabular logs from this business domain.';
                },

                get availableFilters() {
                    const schema = {
                        executive: ['company_id', 'branch_id', 'department_id', 'date_from', 'date_to'],
                        project_summary: ['company_id', 'branch_id', 'client_id', 'status', 'priority', 'date_from', 'date_to'],
                        task_summary: ['project_id', 'user_id', 'status', 'priority', 'date_from', 'date_to'],
                        time_summary: ['user_id', 'project_id', 'date_from', 'date_to'],
                        user_productivity: ['company_id', 'branch_id', 'department_id', 'date_from', 'date_to'],
                        approval_summary: ['company_id', 'status', 'date_from', 'date_to'],
                        meetings: ['user_id', 'status', 'date_from', 'date_to'],
                        invoice_summary: ['company_id', 'client_id', 'status', 'date_from', 'date_to'],
                        payment_summary: ['company_id', 'status', 'date_from', 'date_to'],
                        aging_report: ['company_id', 'client_id', 'date_from', 'date_to'],
                        revenue_report: ['company_id', 'date_from', 'date_to'],
                        profit_and_loss: ['company_id', 'date_from', 'date_to'],
                        balance_sheet: ['company_id', 'date_from', 'date_to'],
                        budget_analysis: ['company_id', 'project_id', 'date_from', 'date_to'],
                        expense_analysis: ['company_id', 'department_id', 'date_from', 'date_to'],
                        purchase_orders: ['company_id', 'supplier_id', 'status', 'date_from', 'date_to'],
                        purchase_invoices: ['company_id', 'supplier_id', 'status', 'date_from', 'date_to'],
                        goods_receipts: ['company_id', 'supplier_id', 'date_from', 'date_to'],
                        supplier_spend: ['company_id', 'supplier_id', 'date_from', 'date_to'],
                        supplier_performance: ['company_id', 'supplier_id'],
                        outstanding_supplier_payments: ['company_id', 'supplier_id'],
                        lead_time_report: ['company_id', 'supplier_id'],
                        stock_on_hand: ['company_id', 'warehouse_id'],
                        inventory_valuation: ['company_id', 'warehouse_id'],
                        low_stock: ['company_id', 'warehouse_id'],
                        movement_report: ['company_id', 'date_from', 'date_to'],
                        warehouse_utilization: ['company_id'],
                        bin_utilization: ['company_id', 'warehouse_id'],
                        cycle_count_report: ['company_id', 'date_from', 'date_to'],
                        warehouse_productivity: ['company_id', 'date_from', 'date_to'],
                        crm_pipeline: ['company_id', 'status', 'date_from', 'date_to'],
                        crm_leads: ['company_id', 'status', 'date_from', 'date_to'],
                        crm_conversions: ['company_id', 'date_from', 'date_to'],
                        quotation_summary: ['company_id', 'status', 'date_from', 'date_to'],
                        sales_forecast: ['company_id', 'status', 'date_from', 'date_to'],
                        clients: ['company_id', 'date_from', 'date_to'],
                        organizations: ['company_id'],
                        documents: ['company_id', 'date_from', 'date_to']
                    };
                    return schema[this.type] || ['company_id', 'date_from', 'date_to'];
                },

                get availableColumns() {
                    const schema = {
                        project_summary: [
                            { field: 'id', label: 'ID' },
                            { field: 'name', label: 'Project Name' },
                            { field: 'client.name', label: 'Client' },
                            { field: 'manager.name', label: 'Project Manager' },
                            { field: 'status', label: 'Status' },
                            { field: 'priority', label: 'Priority' },
                            { field: 'progress', label: 'Progress (%)' },
                            { field: 'created_at', label: 'Created Date' }
                        ],
                        task_summary: [
                            { field: 'id', label: 'ID' },
                            { field: 'name', label: 'Task Name' },
                            { field: 'project.name', label: 'Project' },
                            { field: 'status', label: 'Status' },
                            { field: 'priority', label: 'Priority' },
                            { field: 'due_date', label: 'Due Date' }
                        ],
                        time_summary: [
                            { field: 'id', label: 'ID' },
                            { field: 'user.name', label: 'Staff Member' },
                            { field: 'project.name', label: 'Project' },
                            { field: 'task.name', label: 'Task' },
                            { field: 'duration_minutes', label: 'Duration (Mins)' },
                            { field: 'start_time', label: 'Timestamp' }
                        ],
                        invoice_summary: [
                            { field: 'id', label: 'ID' },
                            { field: 'invoice_number', label: 'Invoice #' },
                            { field: 'client.name', label: 'Client' },
                            { field: 'company.name', label: 'Company' },
                            { field: 'status', label: 'Status' },
                            { field: 'total_amount', label: 'Total Amount (RWF)' },
                            { field: 'balance_due', label: 'Balance Due' },
                            { field: 'issue_date', label: 'Issue Date' },
                            { field: 'due_date', label: 'Due Date' }
                        ],
                        payment_summary: [
                            { field: 'id', label: 'ID' },
                            { field: 'payment_number', label: 'Payment #' },
                            { field: 'company.name', label: 'Company' },
                            { field: 'paymentMethod.name', label: 'Payment Channel' },
                            { field: 'amount', label: 'Amount (RWF)' },
                            { field: 'payment_date', label: 'Payment Date' },
                            { field: 'status', label: 'Status' }
                        ],
                        aging_report: [
                            { field: 'id', label: 'ID' },
                            { field: 'client.name', label: 'Client' },
                            { field: 'total_outstanding', label: 'Total Outstanding' },
                            { field: 'current_due', label: 'Current (0-30d)' },
                            { field: 'days_30', label: '31-60 Days' },
                            { field: 'days_60', label: '61-90 Days' },
                            { field: 'days_90_plus', label: '90+ Days Overdue' }
                        ],
                        revenue_report: [
                            { field: 'period', label: 'Period' },
                            { field: 'total_invoiced', label: 'Total Invoiced' },
                            { field: 'total_collected', label: 'Total Collected' },
                            { field: 'outstanding', label: 'Outstanding Balance' }
                        ],
                        budget_analysis: [
                            { field: 'id', label: 'ID' },
                            { field: 'project.name', label: 'Project' },
                            { field: 'planned_budget', label: 'Budget Allocation' },
                            { field: 'actual_spend', label: 'Actual Spend' },
                            { field: 'variance', label: 'Variance (RWF)' },
                            { field: 'variance_percentage', label: 'Variance %' }
                        ],
                        purchase_orders: [
                            { field: 'id', label: 'ID' },
                            { field: 'po_number', label: 'PO Number' },
                            { field: 'supplier.name', label: 'Supplier' },
                            { field: 'total_amount', label: 'Total Amount' },
                            { field: 'status', label: 'Status' },
                            { field: 'issue_date', label: 'Order Date' }
                        ],
                        stock_on_hand: [
                            { field: 'id', label: 'ID' },
                            { field: 'item_code', label: 'Item Code' },
                            { field: 'name', label: 'Item Name' },
                            { field: 'warehouse.name', label: 'Warehouse' },
                            { field: 'quantity_on_hand', label: 'Quantity' },
                            { field: 'unit_cost', label: 'Unit Cost' },
                            { field: 'total_value', label: 'Valuation' }
                        ],
                        inventory_valuation: [
                            { field: 'id', label: 'ID' },
                            { field: 'warehouse.name', label: 'Warehouse' },
                            { field: 'total_items', label: 'SKU Count' },
                            { field: 'total_valuation', label: 'Total Value' }
                        ],
                        crm_pipeline: [
                            { field: 'id', label: 'ID' },
                            { field: 'name', label: 'Deal Name' },
                            { field: 'expected_revenue', label: 'Deal Value' },
                            { field: 'probability', label: 'Probability (%)' },
                            { field: 'status', label: 'Status' },
                            { field: 'stage.name', label: 'Pipeline Stage' },
                            { field: 'expected_close_date', label: 'Expected Close' }
                        ],
                        crm_leads: [
                            { field: 'id', label: 'ID' },
                            { field: 'first_name', label: 'First Name' },
                            { field: 'last_name', label: 'Last Name' },
                            { field: 'email', label: 'Email' },
                            { field: 'status', label: 'Status' },
                            { field: 'source', label: 'Source' },
                            { field: 'expected_value', label: 'Expected Value' }
                        ],
                        quotation_summary: [
                            { field: 'id', label: 'ID' },
                            { field: 'quotation_number', label: 'Quotation #' },
                            { field: 'account.name', label: 'Client Account' },
                            { field: 'grand_total', label: 'Grand Total' },
                            { field: 'status.name', label: 'Status' },
                            { field: 'created_at', label: 'Quotation Date' }
                        ],
                        approval_summary: [
                            { field: 'id', label: 'ID' },
                            { field: 'workflow.name', label: 'Workflow' },
                            { field: 'requester.name', label: 'Requester' },
                            { field: 'approver.name', label: 'Approver' },
                            { field: 'status', label: 'Status' },
                            { field: 'created_at', label: 'Submission Date' }
                        ],
                        user_productivity: [
                            { field: 'id', label: 'ID' },
                            { field: 'name', label: 'Staff Name' },
                            { field: 'assigned_tasks_count', label: 'Tasks Count' },
                            { field: 'time_entries_sum_duration_minutes', label: 'Logged Time (Mins)' }
                        ]
                    };

                    return schema[this.type] || [
                        { field: 'id', label: 'ID' },
                        { field: 'name', label: 'Name' },
                        { field: 'created_at', label: 'Created Date' }
                    ];
                }
            }));
        });
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @endpush
</x-layouts.admin>
