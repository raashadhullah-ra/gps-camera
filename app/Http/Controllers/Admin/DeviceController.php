<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use App\Services\FirebaseSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeviceController extends Controller
{
    /**
     * The device business service instance.
     *
     * @var DeviceService
     */
    protected DeviceService $deviceService;

    /**
     * Create a new controller instance.
     *
     * @param  DeviceService  $deviceService
     */
    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * Display the installed devices management dashboard.
     *
     * @param  Request  $request
     * @return View
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'search',
            'tab',
            'platform',
            'status',
            'app_version',
            'location',
            'permission',
            'date_from',
            'date_to',
        ]);

        $perPage = (int) $request->get('per_page', 10);
        $devices = $this->deviceService->getFilteredDevices($filters, $perPage);
        $stats = $this->deviceService->getDeviceStats();
        $filterOptions = $this->deviceService->getFilterOptions();

        return view('admin.devices.index', compact('devices', 'stats', 'filterOptions', 'filters'));
    }

    /**
     * Display full device details screen matching Screenshot 1.
     *
     * @param  int  $id
     * @return View
     */
    public function show(int $id): View
    {
        $device = $this->deviceService->getDeviceById($id);
        return view('admin.devices.show', compact('device'));
    }

    /**
     * Dispatch push notification to a single specific device.
     */
    public function sendPush(Request $request, int $id, FirebaseSettingService $firebaseService): JsonResponse
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:120',
            'body'      => 'required|string|max:500',
            'deep_link' => 'nullable|string|max:255',
        ]);

        $device = $this->deviceService->getDeviceById($id);

        if (empty($device->fcm_token)) {
            return response()->json([
                'status'  => 'error',
                'message' => "Device {$device->device_model} does not have an FCM token registered.",
            ], 422);
        }

        try {
            $data = [];
            if (!empty($validated['deep_link'])) {
                $data['deep_link'] = $validated['deep_link'];
            }

            $result = $firebaseService->sendPushNotification(
                $device->fcm_token,
                $validated['title'],
                $validated['body'],
                $data
            );

            return response()->json([
                'status'  => 'success',
                'message' => "Push notification dispatched successfully to {$device->device_model}!",
                'result'  => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'FCM Dispatch failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dispatch push notification campaign to target devices.
     */
    public function sendCampaign(Request $request, FirebaseSettingService $firebaseService): JsonResponse
    {
        $validated = $request->validate([
            'target'    => 'required|string|in:single,all,active,android,ios',
            'device_id' => 'nullable|integer|exists:devices,id',
            'title'     => 'required|string|max:120',
            'body'      => 'required|string|max:500',
            'deep_link' => 'nullable|string|max:255',
        ]);

        $query = \App\Models\Device::whereNotNull('fcm_token')->where('fcm_token', '!=', '');

        if ($validated['target'] === 'single' && !empty($validated['device_id'])) {
            $query->where('id', $validated['device_id']);
        } elseif ($validated['target'] === 'active') {
            $query->where('is_active', true);
        } elseif ($validated['target'] === 'android') {
            $query->where('platform', 'Android');
        } elseif ($validated['target'] === 'ios') {
            $query->where('platform', 'iOS');
        }

        $devices = $query->get();

        if ($devices->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No devices with registered FCM tokens found for the selected target.',
            ], 422);
        }

        $data = [];
        if (!empty($validated['deep_link'])) {
            $data['deep_link'] = $validated['deep_link'];
        }

        $sentCount = 0;
        $failedCount = 0;
        $lastError = null;

        foreach ($devices as $dev) {
            try {
                $firebaseService->sendPushNotification($dev->fcm_token, $validated['title'], $validated['body'], $data);
                $sentCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                $lastError = $e->getMessage();
            }
        }

        if ($sentCount === 0 && $failedCount > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => "FCM dispatch failed for all {$failedCount} device(s). Error: {$lastError}",
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => "Push notification campaign sent to {$sentCount} device(s)." . ($failedCount > 0 ? " ({$failedCount} failed: {$lastError})" : ""),
            'sent_count'   => $sentCount,
            'failed_count' => $failedCount,
        ]);
    }

    /**
     * Mark device as inactive with modal inputs (Screenshot 3).
     *
     * @param  Request  $request
     * @param  int  $id
     * @return RedirectResponse|JsonResponse
     */
    public function markInactive(Request $request, int $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $this->deviceService->markDeviceInactive($id, $validated['reason'], $validated['admin_notes'] ?? null);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Device marked as inactive.']);
        }

        return redirect()->route('admin.devices.index')->with('status', 'Device marked as inactive.');
    }

    /**
     * Toggle active/inactive status for a device.
     *
     * @param  int  $id
     * @return RedirectResponse|JsonResponse
     */
    public function toggleStatus(int $id, Request $request)
    {
        $this->deviceService->toggleDeviceStatus($id);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Device status updated successfully.']);
        }

        return redirect()->back()->with('status', 'Device status updated successfully.');
    }

    /**
     * Remove a device from tracking records.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return RedirectResponse|JsonResponse
     */
    public function destroy(int $id, Request $request)
    {
        $this->deviceService->deleteDevice($id);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Device record removed successfully.']);
        }

        return redirect()->back()->with('status', 'Device record removed successfully.');
    }

    /**
     * Export device installations to CSV file.
     *
     * @return StreamedResponse
     */
    public function exportCsv(): StreamedResponse
    {
        $fileName = 'geocam_installed_devices_' . date('Y-m-d_H-i-s') . '.csv';
        $devices = \App\Models\Device::orderBy('last_active_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($devices) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Installation ID',
                'Hardware ID',
                'Device Model',
                'Brand',
                'Platform',
                'OS Version',
                'App Version',
                'Country',
                'City',
                'Permissions',
                'Notification Status',
                'Status',
                'First Installed',
                'Last Active',
            ]);

            foreach ($devices as $device) {
                fputcsv($handle, [
                    $device->installation_id,
                    $device->hardware_id,
                    $device->device_model,
                    $device->device_brand,
                    $device->platform,
                    $device->os_version,
                    $device->app_version,
                    $device->country,
                    $device->city,
                    $device->permissions_status,
                    $device->notification_status,
                    $device->is_active ? 'Active' : 'Inactive',
                    $device->first_installed_at ? $device->first_installed_at->format('Y-m-d H:i:s') : '',
                    $device->last_active_at ? $device->last_active_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
