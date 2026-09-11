<?php

namespace App\Services;

use App\Models\AdCampaign;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdCampaignService
{
    /**
     * Create or update a custom ad campaign.
     *
     * @param array $data
     * @param \Illuminate\Http\UploadedFile|null $imageFile
     * @param int|null $id
     * @return AdCampaign
     */
    public function saveCampaign(array $data, $imageFile = null, $id = null)
    {
        // 1. Process Cropped Base64 Image if provided
        if (!empty($data['cropped_image_data']) && preg_match('/^data:image\/(\w+);base64,/', $data['cropped_image_data'], $type)) {
            $imageData = substr($data['cropped_image_data'], strpos($data['cropped_image_data'], ',') + 1);
            $ext = strtolower($type[1]);
            if ($ext === 'jpeg') $ext = 'jpg';
            $imageData = base64_decode($imageData);
            $fileName = 'ads/ad_' . time() . '_' . Str::random(8) . '.' . $ext;
            Storage::disk('public')->put($fileName, $imageData);
            $data['image_path'] = Storage::url($fileName);
            unset($data['cropped_image_data']);
        } elseif ($imageFile) {
            $path = $imageFile->store('ads', 'public');
            $data['image_path'] = Storage::url($path);
        }

        // 2. Ensure Campaign ID format: CUSTOM-[3 alphabets]-[3 numbers]
        if (empty($data['campaign_id'])) {
            $letters = '';
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            for ($i = 0; $i < 3; $i++) {
                $letters .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $digits = str_pad((string)random_int(0, 999), 3, '0', STR_PAD_LEFT);
            $data['campaign_id'] = "CUSTOM-{$letters}-{$digits}";
        }

        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;
        
        if ($id) {
            $campaign = AdCampaign::findOrFail($id);
            $campaign->update($data);
            return $campaign;
        }

        return AdCampaign::create($data);
    }

    /**
     * Get active custom ads for a given placement and platform.
     *
     * @param string $placement
     * @param string $platform
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveAds(string $placement = null, string $platform = null)
    {
        $query = AdCampaign::where('status', 'Active')
            ->where('is_active', true);

        return $query->get()->filter(function ($ad) use ($placement, $platform) {
            $matchPlacement = true;
            if ($placement && is_array($ad->placements)) {
                $matchPlacement = in_array($placement, $ad->placements);
            }

            $matchPlatform = true;
            if ($platform && is_array($ad->platforms)) {
                $matchPlatform = in_array($platform, $ad->platforms);
            }

            return $matchPlacement && $matchPlatform;
        })->values();
    }
}
