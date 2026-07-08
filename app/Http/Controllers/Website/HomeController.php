<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the website home page.
     */
    public function index(): View
    {
        return view('website.home');
    }
}
