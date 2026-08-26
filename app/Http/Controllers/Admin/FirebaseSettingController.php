<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FirebaseSetting;
use App\Services\FirebaseSettingService;
use Illuminate\Http\Request;

class FirebaseSettingController extends Controller
{
    public function __construct(
        protected FirebaseSettingService $firebaseService
    ) {}

    /**
     * Display Firebase settings dashboard & list.
     */
    public function index()
    {
        $settings = $this->firebaseService->getAllSettings();
        $activeSetting = $this->firebaseService->getActiveSetting();

        return view('admin.settings.firebase.index', compact('settings', 'activeSetting'));
    }

    /**
     * Display form to create a new Firebase project configuration.
     */
    public function create()
    {
        return view('admin.settings.firebase.create');
    }

    /**
     * Display form to edit an existing Firebase project configuration.
     */
    public function edit($id)
    {
        $setting = FirebaseSetting::findOrFail($id);
        return view('admin.settings.firebase.edit', compact('setting'));
    }

    /**
     * Store a new Firebase project configuration.
     */
    public function store(Request $request)
    {
        $existing = FirebaseSetting::first();
        if ($existing) {
            return $this->update($request, $existing->id);
        }

        $configMode = $request->input('config_mode', 'upload');

        if ($configMode === 'manual') {
            $validated = $request->validate([
                'label'                 => 'required|string|max:100',
                'project_id'            => 'required|string|max:100',
                'api_key'               => 'required|string|max:255',
                'app_id'                => 'required|string|max:100',
                'messaging_sender_id'   => 'required|string|max:100',
                'auth_domain'           => 'required|string|max:255',
                'storage_bucket'        => 'required|string|max:255',
                'measurement_id'        => 'required|string|max:100',
                'vapid_key'             => 'required|string|max:255',
                'is_active'             => 'nullable|boolean',
            ], [
                'label.required'               => 'The configuration label is required.',
                'project_id.required'          => 'The Firebase Project ID is mandatory.',
                'api_key.required'             => 'The Web API Key is mandatory.',
                'app_id.required'              => 'The App ID is mandatory.',
                'messaging_sender_id.required' => 'The Messaging Sender ID is mandatory.',
                'auth_domain.required'         => 'The Auth Domain is mandatory.',
                'storage_bucket.required'      => 'The Storage Bucket is mandatory.',
                'measurement_id.required'      => 'The Measurement ID is mandatory.',
                'vapid_key.required'           => 'The VAPID Key is mandatory.',
            ]);
        } else {
            $validated = $request->validate([
                'label'                => 'required|string|max:100',
                'service_account_file' => 'required_without:service_account_raw|nullable|file|mimes:json,txt|max:2048',
                'service_account_raw'  => 'required_without:service_account_file|nullable|string',
                'is_active'            => 'nullable|boolean',
            ], [
                'label.required'                        => 'The configuration label is required.',
                'service_account_file.required_without' => 'Please upload a Firebase Service Account JSON file.',
                'service_account_raw.required_without'  => 'Please upload a JSON file or paste the Service Account JSON.',
            ]);
        }

        try {
            $result = $this->firebaseService->storeSetting(
                data: $validated,
                file: $request->file('service_account_file'),
                rawJson: $request->input('service_account_raw'),
                makeActive: $request->boolean('is_active')
            );

            if ($result['test_result']['success']) {
                return redirect()->route('admin.settings.firebase.index')
                    ->with('success', "Firebase project '{$result['setting']->label}' saved and verified! " . $result['test_result']['message']);
            } else {
                return redirect()->route('admin.settings.firebase.index')
                    ->with('error', "Project '{$result['setting']->label}' saved, but connection failed: " . $result['test_result']['message']);
            }
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update an existing Firebase project configuration.
     */
    public function update(Request $request, $id)
    {
        $setting = FirebaseSetting::findOrFail($id);
        $configMode = $request->input('config_mode', 'upload');

        if ($configMode === 'manual') {
            $validated = $request->validate([
                'label'                 => 'required|string|max:100',
                'project_id'            => 'required|string|max:100',
                'api_key'               => 'required|string|max:255',
                'app_id'                => 'required|string|max:100',
                'messaging_sender_id'   => 'required|string|max:100',
                'auth_domain'           => 'required|string|max:255',
                'storage_bucket'        => 'required|string|max:255',
                'measurement_id'        => 'required|string|max:100',
                'vapid_key'             => 'required|string|max:255',
                'is_active'             => 'nullable|boolean',
            ], [
                'label.required'               => 'The configuration label is required.',
                'project_id.required'          => 'The Firebase Project ID is mandatory.',
                'api_key.required'             => 'The Web API Key is mandatory.',
                'app_id.required'              => 'The App ID is mandatory.',
                'messaging_sender_id.required' => 'The Messaging Sender ID is mandatory.',
                'auth_domain.required'         => 'The Auth Domain is mandatory.',
                'storage_bucket.required'      => 'The Storage Bucket is mandatory.',
                'measurement_id.required'      => 'The Measurement ID is mandatory.',
                'vapid_key.required'           => 'The VAPID Key is mandatory.',
            ]);
        } else {
            $validated = $request->validate([
                'label'                => 'required|string|max:100',
                'service_account_file' => 'nullable|file|mimes:json,txt|max:2048',
                'service_account_raw'  => 'nullable|string',
                'is_active'            => 'nullable|boolean',
            ], [
                'label.required' => 'The configuration label is required.',
            ]);
        }

        try {
            $result = $this->firebaseService->updateSetting(
                setting: $setting,
                data: $validated,
                file: $request->file('service_account_file'),
                rawJson: $request->input('service_account_raw'),
                makeActive: $request->boolean('is_active')
            );

            if ($result['test_result']['success']) {
                return redirect()->route('admin.settings.firebase.index')
                    ->with('success', "Firebase project '{$setting->label}' updated and verified! " . $result['test_result']['message']);
            } else {
                return redirect()->route('admin.settings.firebase.index')
                    ->with('error', "Project '{$setting->label}' updated, but connection failed: " . $result['test_result']['message']);
            }
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Activate a Firebase configuration as the live one.
     */
    public function activate($id)
    {
        $setting = FirebaseSetting::findOrFail($id);
        $this->firebaseService->activateSetting($setting);

        return redirect()->route('admin.settings.firebase.index')
            ->with('success', "Active Firebase project switched to '{$setting->label}'. Push notifications will route through this project.");
    }

    /**
     * Test connection for a given Firebase configuration.
     */
    public function testConnection($id)
    {
        $setting = FirebaseSetting::findOrFail($id);
        $result = $this->firebaseService->testConnection($setting);

        if (request()->wantsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', "Connection Test Failed: " . $result['message']);
        }
    }

    /**
     * Delete a Firebase configuration.
     */
    public function destroy($id)
    {
        $setting = FirebaseSetting::findOrFail($id);
        $deletedLabel = $this->firebaseService->deleteSetting($setting);

        return redirect()->route('admin.settings.firebase.index')
            ->with('success', "Firebase configuration '{$deletedLabel}' removed successfully.");
    }
}
