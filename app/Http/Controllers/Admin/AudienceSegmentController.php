<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AudienceSegment;
use App\Services\AudienceRuleEngine;
use App\Services\AudienceSegmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AudienceSegmentController extends Controller
{
    public function __construct(
        protected AudienceSegmentService $segmentService,
        protected AudienceRuleEngine $ruleEngine
    ) {}

    /**
     * Display a listing of audience segments with KPI metrics and filters.
     */
    public function index(Request $request): View
    {
        $segments = $this->segmentService->getFilteredSegments($request, (int) $request->input('per_page', 10));
        $metrics = $this->segmentService->getGlobalMetrics();
        $filterOptions = $this->segmentService->getFilterOptions();

        return view('admin.segments.index', compact('segments', 'metrics', 'filterOptions'));
    }

    /**
     * Show the visual segment builder form.
     */
    public function create(Request $request): View
    {
        $filterOptions = $this->segmentService->getFilterOptions();

        // Optional prefilled location
        $prefill = [
            'city'    => $request->input('city'),
            'state'   => $request->input('state'),
            'country' => $request->input('country'),
        ];

        // Initial default calculation
        $initialEstimate = $this->ruleEngine->evaluateCriteria([
            [
                'match' => 'ALL',
                'rules' => [
                    ['attribute' => 'Country', 'operator' => 'is', 'value' => $prefill['country'] ?: 'India'],
                    ['attribute' => 'State / Region', 'operator' => 'is', 'value' => $prefill['state'] ?: 'Tamil Nadu'],
                    ['attribute' => 'City', 'operator' => 'is', 'value' => $prefill['city'] ?: 'Tirunelveli'],
                    ['attribute' => 'Activity Status', 'operator' => 'is', 'value' => 'Active'],
                    ['attribute' => 'Last Active', 'operator' => 'within', 'value' => '30 days'],
                    ['attribute' => 'Notification Permission', 'operator' => 'is', 'value' => 'Enabled'],
                ],
            ],
        ]);

        return view('admin.segments.create', compact('filterOptions', 'prefill', 'initialEstimate'));
    }

    /**
     * Store a newly created audience segment.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:80',
            'description'      => 'nullable|string|max:200',
            'type'             => 'required|in:dynamic,static',
            'status'           => 'nullable|in:active,draft',
            'rule_groups'      => 'nullable|array',
            'platform_filters' => 'nullable|array',
            'exclusions'       => 'nullable|array',
        ]);

        // Default rule group if empty
        if (empty($validated['rule_groups'])) {
            $validated['rule_groups'] = [
                [
                    'match' => 'ALL',
                    'rules' => [
                        ['attribute' => 'Activity Status', 'operator' => 'is', 'value' => 'Active'],
                    ],
                ],
            ];
        }

        $segment = $this->segmentService->createSegment($validated, auth()->id());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Audience segment created successfully.',
                'data'    => $segment,
            ]);
        }

        return redirect()->route('admin.segments.show', $segment->id)
            ->with('success', 'Audience segment "' . $segment->name . '" created successfully.');
    }

    /**
     * Display detailed overview of a segment.
     */
    public function show(int $id): View
    {
        $segment = AudienceSegment::with(['creator', 'activityLogs'])->findOrFail($id);
        $filterOptions = $this->segmentService->getFilterOptions();

        return view('admin.segments.show', compact('segment', 'filterOptions'));
    }

    /**
     * Show edit builder form for a segment.
     */
    public function edit(int $id): View
    {
        $segment = AudienceSegment::findOrFail($id);
        $filterOptions = $this->segmentService->getFilterOptions();
        $estimate = $this->ruleEngine->evaluateCriteria(
            $segment->rule_groups ?? [],
            $segment->platform_filters,
            $segment->exclusions
        );

        return view('admin.segments.edit', compact('segment', 'filterOptions', 'estimate'));
    }

    /**
     * Update an audience segment.
     */
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $segment = AudienceSegment::findOrFail($id);

        $validated = $request->validate([
            'name'             => 'required|string|max:80',
            'description'      => 'nullable|string|max:200',
            'type'             => 'required|in:dynamic,static',
            'status'           => 'nullable|in:active,draft,paused,archived',
            'rule_groups'      => 'nullable|array',
            'platform_filters' => 'nullable|array',
            'exclusions'       => 'nullable|array',
        ]);

        $this->segmentService->updateSegment($segment, $validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Audience segment updated successfully.',
            ]);
        }

        return redirect()->route('admin.segments.show', $segment->id)
            ->with('success', 'Audience segment "' . $segment->name . '" updated successfully.');
    }

    /**
     * Display individual matching devices in this segment.
     */
    public function audience(Request $request, int $id): View
    {
        $segment = AudienceSegment::findOrFail($id);
        $devices = $this->segmentService->getSegmentAudienceDevices($segment, $request, (int) $request->input('per_page', 10));
        $filterOptions = $this->segmentService->getFilterOptions();

        return view('admin.segments.audience', compact('segment', 'devices', 'filterOptions'));
    }

    /**
     * Duplicate a segment.
     */
    public function duplicate(Request $request, int $id): RedirectResponse
    {
        $sourceSegment = AudienceSegment::findOrFail($id);

        $validated = $request->validate([
            'name'               => 'required|string|max:80',
            'description'        => 'nullable|string|max:200',
            'copy_rules'         => 'nullable|boolean',
            'copy_filters'       => 'nullable|boolean',
            'copy_exclusions'    => 'nullable|boolean',
            'create_as_active'   => 'nullable|boolean',
        ]);

        $newSegment = $this->segmentService->duplicateSegment($sourceSegment, $validated, auth()->id());

        return redirect()->route('admin.segments.index')
            ->with('success', 'Segment duplicated as "' . $newSegment->name . '".');
    }

    /**
     * Refresh dynamic audience on demand.
     */
    public function refreshAudience(int $id): RedirectResponse|JsonResponse
    {
        $segment = AudienceSegment::findOrFail($id);
        $result = $this->segmentService->refreshAudience($segment);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Audience recalculated successfully.',
                'data'    => $result,
            ]);
        }

        return back()->with('success', 'Audience for "' . $segment->name . '" recalculated successfully.');
    }

    /**
     * Pause an audience segment.
     */
    public function pause(Request $request, int $id): RedirectResponse
    {
        $segment = AudienceSegment::findOrFail($id);

        $durationType = $request->input('pause_duration', 'manual');
        $untilDate    = $request->input('pause_until');
        $reason       = $request->input('reason');

        $this->segmentService->pauseSegment($segment, $durationType, $untilDate, $reason);

        return back()->with('success', 'Audience segment paused.');
    }

    /**
     * Resume a paused audience segment.
     */
    public function resume(int $id): RedirectResponse
    {
        $segment = AudienceSegment::findOrFail($id);
        $this->segmentService->resumeSegment($segment);

        return back()->with('success', 'Audience segment resumed to active status.');
    }

    /**
     * Archive an audience segment.
     */
    public function archive(Request $request, int $id): RedirectResponse
    {
        $segment = AudienceSegment::findOrFail($id);
        $reason = $request->input('reason');

        $this->segmentService->archiveSegment($segment, $reason);

        return back()->with('success', 'Audience segment moved to archive.');
    }

    /**
     * Restore an archived audience segment.
     */
    public function restore(int $id): RedirectResponse
    {
        $segment = AudienceSegment::findOrFail($id);
        $this->segmentService->restoreSegment($segment);

        return back()->with('success', 'Audience segment restored from archive.');
    }

    /**
     * Safely delete a segment.
     */
    public function destroy(int $id): RedirectResponse
    {
        $segment = AudienceSegment::findOrFail($id);
        $name = $segment->name;

        $this->segmentService->deleteSegment($segment);

        return redirect()->route('admin.segments.index')
            ->with('success', 'Audience segment "' . $name . '" deleted permanently.');
    }

    /**
     * Export segment data (CSV, XLSX, JSON).
     */
    public function export(Request $request, int $id)
    {
        $segment = AudienceSegment::findOrFail($id);
        return $this->segmentService->exportSegmentData($segment, $request);
    }

    /**
     * Export all segments table CSV.
     */
    public function exportAll(Request $request)
    {
        return $this->segmentService->exportAllSegmentsCsv($request);
    }

    /**
     * AJAX Live Audience Estimation endpoint.
     */
    public function estimateLive(Request $request): JsonResponse
    {
        $ruleGroups      = $request->input('rule_groups', []);
        $platformFilters = $request->input('platform_filters');
        $exclusions      = $request->input('exclusions');

        $estimate = $this->ruleEngine->evaluateCriteria($ruleGroups, $platformFilters, $exclusions);

        return response()->json([
            'success' => true,
            'data'    => $estimate,
        ]);
    }
}
