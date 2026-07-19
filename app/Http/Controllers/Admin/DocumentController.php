<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $documents = Document::with(['category', 'uploader', 'documentable'])
            ->when($request->search, function ($query, $search) {
                $query->where('original_name', 'like', "%{$search}%")
                      ->orWhere('file_name', 'like', "%{$search}%");
            })
            ->when($request->category_id, function ($query, $category_id) {
                $query->where('category_id', $category_id);
            })
            ->when($request->visibility, function ($query, $visibility) {
                $query->where('visibility', $visibility);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        $categories = DocumentCategory::where('is_active', true)->get();

        return view('admin.documents.index', compact('documents', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Document::class);

        $categories = DocumentCategory::where('is_active', true)->get();

        return view('admin.documents.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request)
    {
        $this->authorize('create', Document::class);

        $data = $request->validated();
        
        $documentableType = app($data['documentable_type']);
        $documentable = $documentableType::find($data['documentable_id']);

        if (!$documentable) {
            return back()->withErrors(['documentable_id' => 'The selected record does not exist.'])->withInput();
        }

        $this->documentService->uploadDocument($data, $request->file('file'), $documentable);

        $routePrefix = match(class_basename($documentableType)) {
            'Project' => 'admin.projects.show',
            'Task' => 'admin.projects.tasks.show',
            'Milestone' => 'admin.milestones.show',
            'Client' => 'admin.clients.show',
            'Company' => 'admin.companies.show',
            'Branch' => 'admin.branches.show',
            'Department' => 'admin.departments.show',
            'Lead' => 'admin.crm.leads.show',
            'Contact' => 'admin.crm.contacts.show',
            'Account' => 'admin.crm.accounts.show',
            'Opportunity' => 'admin.crm.opportunities.show',
            default => null,
        };

        if ($routePrefix) {
            // Store the active tab in session so the view opens the documents tab
            session()->flash('activeTab', 'documents');
            return redirect()->route($routePrefix, $documentable)->with('success', 'Document uploaded successfully.');
        }

        return redirect()->route('admin.documents.index')->with('success', 'Document uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        $this->authorize('view', $document);

        $document->load(['category', 'uploader', 'documentable', 'creator', 'updater']);

        return view('admin.documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        $this->authorize('update', $document);

        $categories = DocumentCategory::where('is_active', true)->get();

        return view('admin.documents.edit', compact('document', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $this->authorize('update', $document);

        $data = $request->validated();
        
        if ($request->hasFile('file')) {
            $this->authorize('replace', $document);
            $this->documentService->replaceDocument($document, $request->file('file'), $data);
        } else {
            $this->documentService->updateMetadata($document, $data);
        }

        return redirect()->route('admin.documents.show', $document)
            ->with('success', 'Document updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        $this->documentService->deleteDocument($document);

        return back()->with('success', 'Document deleted successfully.');
    }

    /**
     * Download the specified document.
     */
    public function download(Document $document)
    {
        $this->authorize('download', $document);
        
        $path = storage_path('app/public/' . $document->path);
        
        if (!file_exists($path)) {
            abort(404, 'File not found on the server.');
        }

        return response()->download($path, $document->original_name);
    }

    /**
     * Get records for a specific module for the document upload dropdown.
     */
    public function getRecords(Request $request, $module)
    {
        // Whitelist allowed modules to prevent arbitrary class instantiation
        $allowedModules = [
            'App\Models\Company',
            'App\Models\Project',
            'App\Models\Task',
            'App\Models\Milestone',
            'App\Models\Client',
            'App\Models\Branch',
            'App\Models\Department',
            'App\Models\Lead',
            'App\Models\Contact',
            'App\Models\Account',
            'App\Models\Opportunity'
        ];

        if (!in_array($module, $allowedModules)) {
            return response()->json([]);
        }

        $search = $request->query('q', '');
        
        $query = $module::query();
        
        // Handle search based on module type
        if (!empty($search)) {
            if (in_array($module, ['App\Models\Client', 'App\Models\Lead', 'App\Models\Contact'])) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
            } else {
                $query->where('name', 'like', "%{$search}%");
            }
        }

        $records = $query->take(50)->get()->map(function ($record) use ($module) {
            $name = $record->name ?? $record->title ?? '';
            
            if (in_array($module, ['App\Models\Client', 'App\Models\Lead', 'App\Models\Contact'])) {
                $name = $record->company_name ?? ($record->first_name . ' ' . $record->last_name);
            }
            
            if (empty($name) && isset($record->task_code)) {
                $name = $record->task_code;
            }

            return [
                'id' => $record->id,
                'name' => $name . ' (ID: ' . $record->id . ')'
            ];
        });

        return response()->json($records);
    }
}
