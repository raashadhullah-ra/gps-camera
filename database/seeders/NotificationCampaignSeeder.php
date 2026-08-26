<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationCampaign;
use App\Models\AudienceSegment;
use App\Models\User;
use Carbon\Carbon;

class NotificationCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : null;
        $segment = AudienceSegment::first();
        $segmentId = $segment ? $segment->id : null;

        $campaigns = [
            [
                'campaign_id'         => 'NTF-2026-0128',
                'name'                => 'GPS Camera Feature Update',
                'title'               => 'GPS Camera Update',
                'message'             => 'New GPS Camera features are now available. Explore improved location stamps and better performance.',
                'action'              => 'Open App',
                'audience_type'       => 'individual',
                'audience_label'      => 'Individual Devices • 3',
                'segment_id'          => $segmentId,
                'target_device_ids'   => ['ANON-DEVICE-001', 'ANON-DEVICE-002', 'ANON-DEVICE-003'],
                'android_count'       => 2,
                'ios_count'           => 1,
                'total_audience'      => 3,
                'delivered_count'     => 0,
                'open_count'          => 0,
                'open_rate'           => null,
                'status'              => 'scheduled',
                'scheduled_at'        => Carbon::parse('2026-08-26 10:30:00'),
                'sent_at'             => null,
                'time_zone'           => 'Asia/Kolkata',
                'quiet_hours_enabled' => true,
                'expiry_hours'        => 24,
                'timeline_steps'      => [
                    ['title' => 'Draft Created', 'time' => '24 Aug 2026, 09:45 AM', 'done' => true, 'icon' => 'fa-regular fa-file-lines'],
                    ['title' => 'Audience Selected', 'time' => '24 Aug 2026, 09:55 AM', 'done' => true, 'icon' => 'fa-solid fa-users'],
                    ['title' => 'Scheduled', 'time' => '24 Aug 2026, 10:00 AM', 'done' => true, 'icon' => 'fa-regular fa-calendar'],
                    ['title' => 'Awaiting Delivery', 'time' => '26 Aug 2026, 10:30 AM', 'done' => false, 'icon' => 'fa-regular fa-clock'],
                ],
                'created_by'          => $adminId,
                'created_at'          => Carbon::parse('2026-08-24 10:00:00'),
                'updated_at'          => Carbon::parse('2026-08-24 10:00:00'),
            ],
            [
                'campaign_id'         => 'NTF-2026-0127',
                'name'                => 'New Map Styles Available',
                'title'               => 'Explore New GPS Map Themes',
                'message'             => 'We have added satellite, hybrid and terrain styling to your photo stamps. Check them out today!',
                'action'              => 'Open App',
                'audience_type'       => 'all',
                'audience_label'      => 'All Eligible Installations • 7,054',
                'segment_id'          => null,
                'target_device_ids'   => null,
                'android_count'       => 6419,
                'ios_count'           => 635,
                'total_audience'      => 7054,
                'delivered_count'     => 6842,
                'open_count'          => 2928,
                'open_rate'           => 42.80,
                'status'              => 'sent',
                'scheduled_at'        => Carbon::parse('2026-08-23 09:00:00'),
                'sent_at'             => Carbon::parse('2026-08-23 09:00:00'),
                'time_zone'           => 'Asia/Kolkata',
                'quiet_hours_enabled' => true,
                'expiry_hours'        => 24,
                'timeline_steps'      => [
                    ['title' => 'Draft Created', 'time' => '22 Aug 2026, 09:00 AM', 'done' => true, 'icon' => 'fa-regular fa-file-lines'],
                    ['title' => 'Audience Selected', 'time' => '22 Aug 2026, 09:10 AM', 'done' => true, 'icon' => 'fa-solid fa-users'],
                    ['title' => 'Scheduled', 'time' => '22 Aug 2026, 09:15 AM', 'done' => true, 'icon' => 'fa-regular fa-calendar'],
                    ['title' => 'Delivered', 'time' => '23 Aug 2026, 09:00 AM', 'done' => true, 'icon' => 'fa-solid fa-check'],
                ],
                'created_by'          => $adminId,
                'created_at'          => Carbon::parse('2026-08-22 09:00:00'),
                'updated_at'          => Carbon::parse('2026-08-23 09:00:00'),
            ],
            [
                'campaign_id'         => 'NTF-2026-0126',
                'name'                => 'Location Stamp Tips',
                'title'               => 'Get High Accuracy GPS Coordinates',
                'message'             => 'Learn how to calibrate your camera sensors for pinpoint geotag precision in every capture.',
                'action'              => 'Open App',
                'audience_type'       => 'segment',
                'audience_label'      => 'Tirunelveli Active Users • 1,284',
                'segment_id'          => $segmentId,
                'target_device_ids'   => null,
                'android_count'       => 1168,
                'ios_count'           => 116,
                'total_audience'      => 1284,
                'delivered_count'     => 1241,
                'open_count'          => 635,
                'open_rate'           => 51.20,
                'status'              => 'sent',
                'scheduled_at'        => Carbon::parse('2026-08-21 18:30:00'),
                'sent_at'             => Carbon::parse('2026-08-21 18:30:00'),
                'time_zone'           => 'Asia/Kolkata',
                'quiet_hours_enabled' => true,
                'expiry_hours'        => 24,
                'timeline_steps'      => [
                    ['title' => 'Draft Created', 'time' => '20 Aug 2026, 11:15 AM', 'done' => true, 'icon' => 'fa-regular fa-file-lines'],
                    ['title' => 'Audience Selected', 'time' => '20 Aug 2026, 11:20 AM', 'done' => true, 'icon' => 'fa-solid fa-users'],
                    ['title' => 'Scheduled', 'time' => '20 Aug 2026, 11:25 AM', 'done' => true, 'icon' => 'fa-regular fa-calendar'],
                    ['title' => 'Delivered', 'time' => '21 Aug 2026, 06:30 PM', 'done' => true, 'icon' => 'fa-solid fa-check'],
                ],
                'created_by'          => $adminId,
                'created_at'          => Carbon::parse('2026-08-20 11:15:00'),
                'updated_at'          => Carbon::parse('2026-08-21 18:30:00'),
            ],
            [
                'campaign_id'         => 'NTF-2026-0125',
                'name'                => 'Weekend Feature Highlight',
                'title'               => 'Weekend Photo Stamps & Overlays',
                'message'             => 'Capture your weekend travels with customizable time & altitude watermarks.',
                'action'              => 'Open App',
                'audience_type'       => 'segment',
                'audience_label'      => 'Audience Segment • 2,190',
                'segment_id'          => $segmentId,
                'target_device_ids'   => null,
                'android_count'       => 1993,
                'ios_count'           => 197,
                'total_audience'      => 2190,
                'delivered_count'     => 0,
                'open_count'          => 0,
                'open_rate'           => null,
                'status'              => 'draft',
                'scheduled_at'        => null,
                'sent_at'             => null,
                'time_zone'           => 'Asia/Kolkata',
                'quiet_hours_enabled' => true,
                'expiry_hours'        => 24,
                'timeline_steps'      => [
                    ['title' => 'Draft Created', 'time' => '19 Aug 2026, 02:00 PM', 'done' => true, 'icon' => 'fa-regular fa-file-lines'],
                    ['title' => 'Audience Selected', 'time' => '19 Aug 2026, 02:05 PM', 'done' => true, 'icon' => 'fa-solid fa-users'],
                ],
                'created_by'          => $adminId,
                'created_at'          => Carbon::parse('2026-08-19 14:00:00'),
                'updated_at'          => Carbon::parse('2026-08-19 14:00:00'),
            ],
            [
                'campaign_id'         => 'NTF-2026-0124',
                'name'                => 'Critical App Update',
                'title'               => 'Important Fix Available for Android',
                'message'             => 'Please update your GPS Camera app to continue receiving automated geotag synchronization.',
                'action'              => 'Open App',
                'audience_type'       => 'segment',
                'audience_label'      => 'Android v1.3 and below • 984',
                'segment_id'          => $segmentId,
                'target_device_ids'   => null,
                'android_count'       => 984,
                'ios_count'           => 0,
                'total_audience'      => 984,
                'delivered_count'     => 0,
                'open_count'          => 0,
                'open_rate'           => 0.00,
                'status'              => 'failed',
                'scheduled_at'        => Carbon::parse('2026-08-18 11:15:00'),
                'sent_at'             => Carbon::parse('2026-08-18 11:15:00'),
                'time_zone'           => 'Asia/Kolkata',
                'quiet_hours_enabled' => true,
                'expiry_hours'        => 24,
                'timeline_steps'      => [
                    ['title' => 'Draft Created', 'time' => '18 Aug 2026, 10:00 AM', 'done' => true, 'icon' => 'fa-regular fa-file-lines'],
                    ['title' => 'Audience Selected', 'time' => '18 Aug 2026, 10:15 AM', 'done' => true, 'icon' => 'fa-solid fa-users'],
                    ['title' => 'Scheduled', 'time' => '18 Aug 2026, 10:30 AM', 'done' => true, 'icon' => 'fa-regular fa-calendar'],
                    ['title' => 'Dispatch Failed', 'time' => '18 Aug 2026, 11:15 AM', 'done' => false, 'icon' => 'fa-solid fa-triangle-exclamation'],
                ],
                'created_by'          => $adminId,
                'created_at'          => Carbon::parse('2026-08-18 10:00:00'),
                'updated_at'          => Carbon::parse('2026-08-18 11:15:00'),
            ],
            [
                'campaign_id'         => 'NTF-2026-0123',
                'name'                => 'Storage Permission Reminder',
                'title'               => 'Ensure Camera Photos are Saved',
                'message'             => 'Grant storage permissions to automatically save HD watermarked pictures to your gallery.',
                'action'              => 'Open App',
                'audience_type'       => 'segment',
                'audience_label'      => 'Location: Chennai • 862',
                'segment_id'          => $segmentId,
                'target_device_ids'   => null,
                'android_count'       => 784,
                'ios_count'           => 78,
                'total_audience'      => 862,
                'delivered_count'     => 831,
                'open_count'          => 321,
                'open_rate'           => 38.60,
                'status'              => 'sent',
                'scheduled_at'        => Carbon::parse('2026-08-16 16:00:00'),
                'sent_at'             => Carbon::parse('2026-08-16 16:00:00'),
                'time_zone'           => 'Asia/Kolkata',
                'quiet_hours_enabled' => true,
                'expiry_hours'        => 24,
                'timeline_steps'      => [
                    ['title' => 'Draft Created', 'time' => '15 Aug 2026, 09:30 AM', 'done' => true, 'icon' => 'fa-regular fa-file-lines'],
                    ['title' => 'Audience Selected', 'time' => '15 Aug 2026, 09:45 AM', 'done' => true, 'icon' => 'fa-solid fa-users'],
                    ['title' => 'Scheduled', 'time' => '15 Aug 2026, 10:00 AM', 'done' => true, 'icon' => 'fa-regular fa-calendar'],
                    ['title' => 'Delivered', 'time' => '16 Aug 2026, 04:00 PM', 'done' => true, 'icon' => 'fa-solid fa-check'],
                ],
                'created_by'          => $adminId,
                'created_at'          => Carbon::parse('2026-08-15 09:30:00'),
                'updated_at'          => Carbon::parse('2026-08-16 16:00:00'),
            ],
        ];

        foreach ($campaigns as $data) {
            NotificationCampaign::updateOrCreate(
                ['campaign_id' => $data['campaign_id']],
                $data
            );
        }
    }
}
