<x-layouts.admin title="Budget vs Actual - {{ $budget->name }}">
 <x-slot:breadcrumbs>
 @php
 $breadcrumbs = [
 ['label' => 'Finance', 'url' => '#'],
 ['label' => 'Budgets', 'url' => route('admin.finance.budgets.index')],
 ['label' => $budget->name]
 ];
 @endphp
 </x-slot:breadcrumbs>
 <div class="flex justify-between items-center mb-6">
 <div>
 <h1 class="text-2xl font-bold text-gray-800 ">Budget vs Actuals: {{ $budget->name }}</h1>
 <p class="text-sm text-gray-500 mt-1">Fiscal Year: {{ $budget->fiscalYear->name ?? 'N/A' }}</p>
 </div>
 <div class="flex space-x-3">
 <a href="{{ route('admin.finance.budgets.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">Back to Budgets</a>
 </div>
 </div>

 <!-- Summary Cards -->
 <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
 <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
 <p class="text-sm font-medium text-gray-500 ">Total Budget</p>
 <p class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($analysis['summary']['budget'], 2) }}</p>
 </div>
 
 <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
 <p class="text-sm font-medium text-gray-500 ">Total Actual</p>
 <p class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($analysis['summary']['actual'], 2) }}</p>
 </div>
 
 <div class="bg-white rounded-lg shadow p-5 border-l-4 {{ $analysis['summary']['variance'] >= 0 ? 'border-green-500' : 'border-red-500' }}">
 <p class="text-sm font-medium text-gray-500 ">Variance</p>
 <p class="text-2xl font-bold {{ $analysis['summary']['variance'] >= 0 ? 'text-green-600 ' : 'text-red-600 ' }} mt-1">
 ${{ number_format($analysis['summary']['variance'], 2) }}
 </p>
 </div>
 
 <div class="bg-white rounded-lg shadow p-5 border-l-4 {{ $analysis['summary']['status'] == 'exceeded' ? 'border-red-500' : ($analysis['summary']['status'] == 'warning' ? 'border-yellow-500' : 'border-green-500') }}">
 <p class="text-sm font-medium text-gray-500 ">Utilization</p>
 <p class="text-2xl font-bold text-gray-900 mt-1">
 {{ number_format(( $analysis['summary']['budget'] > 0 ? ($analysis['summary']['actual'] / $analysis['summary']['budget']) * 100 : 0 ), 1) }}%
 </p>
 <p class="text-xs mt-1 
 {{ $analysis['summary']['status'] == 'exceeded' ? 'text-red-600' : ($analysis['summary']['status'] == 'warning' ? 'text-yellow-600' : 'text-green-600') }}">
 {{ strtoupper(str_replace('_', ' ', $analysis['summary']['status'])) }}
 </p>
 </div>
 </div>

 <!-- Details Table -->
 <div class="bg-white rounded-lg shadow overflow-hidden">
 <div class="px-6 py-4 border-b border-gray-200 ">
 <h3 class="text-lg font-medium text-gray-900 ">Budget Lines Breakdown</h3>
 </div>
 <table class="min-w-full divide-y divide-gray-200 ">
 <thead class="bg-gray-50 ">
 <tr>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
 <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Budget Amount</th>
 <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actual Amount</th>
 <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Variance ($)</th>
 <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Variance (%)</th>
 <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200 ">
 @forelse($analysis['lines'] as $line)
 <tr>
 <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 ">
 {{ $line['category'] }}
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">
 {{ $line['account'] ?? 'All Accounts' }}
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900 ">
 ${{ number_format($line['budget_amount'], 2) }}
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 ">
 ${{ number_format($line['actual_amount'], 2) }}
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $line['variance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
 {{ $line['variance'] >= 0 ? '+' : '' }}${{ number_format($line['variance'], 2) }}
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $line['variance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
 {{ number_format($line['variance_percentage'], 2) }}%
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
 {{ $line['status'] == 'exceeded' ? 'bg-red-100 text-red-800' : ($line['status'] == 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
 {{ strtoupper(str_replace('_', ' ', $line['status'])) }}
 </span>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 ">
 No budget lines found.
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
</x-layouts.admin>
