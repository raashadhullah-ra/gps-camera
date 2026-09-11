<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdCampaignService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdCampaignApiController extends Controller
{
    protected AdCampaignService $adCampaignService;

    public function __construct(AdCampaignService $adCampaignService)
    {
        $this->adCampaignService = $adCampaignService;
    }

    /**
     * Get active custom ad campaigns based on placement and platform.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAds(Request $request): JsonResponse
    {
        $placement = $request->query('placement');
        $platform = $request->query('platform', 'Android'); // Default to Android or could be null

        $ads = $this->adCampaignService->getActiveAds($placement, $platform);

        return response()->json([
            'status' => 'success',
            'data' => $ads,
        ]);
    }
}
