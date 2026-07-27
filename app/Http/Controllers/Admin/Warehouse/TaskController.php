<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', \App\Models\WarehouseTask::class);
        $items = \App\Models\WarehouseTask::latest()->paginate(15);
        return view('admin.warehouse.tasks.index', compact('items'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\WarehouseTask::class);
        return view('admin.warehouse.tasks.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\WarehouseTask::class);
        // Implement store logic
        return redirect()->route('admin.warehouse.tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(\App\Models\WarehouseTask $task)
    {
        $this->authorize('view', $task);
        $item = $task;
        return view('admin.warehouse.tasks.show', compact('item'));
    }

    public function edit(\App\Models\WarehouseTask $task)
    {
        $this->authorize('update', $task);
        $item = $task;
        return view('admin.warehouse.tasks.edit', compact('item'));
    }

    public function update(Request $request, \App\Models\WarehouseTask $task)
    {
        $this->authorize('update', $task);
        // Implement update logic
        return redirect()->route('admin.warehouse.tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(\App\Models\WarehouseTask $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return redirect()->route('admin.warehouse.tasks.index')->with('success', 'Task deleted successfully.');
    }
}
