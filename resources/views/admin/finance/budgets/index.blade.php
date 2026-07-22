<x-layouts.admin title="Budgets">
 <x-slot:breadcrumbs>
 @php
 $breadcrumbs = [
 ['label' => 'Finance', 'url' => '#'],
 ['label' => 'Budgets']
 ];
 @endphp
 </x-slot:breadcrumbs>
 <div class="flex justify-between items-center mb-6">
 <h1 class="text-2xl font-bold text-gray-800 ">Budgets</h1>
 <a href="{{ route('admin.finance.budgets.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
 Create Budget
 </a>
 </div>

 <div class="bg-white rounded-lg shadow overflow-hidden">
 <table class="min-w-full divide-y divide-gray-200 ">
 <thead class="bg-gray-50 ">
 <tr>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiscal Year</th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
 <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200 ">
 @forelse($budgets as $budget)
 <tr>
 <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 ">
 {{ $budget->name }}
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">
 {{ $budget->fiscalYear->name ?? 'N/A' }}
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">
 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
 {{ $budget->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
 {{ ucfirst($budget->status) }}
 </span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">
 ${{ number_format($budget->total_amount, 2) }}
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
 <a href="{{ route('admin.finance.budgets.show', $budget) }}" class="text-blue-600 hover:text-blue-900 :text-blue-300">View vs Actuals</a>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 ">
 No budgets found.
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 @if($budgets->hasPages())
 <div class="px-6 py-3 border-t border-gray-200 ">
 {{ $budgets->links() }}
 </div>
 @endif
 </div>
</x-layouts.admin>
