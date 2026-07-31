@props([
    'action' => '',
    'fiscalYears' => [],
    'fiscalYearId' => null,
    'branches' => [],
    'departments' => [],
    'projects' => [],
    'clients' => [],
    'filters' => []
])

<div class="mb-6 print:hidden bg-white rounded-xl shadow-sm border border-gray-100 overflow-visible">
    <form method="GET" action="{{ $action }}" id="financial-filters" x-data @submit="window.dispatchEvent(new CustomEvent('filters-loading')); Alpine.store('loading').start()" class="p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5">
            
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                
                {{-- Fiscal Year --}}
                <div class="relative">
                    <label for="fiscal_year_id" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Fiscal Year</label>
                    <select name="fiscal_year_id" id="fiscal_year_id" class="block w-full rounded-lg border-gray-200 bg-gray-50/50 py-2 pl-3 pr-10 text-sm font-medium text-gray-900 focus:border-blue-500 focus:ring-blue-500 focus:bg-white transition-colors cursor-pointer" onchange="this.form.submit()">
                        <option value="">All Time</option>
                        @foreach($fiscalYears as $year)
                            <option value="{{ $year->id }}" {{ $fiscalYearId == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Currency --}}
                <div class="relative">
                    <label for="currency_code" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Currency</label>
                    <select name="currency_code" id="currency_code" class="block w-full rounded-lg border-gray-200 bg-gray-50/50 py-2 pl-3 pr-10 text-sm font-medium text-gray-900 focus:border-blue-500 focus:ring-blue-500 focus:bg-white transition-colors cursor-pointer" onchange="this.form.submit()">
                        <option value="">All Currencies</option>
                        <option value="USD" {{ ($filters['currency_code'] ?? '') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ ($filters['currency_code'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="GBP" {{ ($filters['currency_code'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP</option>
                        <option value="RWF" {{ ($filters['currency_code'] ?? '') == 'RWF' ? 'selected' : '' }}>RWF</option>
                    </select>
                </div>

                {{-- Branch --}}
                @if(count($branches) > 0)
                <div class="relative">
                    <label for="branch_id" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Branch</label>
                    <select name="branch_id" id="branch_id" class="block w-full rounded-lg border-gray-200 bg-gray-50/50 py-2 pl-3 pr-10 text-sm font-medium text-gray-900 focus:border-blue-500 focus:ring-blue-500 focus:bg-white transition-colors cursor-pointer" onchange="this.form.submit()">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ ($filters['branch_id'] ?? '') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Department --}}
                @if(count($departments) > 0)
                <div class="relative">
                    <label for="department_id" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Department</label>
                    <select name="department_id" id="department_id" class="block w-full rounded-lg border-gray-200 bg-gray-50/50 py-2 pl-3 pr-10 text-sm font-medium text-gray-900 focus:border-blue-500 focus:ring-blue-500 focus:bg-white transition-colors cursor-pointer" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ ($filters['department_id'] ?? '') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Project --}}
                @if(count($projects) > 0)
                <div class="relative">
                    <label for="project_id" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Project</label>
                    <select name="project_id" id="project_id" class="block w-full rounded-lg border-gray-200 bg-gray-50/50 py-2 pl-3 pr-10 text-sm font-medium text-gray-900 focus:border-blue-500 focus:ring-blue-500 focus:bg-white transition-colors cursor-pointer" onchange="this.form.submit()">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ ($filters['project_id'] ?? '') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Client / Customer --}}
                @if(count($clients) > 0)
                <div class="relative">
                    <label for="client_id" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Customer</label>
                    <select name="client_id" id="client_id" class="block w-full rounded-lg border-gray-200 bg-gray-50/50 py-2 pl-3 pr-10 text-sm font-medium text-gray-900 focus:border-blue-500 focus:ring-blue-500 focus:bg-white transition-colors cursor-pointer" onchange="this.form.submit()">
                        <option value="">All Customers</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ ($filters['client_id'] ?? '') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Start Date --}}
                <div class="relative">
                    <label for="start_date" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Start Date</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="block w-full rounded-lg border-gray-200 bg-gray-50/50 py-2 pl-9 pr-3 text-sm font-medium text-gray-900 focus:border-blue-500 focus:ring-blue-500 focus:bg-white transition-colors" onchange="this.form.submit()">
                    </div>
                </div>

                {{-- End Date --}}
                <div class="relative">
                    <label for="end_date" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">End Date</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="block w-full rounded-lg border-gray-200 bg-gray-50/50 py-2 pl-9 pr-3 text-sm font-medium text-gray-900 focus:border-blue-500 focus:ring-blue-500 focus:bg-white transition-colors" onchange="this.form.submit()">
                    </div>
                </div>

            </div>
            
            <div class="flex items-center gap-3 pt-2 lg:pt-0 shrink-0">
                <a href="{{ $action }}" @click="Alpine.store('loading').start(); window.dispatchEvent(new CustomEvent('filters-loading'))" class="text-sm text-gray-500 hover:text-gray-900 font-medium px-2 py-2 transition-colors">Clear Filters</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors">
                    Apply Filters
                </button>
            </div>
            
        </div>
    </form>
</div>
