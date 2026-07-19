<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\ContactService;
use App\Services\Crm\CustomerTimelineService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ContactController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected ContactService $contactService)
    {
        $this->authorizeResource(Contact::class, 'contact');
    }

    public function index(Request $request)
    {
        $contacts = $this->contactService->getPaginatedContacts($request->all());
        return view('admin.crm.contacts.index', compact('contacts'));
    }

    public function create()
    {
        $companies = \App\Models\Company::where('status', 'active')->orderBy('name')->get();
        return view('admin.crm.contacts.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $contact = $this->contactService->createContact($request->all());
        return redirect()->route('admin.crm.contacts.index')->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact)
    {
        $contact->load(['owner', 'account', 'tags', 'opportunities']);
        return view('admin.crm.contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $companies = \App\Models\Company::where('status', 'active')->orderBy('name')->get();
        return view('admin.crm.contacts.edit', compact('contact', 'companies'));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->contactService->updateContact($contact, $request->all());
        return redirect()->route('admin.crm.contacts.show', $contact)->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $this->contactService->deleteContact($contact);
        return redirect()->route('admin.crm.contacts.index')->with('success', 'Contact archived successfully.');
    }

    public function timeline(Contact $contact, CustomerTimelineService $timelineService)
    {
        $this->authorize('view', $contact);
        
        $timelineEvents = $timelineService->getForModel($contact);
        return view('admin.crm.partials.timeline', compact('timelineEvents'));
    }
}
