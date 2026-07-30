@extends('layouts.admin')

@section('title', 'Project Material Issues')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Project Material Issues</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all materials issued to projects.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            @can('material_issue.create')
            <a href="{{ route('admin.project-material-issues.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Issue Material
            </a>
            @endcan
        </div>
    </div>
    
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($issues as $issue)
            <li>
                <a href="{{ route('admin.project-material-issues.show', $issue->id) }}" class="block hover:bg-gray-50">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">{{ $issue->issue_number }}</p>
                            <div class="ml-2 flex-shrink-0 flex">
                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $issue->status }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ $issue->project->name }}
                                </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p>
                                    Issued on <time datetime="{{ $issue->issue_date->format('Y-m-d') }}">{{ $issue->issue_date->format('M d, Y') }}</time>
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            </li>
            @empty
            <li class="px-4 py-12 text-center text-gray-500">
                No material issues found.
            </li>
            @endforelse
        </ul>
    </div>
    
    <div class="mt-4">
        {{ $issues->links() }}
    </div>
</div>
@endsection
