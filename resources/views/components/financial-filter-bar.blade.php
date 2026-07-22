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

<x-card class="mb-6 print:hidden bg-white shadow-sm border border-gray-200">
    <form method="GET" action="{{ $action }}" class="p-2">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            
            {{-- Fiscal Year --}}
            <div>
                <label for="fiscal_year_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Fiscal Year</label>
                <select name="fiscal_year_id" id="fiscal_year_id" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                    <option value="">All Time</option>
                    @foreach($fiscalYears as $year)
                        <option value="{{ $year->id }}" {{ $fiscalYearId == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Branch --}}
            @if(count($branches) > 0)
            <div>
                <label for="branch_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Branch</label>
                <select name="branch_id" id="branch_id" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
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
            <div>
                <label for="department_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Department</label>
                <select name="department_id" id="department_id" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
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
            <div>
                <label for="project_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Project</label>
                <select name="project_id" id="project_id" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
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
            <div>
                <label for="client_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Customer</label>
                <select name="client_id" id="client_id" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                    <option value="">All Customers</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ ($filters['client_id'] ?? '') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Currency --}}
            <div>
                <label for="currency_code" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Currency</label>
                <select name="currency_code" id="currency_code" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                    <option value="">All Currencies</option>
                    <option value="USD" {{ ($filters['currency_code'] ?? '') == 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ ($filters['currency_code'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR</option>
                    <option value="GBP" {{ ($filters['currency_code'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP</option>
                    <option value="RWF" {{ ($filters['currency_code'] ?? '') == 'RWF' ? 'selected' : '' }}>RWF</option>
                </select>
            </div>

            {{-- Date Range (Optional for some reports) --}}
            <div>
                <label for="start_date" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="block w-full rounded-md border-gray-300 py-2 pl-3 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
            </div>
            <div>
                <label for="end_date" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="block w-full rounded-md border-gray-300 py-2 pl-3 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
            </div>

        </div>
        
        <div class="mt-4 flex justify-end">
            <a href="{{ $action }}" class="text-sm text-gray-500 hover:text-gray-700 mr-4 self-center font-medium">Clear Filters</a>
            <x-button type="primary" type="submit">
                Apply Filters
            </x-button>
        </div>
    </form>
</x-card>
