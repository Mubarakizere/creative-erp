<x-layouts.admin title="Expertise Cards">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Website CMS'],
                ['label' => 'Expertise Cards'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Expertise Cards</h1>
            <p class="mt-1 text-sm text-gray-500">Manage the dynamic cards displayed on the public Expertise page.</p>
        </div>
        <x-button type="primary" href="{{ route('admin.expertise-cards.create') }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Card
        </x-button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl flex items-center shadow-sm border border-green-100 mb-6">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-200">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($cards as $card)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($card->image)
                                    <div class="h-12 w-20 rounded bg-gray-100 overflow-hidden border">
                                        <img src="{{ Str::startsWith($card->image, 'http') ? $card->image : asset($card->image) }}" class="h-full w-full object-cover">
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">No image</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $card->title }}</div>
                                <div class="text-xs text-gray-500 mt-1 line-clamp-1 max-w-xs">{{ $card->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $card->sort_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($card->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <x-action-dropdown>
                                    @can('update', $card)
                                        <x-action-dropdown-item href="{{ route('admin.expertise-cards.edit', $card) }}" icon="edit">
                                            Edit Card
                                        </x-action-dropdown-item>
                                    @endcan
                                    @can('delete', $card)
                                        <x-action-dropdown-item @click="$dispatch('open-modal', 'delete-card-{{ $card->id }}')" icon="delete" variant="danger">
                                            Delete Card
                                        </x-action-dropdown-item>
                                    @endcan
                                </x-action-dropdown>

                                {{-- Delete Modal --}}
                                @can('delete', $card)
                                <x-modal id="delete-card-{{ $card->id }}" maxWidth="md">
                                    <x-slot:header>Delete Expertise Card</x-slot:header>

                                    <div class="text-center py-4">
                                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ $card->title }}"?</h3>
                                        <p class="text-sm text-gray-500">This action will permanently delete the card and remove it from the public website.</p>
                                    </div>

                                    <x-slot:footer>
                                        <x-button type="ghost" @click="open = false">Cancel</x-button>
                                        <form method="POST" action="{{ route('admin.expertise-cards.destroy', $card) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="danger" submit>Delete Card</x-button>
                                        </form>
                                    </x-slot:footer>
                                </x-modal>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                No expertise cards found. Click "Add New Card" to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.admin>
