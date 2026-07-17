@props(['notification'])

@php
    $priorityColors = [
        'Critical' => 'bg-red-100 text-red-800  ',
        'High' => 'bg-orange-100 text-orange-800  ',
        'Normal' => 'bg-blue-100 text-blue-800  ',
        'Low' => 'bg-gray-100 text-gray-800  ',
    ];
    $colorClass = $priorityColors[$notification->priority ?? 'Normal'] ?? $priorityColors['Normal'];
    
    $iconName = $notification->icon ?? 'bell';
    $iconClass = 'h-5 w-5';
    // Fallback inline SVG
    $iconHtml = '<svg class="'.$iconClass.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>';
@endphp

<div class="flex items-start p-4 border-b border-gray-100  hover:bg-gray-50  transition-colors {{ is_null($notification->read_at) ? 'bg-blue-50/50 ' : '' }}">
    <div class="flex-shrink-0 mr-4">
        <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $notification->color ? 'bg-'.$notification->color.'-100 text-'.$notification->color.'-600 '.$notification->color.'-900/50 '.$notification->color.'-400' : 'bg-gray-100 text-gray-500  ' }}">
            {!! $iconHtml !!}
        </div>
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-gray-900  truncate">
                {{ $notification->data['title'] ?? 'Notification' }}
            </p>
            <div class="ml-2 flex-shrink-0 flex items-center space-x-2">
                @if($notification->priority && $notification->priority !== 'Normal')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $colorClass }}">
                        {{ $notification->priority }}
                    </span>
                @endif
                <span class="text-xs text-gray-500 ">
                    {{ $notification->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
        <p class="text-sm text-gray-600  mt-1 line-clamp-2">
            {{ $notification->data['message'] ?? '' }}
        </p>
        
        <div class="mt-2 flex items-center space-x-3">
            @if(isset($notification->data['action_url']) && isset($notification->data['action_text']))
                <a href="{{ $notification->data['action_url'] }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 ">
                    {{ $notification->data['action_text'] }}
                </a>
            @endif
            
            @if(is_null($notification->read_at))
                <button type="button" class="text-sm font-medium text-gray-500 hover:text-gray-700  "
                        onclick="event.preventDefault(); let f = document.getElementById('single-action-form'); f.action='{{ route('admin.notifications.mark-read', $notification->id) }}'; f.querySelector('input[name=_method]').value='PATCH'; f.submit();">
                    Mark as read
                </button>
            @endif
        </div>
    </div>
</div>
