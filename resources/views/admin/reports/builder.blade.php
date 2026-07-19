<x-layouts.admin title="Report Builder">
    <div x-data="reportBuilder(@js($template ? $template->type : 'project_summary'), @js($template ? $template->filters : []), @js($template ? $template->layout : []))" class="mb-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $template ? 'Edit Report Template' : 'New Custom Report' }}</h1>
                <p class="mt-1 text-sm text-gray-500">Select data source, filter dynamically, and configure your layout.</p>
            </div>
            <div class="flex space-x-3">
                <button type="button" @click="previewReport" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Preview
                </button>
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200">
                    Cancel
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            <!-- Left Sidebar: Configuration Form -->
            <div class="lg:col-span-1 space-y-6">
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form id="reportForm" action="{{ $template ? route('admin.reports.update', $template) : route('admin.reports.store') }}" method="POST" class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
                    @csrf
                    @if($template) @method('PUT') @endif
                    
                    <!-- Hidden Layout JSON -->
                    <input type="hidden" name="layout" :value="JSON.stringify(layout)">

                    <div class="bg-gray-50 px-5 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Core Settings
                        </h2>
                    </div>

                    <div class="p-5 space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Report Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="{{ old('name', $template->name ?? '') }}" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Description</label>
                            <textarea name="description" rows="2" class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">{{ old('description', $template->description ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Data Source (Module) <span class="text-red-500">*</span></label>
                            <select name="type" x-model="type" @change="resetFilters" required class="mt-1.5 block w-full rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-blue-50 border-blue-300 text-blue-900 transition-colors font-medium">
                                <option value="executive">Executive Dashboard</option>
                                <option value="project_summary">Projects</option>
                                <option value="task_summary">Tasks</option>
                                <option value="time_summary">Time Tracking</option>
                                <option value="meetings">Meetings</option>
                                <option value="workflow">Approvals & Workflows</option>
                                <option value="documents">Documents</option>
                                <option value="discussions">Discussions & Comments</option>
                                <option value="clients">Clients</option>
                                <option value="organizations">Organizations (Companies/Branches)</option>
                                <option value="user_productivity">User Productivity</option>
                                <option value="announcements">Announcements</option>
                                <option value="notifications">Notifications</option>
                                <option disabled>─── CRM ───</option>
                                <option value="crm_pipeline">Pipeline (Opportunities)</option>
                                <option value="crm_leads">Leads</option>
                                <option value="crm_conversions">Conversions</option>
                            </select>
                        </div>

                        <!-- Dynamic Filters Accordion -->
                        <div class="pt-4 border-t border-gray-200 mt-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-3 flex justify-between items-center">
                                Dynamic Filters
                                <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full" x-text="availableFilters.length + ' available'"></span>
                            </h3>

                            <div class="space-y-4 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                                
                                <template x-if="availableFilters.includes('company_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Company</label>
                                        <select x-model="filters.company_id" name="filters[company_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">All Companies</option>
                                            @foreach($options['companies'] as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('branch_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Branch (Cascading)</label>
                                        <select x-model="filters.branch_id" name="filters[branch_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">All Branches</option>
                                            @foreach($options['branches'] as $b)
                                                <option value="{{ $b->id }}" x-show="!filters.company_id || filters.company_id == {{ $b->company_id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('department_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Department</label>
                                        <select x-model="filters.department_id" name="filters[department_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">All Departments</option>
                                            @foreach($options['departments'] as $d)
                                                <option value="{{ $d->id }}" x-show="!filters.branch_id || filters.branch_id == {{ $d->branch_id }}">{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('client_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Client</label>
                                        <select x-model="filters.client_id" name="filters[client_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">All Clients</option>
                                            @foreach($options['clients'] as $cl)
                                                <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('project_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Project</label>
                                        <select x-model="filters.project_id" name="filters[project_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">All Projects</option>
                                            @foreach($options['projects'] as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('user_id')">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">User / Employee</label>
                                        <select x-model="filters.user_id" name="filters[user_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">All Users</option>
                                            @foreach($options['users'] as $u)
                                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('status')">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Status</label>
                                        <select x-model="filters.status" name="filters[status]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">Any Status</option>
                                            <option value="Pending">Pending</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Completed">Completed</option>
                                            <option value="On Hold">On Hold</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                </template>

                                <template x-if="availableFilters.includes('priority')">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Priority</label>
                                        <select x-model="filters.priority" name="filters[priority]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">Any Priority</option>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                            <option value="Critical">Critical</option>
                                        </select>
                                    </div>
                                </template>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Date From</label>
                                        <input type="date" x-model="filters.date_from" name="filters[date_from]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Date To</label>
                                        <input type="date" x-model="filters.date_to" name="filters[date_to]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Column Builder -->
                        <div class="pt-4 border-t border-gray-200 mt-4" x-show="availableColumns.length > 0">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Visible Columns</h3>
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                <template x-for="col in availableColumns" :key="col.field">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" :value="col.field" x-model="layout.columns" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="text-sm text-gray-700" x-text="col.label"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Grouping & Sorting -->
                        <div class="pt-4 border-t border-gray-200 mt-4" x-show="type !== 'executive'">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Grouping & Sorting</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs text-gray-500">Group By</label>
                                    <select x-model="layout.groupBy" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">None</option>
                                        <option value="company_id">Company</option>
                                        <option value="branch_id">Branch</option>
                                        <option value="department_id">Department</option>
                                        <option value="status">Status</option>
                                        <option value="priority">Priority</option>
                                        <option value="month">Month</option>
                                        <option value="pipeline_id">Pipeline (CRM)</option>
                                        <option value="stage_id">Stage (CRM)</option>
                                        <option value="source">Source (CRM)</option>
                                    </select>
                                </div>
                                <div class="flex space-x-2">
                                    <div class="flex-1">
                                        <label class="block text-xs text-gray-500">Sort By</label>
                                        <select x-model="layout.sortBy" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">Default (ID)</option>
                                            <option value="created_at">Created Date</option>
                                            <option value="name">Name</option>
                                            <option value="status">Status</option>
                                        </select>
                                    </div>
                                    <div class="w-1/3">
                                        <label class="block text-xs text-gray-500">Direction</label>
                                        <select x-model="layout.sortDirection" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="asc">Asc</option>
                                            <option value="desc">Desc</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chart Builder -->
                        <div class="pt-4 border-t border-gray-200 mt-4" x-show="type !== 'executive'">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Preferred Chart</h3>
                            <select x-model="layout.chartType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="table">Table Only (No Chart)</option>
                                <option value="bar">Bar Chart</option>
                                <option value="line">Line Chart</option>
                                <option value="pie">Pie Chart</option>
                                <option value="doughnut">Doughnut Chart</option>
                                <option value="area">Area Chart</option>
                            </select>
                        </div>

                        <div class="pt-5 flex justify-end gap-2 border-t mt-6">
                            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ $template ? 'Save Changes' : 'Create Report' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Right Area: Live Preview -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-full flex flex-col relative">
                    
                    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Live Preview
                        </h2>
                        <span class="text-xs text-gray-500" x-show="loading">Generating preview...</span>
                    </div>

                    <div class="flex-1 p-0 relative bg-gray-50" style="min-height: 600px;">
                        
                        <!-- Loading Overlay -->
                        <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
                            <p class="mt-4 text-sm font-medium text-blue-600">Building Report...</p>
                        </div>

                        <!-- Preview Container -->
                        <div id="previewContainer" class="h-full w-full overflow-y-auto" x-html="previewHtml">
                            <div class="h-full flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-sm font-medium text-gray-500">Configure settings and click Preview</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportBuilder', (initialType, initialFilters, initialLayout) => ({
                type: initialType,
                filters: initialFilters || {},
                layout: initialLayout && Object.keys(initialLayout).length ? initialLayout : { 
                    columns: [], 
                    groupBy: '', 
                    sortBy: '', 
                    sortDirection: 'asc', 
                    chartType: 'table' 
                },
                loading: false,
                previewHtml: '',
                
                init() {
                    // Pre-select default columns if none are selected
                    if (this.layout.columns.length === 0 && this.availableColumns.length > 0) {
                        this.layout.columns = this.availableColumns.slice(0, 5).map(c => c.field);
                    }
                    if (initialType) {
                        this.previewReport();
                    }
                },

                resetFilters() {
                    this.filters = {};
                    this.layout.columns = this.availableColumns.slice(0, 5).map(c => c.field);
                    this.previewHtml = '';
                },

                get availableFilters() {
                    const schema = {
                        executive: ['company_id', 'branch_id', 'department_id', 'date_from', 'date_to'],
                        project_summary: ['company_id', 'branch_id', 'client_id', 'status', 'priority', 'date_from', 'date_to'],
                        task_summary: ['project_id', 'user_id', 'status', 'priority', 'date_from', 'date_to'],
                        time_summary: ['user_id', 'project_id', 'date_from', 'date_to'],
                        meetings: ['user_id', 'status', 'date_from', 'date_to'],
                        workflow: ['status', 'company_id', 'date_from', 'date_to'],
                        documents: ['company_id', 'date_from', 'date_to'],
                        discussions: ['user_id', 'date_from', 'date_to'],
                        clients: ['company_id', 'date_from', 'date_to'],
                        organizations: ['company_id'],
                        user_productivity: ['company_id', 'branch_id', 'department_id', 'date_from', 'date_to'],
                        announcements: ['company_id', 'priority', 'date_from', 'date_to'],
                        notifications: ['user_id', 'date_from', 'date_to'],
                        crm_pipeline: ['company_id', 'status', 'date_from', 'date_to'],
                        crm_leads: ['company_id', 'status', 'date_from', 'date_to'],
                        crm_conversions: ['company_id', 'date_from', 'date_to'],
                    };
                    return schema[this.type] || ['date_from', 'date_to'];
                },

                get availableColumns() {
                    const schema = {
                        project_summary: [
                            {field: 'id', label: 'ID'}, {field: 'name', label: 'Name'}, 
                            {field: 'client.name', label: 'Client'}, {field: 'manager.name', label: 'Manager'}, 
                            {field: 'status', label: 'Status'}, {field: 'priority', label: 'Priority'}, 
                            {field: 'progress', label: 'Progress (%)'}, {field: 'created_at', label: 'Created Date'}
                        ],
                        task_summary: [
                            {field: 'id', label: 'ID'}, {field: 'name', label: 'Task Name'}, 
                            {field: 'project.name', label: 'Project'}, {field: 'status', label: 'Status'}, 
                            {field: 'priority', label: 'Priority'}, {field: 'due_date', label: 'Due Date'}
                        ],
                        time_summary: [
                            {field: 'id', label: 'ID'}, {field: 'user.name', label: 'User'}, 
                            {field: 'project.name', label: 'Project'}, {field: 'task.name', label: 'Task'}, 
                            {field: 'duration_minutes', label: 'Duration (mins)'}, {field: 'start_time', label: 'Date'}
                        ],
                        user_productivity: [
                            {field: 'id', label: 'ID'}, {field: 'name', label: 'User Name'},
                            {field: 'assigned_tasks_count', label: 'Tasks Count'}, 
                            {field: 'time_entries_sum_duration_minutes', label: 'Total Time (mins)'}
                        ],
                        crm_pipeline: [
                            {field: 'id', label: 'ID'}, {field: 'name', label: 'Deal Name'}, 
                            {field: 'expected_revenue', label: 'Value'}, {field: 'probability', label: 'Probability (%)'}, 
                            {field: 'status', label: 'Status'}, {field: 'pipeline.name', label: 'Pipeline'}, 
                            {field: 'stage.name', label: 'Stage'}, {field: 'expected_close_date', label: 'Close Date'}
                        ],
                        crm_leads: [
                            {field: 'id', label: 'ID'}, {field: 'first_name', label: 'First Name'}, 
                            {field: 'last_name', label: 'Last Name'}, {field: 'email', label: 'Email'}, 
                            {field: 'status', label: 'Status'}, {field: 'source', label: 'Source'}, 
                            {field: 'expected_value', label: 'Expected Value'}
                        ],
                        crm_conversions: [
                            {field: 'id', label: 'Lead ID'}, {field: 'first_name', label: 'First Name'}, 
                            {field: 'last_name', label: 'Last Name'}, {field: 'converted_at', label: 'Converted Date'}, 
                            {field: 'convertedOpportunity.name', label: 'Opportunity'}, {field: 'convertedOpportunity.expected_revenue', label: 'Revenue'}
                        ]
                        // We will dynamically render remaining columns in the backend if not specified
                    };
                    return schema[this.type] || [
                        {field: 'id', label: 'ID'}, {field: 'name', label: 'Name'}, {field: 'created_at', label: 'Created At'}
                    ];
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
                    .then(res => res.text())
                    .then(html => {
                        this.previewHtml = html;
                        // Execute scripts if there are charts
                        setTimeout(() => {
                            const container = document.getElementById('previewContainer');
                            const scripts = container.getElementsByTagName('script');
                            for (let i = 0; i < scripts.length; i++) {
                                eval(scripts[i].innerText);
                            }
                        }, 100);
                    })
                    .catch(err => {
                        console.error('Preview error:', err);
                        this.previewHtml = '<div class="p-6 text-red-500">Failed to load preview.</div>';
                    })
                    .finally(() => {
                        this.loading = false;
                    });
                }
            }));
        });
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
    </style>
    @endpush
</x-layouts.admin>
