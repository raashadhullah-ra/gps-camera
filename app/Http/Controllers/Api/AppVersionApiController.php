<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AppVersionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AppVersionApiController extends Controller
{
    protected AppVersionService $versionService;

    public function __construct(AppVersionService $versionService)
    {
        $this->versionService = $versionService;
    }

    /**
     * Get App Version configuration & evaluate force update status.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function check(Request $request): JsonResponse
    {
        $payload = $request->all();
        $clientVersion = $payload['version'] ?? $request->input('version') ?? $request->query('version');
        $platform = $payload['platform'] ?? $request->input('platform') ?? $request->query('platform', 'android');

        $result = $this->versionService->evaluateAppVersion($clientVersion, (string) $platform);

        return response()->json([
            'status'  => true,
            'message' => 'App version configuration retrieved successfully.',
            'data'    => $result,
        ], 200);
    }
}
