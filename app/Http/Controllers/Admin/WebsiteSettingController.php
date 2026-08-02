<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

class WebsiteSettingController extends Controller
{
    public function index()
    {
        $settings = WebsiteSetting::pluck('value', 'key')->toArray();
        return view('admin.website_settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        
        // Handle file uploads first
        foreach ($request->allFiles() as $key => $file) {
            $path = $file->store('website', 'public');
            WebsiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => '/storage/' . $path]
            );
            // Remove from data array so it doesn't get overwritten below
            unset($data[$key]);
        }

        // Handle regular text/url inputs
        foreach ($data as $key => $value) {
            if ($value !== null) {
                WebsiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        \Illuminate\Support\Facades\Cache::forget('website_settings');

        return back()->with('success', 'Website settings updated successfully.');
    }
}
