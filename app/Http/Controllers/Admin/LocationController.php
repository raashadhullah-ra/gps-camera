<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LocationController extends Controller
{
    public function __construct(
        protected LocationService $locationService
    ) {}

    /**
     * Display the Locations Overview listing page.
     */
    public function index(Request $request): View
    {
        $metrics = $this->locationService->getGlobalMetrics();
        $filterOptions = $this->locationService->getFilterOptions();
        $locations = $this->locationService->getFilteredLocations($request, 10);

        return view('admin.locations.index', compact('metrics', 'filterOptions', 'locations'));
    }

    /**
     * Display the single Location Overview & Analytics details page.
     */
    public function show(int $id): View
    {
        $location = $this->locationService->getLocationDetails($id);

        return view('admin.locations.show', compact('location'));
    }

    /**
     * Display the Location Interactive Map View with Hotspots / Clusters.
     */
    public function map(int $id): View
    {
        $location = $this->locationService->getLocationDetails($id);

        return view('admin.locations.map', compact('location'));
    }

    /**
     * Display the Location Activity Heatmap and 7x24 Hour Matrix.
     */
    public function heatmap(int $id): View
    {
        $location = $this->locationService->getLocationDetails($id);

        return view('admin.locations.heatmap', compact('location'));
    }

    /**
     * Download CSV export for locations.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        return $this->locationService->exportCsv($request);
    }

    /**
     * Display the Create Audience Segment builder globally (All Locations).
     */
    public function createSegmentGlobal(Request $request): View
    {
        $location = null;
        $filterOptions = $this->locationService->getFilterOptions();
        $estimate = $this->locationService->getAudienceEstimate(null);

        return view('admin.locations.create-segment', compact('location', 'filterOptions', 'estimate'));
    }

    /**
     * Store a newly created global audience segment.
     */
    public function storeSegmentGlobal(Request $request)
    {
        $validated = $request->validate([
            'segment_name' => 'required|string|max:80',
            'description'  => 'nullable|string|max:200',
        ]);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Audience segment "' . $validated['segment_name'] . '" created successfully.');
    }

    /**
     * Display the Create Audience Segment builder for a specific location.
     */
    public function createSegment(int $id): View
    {
        $location = $this->locationService->getLocationDetails($id);
        $filterOptions = $this->locationService->getFilterOptions();
        $estimate = $this->locationService->getAudienceEstimate($location);

        return view('admin.locations.create-segment', compact('location', 'filterOptions', 'estimate'));
    }

    /**
     * Store a newly created audience segment.
     */
    public function storeSegment(Request $request, int $id)
    {
        $location = $this->locationService->getLocationDetails($id);

        $validated = $request->validate([
            'segment_name' => 'required|string|max:80',
            'description'  => 'nullable|string|max:200',
        ]);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Audience segment "' . $validated['segment_name'] . '" created successfully.');
    }

    /**
     * Display Send Notification screen targeted globally (All Locations).
     */
    public function sendNotificationGlobal(Request $request): View
    {
        $location = null;
        $filterOptions = $this->locationService->getFilterOptions();
        $allLocations = $filterOptions['locations'];
        $estimate = $this->locationService->getAudienceEstimate(null);

        return view('admin.locations.send-notification', compact('location', 'allLocations', 'estimate'));
    }

    /**
     * Submit global location push notification.
     */
    public function submitNotificationGlobal(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:100',
            'message' => 'required|string|max:200',
        ]);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Global push notification dispatched to all eligible devices.');
    }

    /**
     * Display Send Notification screen targeted to a specific location.
     */
    public function sendNotification(int $id): View
    {
        $location = $this->locationService->getLocationDetails($id);
        $filterOptions = $this->locationService->getFilterOptions();
        $allLocations = $filterOptions['locations'];
        $estimate = $this->locationService->getAudienceEstimate($location);

        return view('admin.locations.send-notification', compact('location', 'allLocations', 'estimate'));
    }

    /**
     * Submit targeted location push notification.
     */
    public function submitNotification(Request $request, int $id)
    {
        $location = $this->locationService->getLocationDetails($id);

        $validated = $request->validate([
            'title'   => 'required|string|max:100',
            'message' => 'required|string|max:200',
        ]);

        return redirect()->route('admin.locations.show', $location->id)
            ->with('success', 'Notification dispatched to ' . $location->city . ' devices.');
    }

    /**
     * Export all locations dataset (CSV, JSON, or PDF).
     */
    public function exportAllModal(Request $request)
    {
        return $this->locationService->exportAllCustomData($request);
    }

    /**
     * Export custom location data dataset (CSV or JSON).
     */
    public function exportModal(Request $request, int $id)
    {
        $location = $this->locationService->getLocationDetails($id);
        return $this->locationService->exportCustomData($location, $request);
    }
}
