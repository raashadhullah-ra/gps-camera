<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AppVersionService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AppVersionController extends Controller
{
    protected AppVersionService $versionService;

    public function __construct(AppVersionService $versionService)
    {
        $this->versionService = $versionService;
    }

    /**
     * Display the App Version & Force Update Management Page.
     *
     * @return View
     */
    public function index(): View
    {
        $settings = $this->versionService->getSettings();
        return view('admin.app_versions.index', compact('settings'));
    }

    /**
     * Update the App Version & Force Update Settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            $this->versionService->updateSettings($request->all());

            return redirect()
                ->route('admin.app-versions.index')
                ->with('success', 'App Version & Force Update settings have been updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update settings: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Live Test Endpoint (AJAX) for testing version logic from Admin UI.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function testVersion(Request $request): JsonResponse
    {
        $testVersion = $request->input('test_version', '1.0.0');
        $platform = $request->input('platform', 'android');

        $result = $this->versionService->evaluateAppVersion($testVersion, $platform);

        return response()->json([
            'status' => 'success',
            'data'   => $result,
        ]);
    }
}
