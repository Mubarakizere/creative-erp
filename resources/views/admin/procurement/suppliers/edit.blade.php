<x-layouts.admin title="Edit Supplier">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.requisitions.index')],
                ['label' => 'Suppliers', 'url' => route('admin.procurement.suppliers.index')],
                ['label' => $supplier->name],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('update', $supplier)
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.procurement.suppliers.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 mb-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Suppliers Directory
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Edit Supplier: {{ $supplier->code }}</h1>
                <p class="mt-1 text-sm text-slate-500 font-medium">Update profile details and classification for {{ $supplier->name }}.</p>
            </div>
        </div>

        <form action="{{ route('admin.procurement.suppliers.update', $supplier) }}" method="POST" id="supplier-form">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden mb-6">
                <div class="bg-slate-50/80 border-b border-slate-200/80 px-6 py-4">
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">Vendor Profile Information</h3>
                    <p class="mt-0.5 text-xs text-slate-500">Update contact and classification details for this supplier.</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Supplier Code <span class="text-rose-500">*</span></label>
                            <input type="text" name="code" id="code" value="{{ old('code', $supplier->code) }}" required class="block w-full rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-xs py-2.5 transition-colors">
                            @error('code') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Company / Vendor Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $supplier->name) }}" required class="block w-full rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-xs py-2.5 transition-colors">
                            @error('name') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="supplier_category_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Supplier Category</label>
                            <select name="supplier_category_id" id="supplier_category_id" class="block w-full rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-xs py-2.5 transition-colors">
                                <option value="">Select Category (Optional)</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('supplier_category_id', $supplier->supplier_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_category_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $supplier->email) }}" placeholder="supplier@example.com" class="block w-full rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-xs py-2.5 transition-colors">
                            @error('email') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $supplier->phone) }}" placeholder="+250 788 000 000" class="block w-full rounded-xl border-slate-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 text-xs py-2.5 transition-colors">
                            @error('phone') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2 pt-2">
                            <div class="bg-amber-50/60 border border-amber-200/80 rounded-xl p-4 flex items-start gap-3">
                                <div class="flex h-5 items-center mt-0.5">
                                    <input type="checkbox" name="is_preferred" id="is_preferred" value="1" {{ old('is_preferred', $supplier->is_preferred) ? 'checked' : '' }} class="h-4 w-4 rounded border-amber-300 text-amber-600 focus:ring-amber-500 transition-colors">
                                </div>
                                <div class="text-xs">
                                    <label for="is_preferred" class="font-bold text-amber-900 cursor-pointer">Mark as Preferred Supplier Partner</label>
                                    <p class="text-amber-700 mt-0.5">Preferred suppliers are prioritized in purchasing requisitions, RFQs, and material source selections.</p>
                                </div>
                            </div>
                            @error('is_preferred') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex items-center justify-end gap-3">
                <a href="{{ route('admin.procurement.suppliers.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors shadow-xs">Cancel</a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-xs transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update Supplier Profile
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-100 mb-4 border border-rose-200">
            <svg class="h-8 w-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Access Denied</h3>
        <p class="text-sm text-slate-500 font-medium">You do not have permission to edit this supplier.</p>
        <div class="mt-6">
            <a href="{{ route('admin.procurement.suppliers.index') }}" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-xs transition-all">Return to Suppliers</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>
