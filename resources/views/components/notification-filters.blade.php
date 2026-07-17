<div class="bg-white  p-4 rounded-lg shadow mb-6 border border-gray-200 ">
    <form action="{{ route('admin.notifications.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="sr-only">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300  rounded-md leading-5 bg-white  placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out " placeholder="Search notifications...">
            </div>
        </div>

        <div class="w-full md:w-48">
            <select name="category" class="block w-full py-2 px-3 border border-gray-300  bg-white  rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm  transition duration-150 ease-in-out" onchange="this.form.submit()">
                <option value="all">All Categories</option>
                <option value="projects" {{ request('category') == 'projects' ? 'selected' : '' }}>Projects</option>
                <option value="tasks" {{ request('category') == 'tasks' ? 'selected' : '' }}>Tasks</option>
                <option value="meetings" {{ request('category') == 'meetings' ? 'selected' : '' }}>Meetings</option>
                <option value="documents" {{ request('category') == 'documents' ? 'selected' : '' }}>Documents</option>
                <option value="workflow" {{ request('category') == 'workflow' ? 'selected' : '' }}>Workflow</option>
                <option value="mentions" {{ request('category') == 'mentions' ? 'selected' : '' }}>Mentions</option>
                <option value="system" {{ request('category') == 'system' ? 'selected' : '' }}>System</option>
            </select>
        </div>

        <div class="w-full md:w-48">
            <select name="priority" class="block w-full py-2 px-3 border border-gray-300  bg-white  rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm  transition duration-150 ease-in-out" onchange="this.form.submit()">
                <option value="all">All Priorities</option>
                <option value="Critical" {{ request('priority') == 'Critical' ? 'selected' : '' }}>Critical</option>
                <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
                <option value="Normal" {{ request('priority') == 'Normal' ? 'selected' : '' }}>Normal</option>
                <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
            </select>
        </div>

        <div class="w-full md:w-48">
            <select name="status" class="block w-full py-2 px-3 border border-gray-300  bg-white  rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm  transition duration-150 ease-in-out" onchange="this.form.submit()">
                <option value="all">All Statuses</option>
                <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
            </select>
        </div>
        
        <noscript>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Filter
            </button>
        </noscript>
    </form>
</div>
