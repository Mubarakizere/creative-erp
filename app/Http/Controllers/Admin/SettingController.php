<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Services\SettingsService;
use App\Services\SequenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        protected SettingsService $settingsService,
        protected SequenceService $sequenceService
    ) {}

    /**
     * Display the settings dashboard.
     */
    public function index(): View
    {
        $settings = $this->settingsService->all();
        $canManage = auth()->user()->can('settings.manage');
        $companyId = auth()->user()->company_id;
        $sequences = $this->sequenceService->getSequencesForCompany($companyId);
        
        return view('admin.settings.index', compact('settings', 'canManage', 'sequences'));
    }

    /**
     * Update the settings in storage.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $validatedSettings = $request->validated('settings');
        
        $settingsToUpdate = [];
        
        foreach ($validatedSettings as $key => $value) {
            // Re-verify the key exists in our whitelist before processing
            if (array_key_exists($key, UpdateSettingsRequest::SUPPORTED_SETTINGS)) {
                $config = UpdateSettingsRequest::SUPPORTED_SETTINGS[$key];
                
                $this->settingsService->set(
                    $key, 
                    $value, 
                    $config['group'], 
                    $config['type']
                );
            }
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Update the sequences in storage.
     */
    public function updateSequences(Request $request): RedirectResponse
    {
        $request->validate([
            'sequences' => 'required|array',
            'sequences.*.id' => 'required|exists:sequences,id',
            'sequences.*.prefix' => 'required|string|max:10|regex:/^[A-Za-z0-9\-_]+$/',
            'sequences.*.next_number' => 'required|integer|min:1',
            'sequences.*.padding' => 'required|integer|min:3|max:10',
        ]);

        $companyId = auth()->user()->company_id;

        foreach ($request->input('sequences') as $seqData) {
            $sequence = \App\Models\Sequence::where('company_id', $companyId)
                ->findOrFail($seqData['id']);

            $sequence->update([
                'prefix' => $seqData['prefix'],
                'next_number' => $seqData['next_number'],
                'padding' => $seqData['padding'],
            ]);
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Document numbering sequences updated successfully.');
    }
}
