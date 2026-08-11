<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use App\Models\ContactMessage;
use App\Models\ExpertiseCard;
use App\Models\WebsiteProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

    /**
     * Handle the contact form submission.
     * Stores the message in the database and sends it via SMTP email.
     */
    public function sendContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'nullable|string|max:50',
            'subject'    => 'required|string|max:255',
            'message'    => 'required|string|max:5000',
        ]);

        // Store the message in the database
        $contactMessage = ContactMessage::create($validated);

        // Send the email via SMTP
        try {
            $recipientEmail = config('mail.contact_to', config('mail.from.address'));
            Mail::to($recipientEmail)->send(new ContactFormMail($contactMessage));
        } catch (\Exception $e) {
            Log::error('Contact form email failed: ' . $e->getMessage());
            // Message is still saved in DB even if email fails
        }

        return redirect()->route('contact')->with('success', 'Thank you! Your message has been sent successfully. We will get back to you soon.');
    }
}