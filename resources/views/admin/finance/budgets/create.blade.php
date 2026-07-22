<x-layouts.admin title="Create Budget">
 <x-slot:breadcrumbs>
 @php
 $breadcrumbs = [
 ['label' => 'Finance', 'url' => '#'],
 ['label' => 'Budgets', 'url' => route('admin.finance.budgets.index')],
 ['label' => 'Create Budget']
 ];
 @endphp
 </x-slot:breadcrumbs>
 <div class="mb-6">
 <h1 class="text-2xl font-bold text-gray-800 ">Create Budget</h1>
 </div>

 @if($errors->any())
 <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
 <ul class="list-disc list-inside text-sm">
 @foreach($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 @endif

 <form action="{{ route('admin.finance.budgets.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
 @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Budget Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="fiscal_year_id" class="block text-sm font-medium text-gray-700">Fiscal Year <span class="text-red-500">*</span></label>
                    <select name="fiscal_year_id" id="fiscal_year_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select Fiscal Year</option>
                        @foreach($fiscalYears as $fy)
                            <option value="{{ $fy->id }}">{{ $fy->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

 <div class="mb-6">
 <h3 class="text-lg font-medium text-gray-900 mb-4">Budget Lines</h3>
 
 <table class="w-full" id="lines-table">
 <thead>
 <tr class="text-left text-sm font-medium text-gray-500 ">
 <th class="pb-3 pr-4">Category</th>
 <th class="pb-3 pr-4">Account (Optional)</th>
 <th class="pb-3 pr-4 w-48">Amount</th>
 <th class="pb-3 w-16"></th>
 </tr>
 </thead>
 <tbody id="lines-container">
 <tr class="line-row">
 <td class="pr-4 pb-3">
 <select name="lines[0][budget_category_id]" required
 class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 ">
 <option value="">Select Category</option>
 @foreach($categories as $cat)
 <option value="{{ $cat->id }}">{{ $cat->name }}</option>
 @endforeach
 </select>
 </td>
 <td class="pr-4 pb-3">
 <select name="lines[0][chart_of_account_id]"
 class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 ">
 <option value="">Select Account</option>
 @foreach($accounts as $acc)
 <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
 @endforeach
 </select>
 </td>
 <td class="pr-4 pb-3">
 <input type="number" step="0.01" name="lines[0][amount]" required min="0" placeholder="0.00"
 class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 ">
 </td>
 <td class="pb-3 text-right">
 <button type="button" class="text-red-600 hover:text-red-800 remove-line" title="Remove line">
 <i class="fas fa-trash"></i>
 </button>
 </td>
 </tr>
 </tbody>
 </table>
 
 <button type="button" id="add-line" class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
 <i class="fas fa-plus mr-1"></i> Add Line
 </button>
 </div>

 <div class="flex justify-end pt-5 border-t border-gray-200 ">
 <a href="{{ route('admin.finance.budgets.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 mr-3">Cancel</a>
 <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Budget</button>
 </div>
 </form>
</div>

<script>
 document.addEventListener('DOMContentLoaded', function() {
 let lineCount = 1;
 const container = document.getElementById('lines-container');
 const addButton = document.getElementById('add-line');
 const firstRowTemplate = document.querySelector('.line-row').cloneNode(true);

 addButton.addEventListener('click', function() {
 const newRow = firstRowTemplate.cloneNode(true);
 
 // Update name indices
 const selects = newRow.querySelectorAll('select');
 const inputs = newRow.querySelectorAll('input');
 
 selects.forEach(select => {
 select.name = select.name.replace('[0]', `[${lineCount}]`);
 select.value = '';
 });
 
 inputs.forEach(input => {
 input.name = input.name.replace('[0]', `[${lineCount}]`);
 input.value = '';
 });

 container.appendChild(newRow);
 lineCount++;
 });

 container.addEventListener('click', function(e) {
 const btn = e.target.closest('.remove-line');
 if (btn) {
 const rows = container.querySelectorAll('.line-row');
 if (rows.length > 1) {
 btn.closest('.line-row').remove();
 } else {
 alert('You must have at least one budget line.');
 }
 }
 });
 });
</script>
</x-layouts.admin>
