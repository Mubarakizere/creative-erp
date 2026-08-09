<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\ExpertiseCard;
use App\Models\WebsiteProject;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the website home page.
     */
    public function index(): View
    {
        $expertiseCards = ExpertiseCard::where('is_active', true)->orderBy('sort_order')->get();
        $websiteProjects = WebsiteProject::where('is_active', true)->orderBy('sort_order')->get();
        return view('website.home', compact('expertiseCards', 'websiteProjects'));
    }

    /**
     * Display the expertise page.
     */
    public function expertise(): View
    {
        return view('website.expertise');
    }

    public function projects(): View
    {
        return view('website.projects');
    }

    public function about(): View
    {
        return view('website.about');
    }

    public function contact(): View
    {
        $settings = \App\Models\WebsiteSetting::pluck('value', 'key')->toArray();
        return view('website.contact', compact('settings'));
    }
}