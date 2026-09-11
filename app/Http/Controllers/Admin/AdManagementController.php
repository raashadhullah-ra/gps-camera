<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdMobService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdManagementController extends Controller
{
    /**
     * Display the Ads Management dashboard with ad units, metrics, and placements.
     */
    public function index(Request $request, AdMobService $adMobService)
    {
        $currentTab = $request->query('tab', 'all');
        $search = $request->query('search', '');
        $provider = $request->query('provider', 'all');
        $format = $request->query('format', 'all');
        $placement = $request->query('placement', 'all');
        $audience = $request->query('audience', 'all');
        $dateRange = $request->query('date_range', 'last_30_days');

        // Fetch Real Live AdMob Stats from Google Reporting API
        $liveStats = $adMobService->fetchLiveStats($dateRange);
        $liveUnits = $liveStats['live_units'] ?? [];

        // Load real DB config so Manage Integration modal shows live values
        $config = $adMobService->getConfiguration();

        // Top Metrics (Real Live Data)
        $stats = [
            'revenue' => $liveStats['revenue'] ?? '₹0.00',
            'revenue_subtext' => $liveStats['revenue_subtext'] ?? 'Last 30 days',
            'impressions' => $liveStats['impressions'] ?? '0',
            'fill_rate' => $liveStats['fill_rate'] ?? '0.0%',
            'ctr' => $liveStats['ctr'] ?? '0.0%',
            'active_ads' => $liveStats['active_ads'] ?? count($liveUnits),
            'is_live' => $liveStats['is_live'] ?? false,
            'is_expired' => $liveStats['is_expired'] ?? false,
            'status_message' => $liveStats['status_message'] ?? 'Google AdMob is connected.',
        ];

        // Real AdMob Ad Units fetched live from Google AdMob API
        $allAds = collect($liveUnits ?? []);

        // Fetch Custom Ad Campaigns from DB
        $customAds = \App\Models\AdCampaign::all()->map(function ($campaign) {
            return [
                'name' => $campaign->name,
                'unit_id' => $campaign->campaign_id ?? 'Custom',
                'provider' => 'Internal',
                'provider_type' => 'custom',
                'format' => $campaign->format,
                'placement' => implode(', ', $campaign->placements ?? []),
                'audience' => $campaign->audience_segment_id ? 'Segment ' . $campaign->audience_segment_id : 'All Users',
                'impressions' => '0', // Will implement tracking later
                'ctr' => '0%',
                'revenue' => '₹0.00',
                'status' => $campaign->status ?? 'Active',
            ];
        });

        $allAds = $allAds->concat($customAds);

        // Filter by Tab
        $filteredAds = $allAds;
        if ($currentTab === 'admob') {
            $filteredAds = $filteredAds->where('provider_type', 'admob');
        } elseif ($currentTab === 'custom') {
            $filteredAds = $filteredAds->where('provider_type', 'custom');
        } elseif ($currentTab === 'drafts') {
            $filteredAds = $filteredAds->where('status', 'Draft');
        } elseif ($currentTab === 'paused') {
            $filteredAds = $filteredAds->where('status', 'Paused');
        }

        // Filter by Search Query
        if (!empty($search)) {
            $filteredAds = $filteredAds->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['name']), strtolower($search)) ||
                       str_contains(strtolower($item['unit_id']), strtolower($search)) ||
                       str_contains(strtolower($item['placement']), strtolower($search));
            });
        }

        // Filter by Provider
        if ($provider !== 'All' && $provider !== 'all' && !empty($provider)) {
            $filteredAds = $filteredAds->filter(function ($item) use ($provider) {
                return str_contains(strtolower($item['provider']), strtolower($provider)) ||
                       str_contains(strtolower($item['provider_type']), strtolower($provider));
            });
        }

        // Filter by Format
        if ($format !== 'All' && $format !== 'all' && !empty($format)) {
            $filteredAds = $filteredAds->filter(function ($item) use ($format) {
                return strtolower($item['format']) === strtolower($format);
            });
        }

        // Filter by Placement
        if ($placement !== 'All' && $placement !== 'all' && !empty($placement)) {
            $filteredAds = $filteredAds->filter(function ($item) use ($placement) {
                return str_contains(strtolower($item['placement']), strtolower($placement));
            });
        }

        // Filter by Audience
        if ($audience !== 'All' && $audience !== 'all' && !empty($audience)) {
            $filteredAds = $filteredAds->filter(function ($item) use ($audience) {
                return strtolower($item['audience']) === strtolower($audience);
            });
        }

        $totalFound = $filteredAds->count();
        $totalAllAds = $allAds->count();

        // Setup Pagination
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);
        $paginatedAds = new LengthAwarePaginator(
            $filteredAds->forPage($page, $perPage)->values(),
            $totalFound,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.ads.index', compact('stats', 'paginatedAds', 'currentTab', 'totalFound', 'totalAllAds', 'config'));
    }

    /**
     * Display the Google AdMob Configuration screen.
     */
    public function configuration(AdMobService $adMobService)
    {
        $config = $adMobService->getConfiguration();
        $summaryData = $adMobService->getSummaryAndChecklist();
        $summary = $summaryData['summary'];
        $checklist = $summaryData['checklist'];

        return view('admin.ads.configuration', compact('config', 'summary', 'checklist'));
    }

    /**
     * Save/update the Google AdMob Configuration.
     */
    public function saveConfiguration(Request $request, AdMobService $adMobService)
    {
        $validated = $request->validate([
            'contact_email' => 'nullable|email|max:100',
            'reporting_currency' => 'nullable|string|max:20',
            'android_enabled' => 'nullable|boolean',
            'android_package_name' => 'nullable|string|max:150',
            'android_app_id' => 'nullable|string|max:100',
            'ios_enabled' => 'nullable|boolean',
            'ios_bundle_id' => 'nullable|string|max:150',
            'ios_app_id' => 'nullable|string|max:100',
            'enable_reporting' => 'nullable|boolean',
            'sync_frequency' => 'nullable|string|max:50',
            'default_report_range' => 'nullable|string|max:50',
            'enable_test_ads' => 'nullable|boolean',
            'default_test_device_ids' => 'nullable|string|max:255',
        ]);

        $adMobService->saveConfiguration($request->all());

        return redirect()->route('admin.ads.configuration')->with('success', 'Google AdMob configuration saved successfully.');
    }

    /**
     * Test the Reporting API connection via AJAX.
     */
    public function testConnection(Request $request, AdMobService $adMobService)
    {
        $result = $adMobService->testReportingConnection($request->all());

        return response()->json($result);
    }

    /**
     * Redirect admin to Google OAuth authorization endpoint.
     */
    public function redirectToGoogle(Request $request, AdMobService $adMobService)
    {
        $redirectUri = route('admin.ads.oauth.callback');
        $setting = $adMobService->getSetting();

        // If client credentials are configured, do real Google OAuth redirect
        if (!empty($setting->google_client_id) || env('GOOGLE_CLIENT_ID')) {
            return redirect()->away($adMobService->getGoogleOAuthUrl($redirectUri));
        }

        // If not configured, redirect back with notice or direct modal prompt
        return redirect()->route('admin.ads.configuration')->with('info', 'Please provide your Google OAuth Client ID & Secret to complete Google Cloud verification, or enter your AdMob publisher email.');
    }

    /**
     * Handle Google OAuth Callback code exchange.
     */
    public function handleGoogleCallback(Request $request, AdMobService $adMobService)
    {
        $code = $request->query('code');
        $redirectUri = route('admin.ads.oauth.callback');

        if (!$code) {
            return redirect()->route('admin.ads.configuration')->with('error', 'Google authorization was cancelled or failed.');
        }

        $result = $adMobService->handleGoogleCallback($code, $redirectUri);

        if ($result['success']) {
            return redirect()->route('admin.ads.configuration')->with('success', $result['message']);
        }

        return redirect()->route('admin.ads.configuration')->with('error', $result['message']);
    }

    /**
     * Disconnect Google Account.
     */
    public function disconnectGoogle(Request $request, AdMobService $adMobService)
    {
        $result = $adMobService->disconnectGoogleAccount();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('admin.ads.configuration')->with('success', $result['message']);
    }

    /**
     * Show the form for creating a new custom ad campaign.
     */
    public function createCustom()
    {
        $segments = \App\Models\AudienceSegment::orderBy('name')->get();
        $countries = \App\Models\Location::whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->toArray();

        $generatedCampaignId = self::generateCampaignId();

        return view('admin.ads.create', compact('segments', 'countries', 'generatedCampaignId'));
    }

    /**
     * Generate an automated custom campaign ID in CUSTOM-[3 letters]-[3 numbers] format.
     */
    public static function generateCampaignId(): string
    {
        $letters = '';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < 3; $i++) {
            $letters .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $digits = str_pad((string)random_int(0, 999), 3, '0', STR_PAD_LEFT);
        return "CUSTOM-{$letters}-{$digits}";
    }

    /**
     * Store a newly created custom ad campaign.
     */
    public function storeCustom(Request $request, \App\Services\AdCampaignService $adCampaignService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'campaign_id' => 'nullable|string|max:100',
            'objective' => 'required|string|max:100',
            'format' => 'required|string|max:50',
            'is_active' => 'nullable|boolean',
            'conversion_goal' => 'nullable|string|max:100',
            'destination_type' => 'nullable|string|max:100',
            'destination' => 'nullable|string|max:255',
            'tracking_event' => 'nullable|string|max:100',
            'headline' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'call_to_action' => 'nullable|string|max:50',
            'destination_url' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'placements' => 'nullable|array',
            'audience_segment_id' => 'nullable|integer',
            'countries' => 'nullable|array',
            'platforms' => 'nullable|array',
            'min_app_version' => 'nullable|string|max:20',
            'device_languages' => 'nullable|array',
            'exclude_subscribed' => 'nullable|boolean',
            'consent_eligibility' => 'nullable|string|max:50',
            'frequency_cap' => 'nullable|integer',
            'frequency_per_user' => 'nullable|string|max:50',
            'max_impressions' => 'nullable|integer',
            'stop_at_limit' => 'nullable|boolean',
            'daily_budget' => 'nullable|numeric',
            'pacing' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable|string', // time string
            'end_date' => 'nullable|date',
            'end_time' => 'nullable|string', // time string
            'timezone' => 'nullable|string|max:100',
            'delivery_type' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'publish_option' => 'nullable|string|max:50',
            'notify_admins' => 'nullable|boolean',
            'cropped_image_data' => 'nullable|string',
        ]);

        $imageFile = $request->file('creative_image');

        $campaign = $adCampaignService->saveCampaign($validated, $imageFile);

        return redirect()->route('admin.ads.index')->with('success', 'Custom ad campaign created successfully.');
    }
}