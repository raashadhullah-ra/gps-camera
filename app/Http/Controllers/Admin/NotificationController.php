<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificationController extends Controller
{
    /**
     * NotificationController constructor.
     *
     * @param  NotificationService  $notificationService
     */
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Display a listing of notification campaigns.
     *
     * @param  Request  $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Process any due scheduled campaigns immediately
        $this->notificationService->processDueScheduledNotifications();

        $filters = $request->only([
            'tab',
            'search',
            'status',
            'audience',
            'platform',
            'created_by',
            'date_range',
            'per_page',
        ]);

        $tab = $request->get('tab', 'all');
        $perPage = (int) $request->get('per_page', 10);

        $campaigns = $this->notificationService->getFilteredCampaigns($filters, $perPage);
        $stats = $this->notificationService->getNotificationStats();
        $tabCounts = $this->notificationService->getTabCounts();
        $filterOptions = $this->notificationService->getFilterOptions();

        return view('admin.notifications.index', compact('campaigns', 'stats', 'tabCounts', 'filterOptions', 'tab'));
    }

    /**
     * Show the form for creating a new push notification campaign.
     *
     * @return View
     */
    public function create(): View
    {
        $audienceMetrics = $this->notificationService->getAudienceMetrics();
        $segments = \App\Models\AudienceSegment::all();
        $locations = \App\Models\Location::orderBy('city')->get();
        $devices = \App\Models\Device::latest()->get();

        return view('admin.notifications.create', compact('audienceMetrics', 'segments', 'locations', 'devices'));
    }

    /**
     * Store a newly created notification campaign.
     *
     * @param  Request  $request
     * @return RedirectResponse|JsonResponse
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'name'           => 'required|string|max:150',
            'title'          => 'required|string|max:100',
            'message'        => 'required|string|max:200',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'action'         => 'nullable|string',
            'deep_link'      => 'nullable|string',
            'audience_type'  => 'required|string',
            'status'         => 'nullable|string',
            'custom_payload' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $originalBase = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanName = \Illuminate\Support\Str::slug($originalBase) ?: 'notification';
            $dateTime = date('Ymd_His');
            $uniqueSuffix = substr(uniqid(), -4);
            $extension = strtolower($imageFile->getClientOriginalExtension());

            // Saved format: noti_20260827_095812_a1b2.jpg (prevents collisions & preserves naming + date + time)
            $imageName = "{$cleanName}_{$dateTime}_{$uniqueSuffix}.{$extension}";
            $path = $imageFile->storeAs('notifications', $imageName, 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $campaign = $this->notificationService->createCampaign($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Notification campaign saved successfully.',
                'campaign' => $campaign,
            ]);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification campaign created successfully.');
    }

    /**
     * Display the specified campaign details.
     *
     * @param  int|string  $id
     * @return View
     */
    public function show($id): View
    {
        // Process any due scheduled campaigns immediately
        $this->notificationService->processDueScheduledNotifications();

        $campaign = $this->notificationService->findCampaign($id);

        return view('admin.notifications.show', compact('campaign'));
    }

    /**
     * Show the form for editing the specified notification campaign.
     *
     * @param  int|string  $id
     * @return View
     */
    public function edit($id): View
    {
        $campaign = $this->notificationService->findCampaign($id);
        $audienceMetrics = $this->notificationService->getAudienceMetrics();
        $segments = \App\Models\AudienceSegment::all();
        $locations = \App\Models\Location::orderBy('city')->get();
        $devices = \App\Models\Device::latest()->get();

        return view('admin.notifications.edit', compact('campaign', 'audienceMetrics', 'segments', 'locations', 'devices'));
    }

    /**
     * Update the specified notification campaign.
     *
     * @param  Request  $request
     * @param  int|string  $id
     * @return RedirectResponse|JsonResponse
     */
    public function update(Request $request, $id): RedirectResponse|JsonResponse
    {
        $request->validate([
            'name'           => 'required|string|max:150',
            'title'          => 'required|string|max:100',
            'message'        => 'required|string|max:200',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'action'         => 'nullable|string',
            'deep_link'      => 'nullable|string',
            'audience_type'  => 'required|string',
            'status'         => 'nullable|string',
            'custom_payload' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $originalBase = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanName = \Illuminate\Support\Str::slug($originalBase) ?: 'notification';
            $dateTime = date('Ymd_His');
            $uniqueSuffix = substr(uniqid(), -4);
            $extension = strtolower($imageFile->getClientOriginalExtension());

            $imageName = "{$cleanName}_{$dateTime}_{$uniqueSuffix}.{$extension}";
            $path = $imageFile->storeAs('notifications', $imageName, 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $campaign = $this->notificationService->updateCampaign($id, $data);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Notification campaign updated successfully.',
                'campaign' => $campaign,
            ]);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification campaign updated successfully.');
    }

    /**
     * Duplicate a notification campaign as a new draft.
     *
     * @param  Request  $request
     * @param  int|string  $id
     * @return RedirectResponse
     */
    public function duplicate(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:150',
        ]);

        $options = [
            'name'          => $request->input('name'),
            'copy_content'  => $request->boolean('copy_content', true),
            'copy_audience' => $request->boolean('copy_audience', true),
            'copy_delivery' => $request->boolean('copy_delivery', true),
        ];

        $newCampaign = $this->notificationService->duplicateCampaign($id, $options);

        return redirect()->route('admin.notifications.index')
            ->with('success', "Notification draft '{$newCampaign->name}' created successfully.");
    }

    /**
     * Reschedule a notification campaign.
     *
     * @param  Request  $request
     * @param  int|string  $id
     * @return RedirectResponse
     */
    public function reschedule(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'delivery_date' => 'required|date',
            'delivery_time' => 'required',
            'time_zone'     => 'nullable|string|max:50',
        ]);

        $campaign = $this->notificationService->rescheduleCampaign($id, [
            'delivery_date' => $request->input('delivery_date'),
            'delivery_time' => $request->input('delivery_time'),
            'time_zone'     => $request->input('time_zone', 'Asia/Kolkata'),
            'quiet_hours'   => $request->boolean('quiet_hours', true),
        ]);

        return redirect()->back()
            ->with('success', "Campaign '{$campaign->name}' rescheduled for {$campaign->scheduled_at?->format('d M Y, h:i A')}.");
    }

    /**
     * Send a notification campaign immediately.
     *
     * @param  Request  $request
     * @param  int|string  $id
     * @return RedirectResponse|JsonResponse
     */
    public function sendNow(Request $request, $id): RedirectResponse|JsonResponse
    {
        $campaign = $this->notificationService->sendNow($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Campaign '{$campaign->name}' dispatched for immediate delivery.",
            ]);
        }

        return redirect()->back()
            ->with('success', "Campaign '{$campaign->name}' dispatched for immediate delivery.");
    }

    /**
     * Cancel a scheduled campaign and move back to draft or archive.
     *
     * @param  Request  $request
     * @param  int|string  $id
     * @return RedirectResponse
     */
    public function cancelSchedule(Request $request, $id): RedirectResponse
    {
        $options = [
            'after_cancellation' => $request->input('after_cancellation', 'draft'),
            'cancel_reason'      => $request->input('cancel_reason'),
        ];

        $campaign = $this->notificationService->cancelSchedule($id, $options);

        $destText = ($campaign->status === 'archived') ? 'Archived' : 'Drafts';

        return redirect()->back()
            ->with('success', "Schedule for '{$campaign->name}' cancelled. Moved to {$destText}.");
    }

    /**
     * Archive a campaign.
     *
     * @param  int|string  $id
     * @return RedirectResponse
     */
    public function archive($id): RedirectResponse
    {
        $campaign = $this->notificationService->archiveCampaign($id);

        return redirect()->back()
            ->with('success', "Campaign '{$campaign->name}' moved to archive.");
    }

    /**
     * Remove the specified campaign permanently.
     *
     * @param  int|string  $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $campaign = $this->notificationService->findCampaign($id);
        $name = $campaign->name ?: $campaign->title;

        $this->notificationService->deleteCampaign($id);

        return redirect()->route('admin.notifications.index')
            ->with('success', "Campaign '{$name}' deleted permanently.");
    }

    /**
     * Export campaigns to CSV.
     *
     * @param  Request  $request
     * @return StreamedResponse
     */
    public function export(Request $request): StreamedResponse
    {
        return $this->notificationService->exportCsv();
    }
}
