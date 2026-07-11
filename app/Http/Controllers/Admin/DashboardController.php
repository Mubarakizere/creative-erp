<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with placeholder statistics.
     */
    public function index(): View
    {
        $clientsCount = \App\Models\Client::count();
        
        return view('admin.dashboard.index', compact('clientsCount'));
    }
}
