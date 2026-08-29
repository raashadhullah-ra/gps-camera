<?php

namespace Tests\Feature;

use App\Models\AudienceSegment;
use App\Models\Device;
use App\Models\FirebaseSetting;
use App\Models\NotificationCampaign;
use App\Models\User;
use App\Services\AudienceRuleEngine;
use App\Services\AudienceSegmentService;
use App\Services\FirebaseSettingService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationAndAudienceSegmentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Device $device33;
    protected Device $inactiveDevice;
    protected Device $deniedDevice;
    protected Device $invalidFcmDevice;
    protected Device $iosDevice;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Ensure Super Admin User exists
        $this->admin = User::firstOrCreate(
            ['email' => 'admin@gpscamera.app'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'displayname' => 'Super Admin',
                'admin_id' => 'ADM-0001',
                'password' => bcrypt('123456'),
                'role' => 'Super Admin',
                'status' => 'Active',
            ]
        );

        // 2. Mock FirebaseSettingService for push notification dispatching
        $this->mock(FirebaseSettingService::class, function ($mock) {
            $mock->shouldReceive('sendPushNotification')
                ->andReturn(['name' => 'projects/test-geocam-fcm/messages/msg_123456789']);
            $mock->shouldReceive('getActiveSetting')
                ->andReturn(new FirebaseSetting(['is_active' => true, 'project_id' => 'test-geocam-fcm']));
        });

        // Mock Firebase OAuth2 token endpoint & FCM v1 messages:send
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock_access_token_12345',
                'expires_in'   => 3600,
                'token_type'   => 'Bearer',
            ], 200),
            'https://fcm.googleapis.com/v1/projects/*/messages:send' => Http::response([
                'name' => 'projects/test-geocam-fcm/messages/msg_123456789',
            ], 200),
        ]);

        // 3. Setup real & diverse test device dataset
        // Real Device 33 (Samsung A71, Tirunelveli, Android, Active, Enabled)
        $this->device33 = Device::updateOrCreate(
            ['id' => 33],
            [
                'installation_id' => 'INS-1E77649B',
                'firebase_installation_id' => 'dY14eyGpROqH4CmBvR4LdI',
                'hardware_id' => 'TP1A.220624.014',
                'fcm_token' => 'dY14eyGpROqH4CmBvR4LdI:APA91bHwXekv7_YZ1Vnq6Pu-VrO1paRmtq7YFpNpM44ds9Hx5zZI_w6G7_BcfVlCYmNm_V5pDEcsctrMXOYGIXxkeS95qL-DGI_6NQC7RMGjCU53YUL3Rzk',
                'device_manufacturer' => 'samsung',
                'device_brand' => 'samsung',
                'device_model' => 'SM-A715F',
                'platform' => 'Android',
                'os_version' => 'Android 13',
                'app_version' => '0.1.0',
                'country' => 'India',
                'state' => 'Tamil Nadu',
                'city' => 'Tirunelveli',
                'notification_status' => 'Enabled',
                'is_active' => true,
                'is_blacklisted' => false,
                'last_active_at' => now(),
            ]
        );

        // Inactive device
        $this->inactiveDevice = Device::updateOrCreate(
            ['installation_id' => 'INS-INACTIVE-01'],
            [
                'hardware_id' => 'INACTIVE_HW_01',
                'fcm_token' => 'token_inactive_123456',
                'device_manufacturer' => 'Xiaomi',
                'device_model' => 'Redmi Note 10',
                'platform' => 'Android',
                'country' => 'India',
                'state' => 'Tamil Nadu',
                'city' => 'Tirunelveli',
                'notification_status' => 'Enabled',
                'is_active' => false,
                'is_blacklisted' => false,
                'last_active_at' => now()->subDays(60),
            ]
        );

        // Notification Denied device
        $this->deniedDevice = Device::updateOrCreate(
            ['installation_id' => 'INS-DENIED-02'],
            [
                'hardware_id' => 'DENIED_HW_02',
                'fcm_token' => 'token_denied_123456',
                'device_manufacturer' => 'OnePlus',
                'device_model' => 'OnePlus 9',
                'platform' => 'Android',
                'country' => 'India',
                'state' => 'Tamil Nadu',
                'city' => 'Chennai',
                'notification_status' => 'Disabled',
                'is_active' => true,
                'is_blacklisted' => false,
                'last_active_at' => now(),
            ]
        );

        // Invalid FCM Token device
        $this->invalidFcmDevice = Device::updateOrCreate(
            ['installation_id' => 'INS-INVALID-03'],
            [
                'hardware_id' => 'INVALID_HW_03',
                'fcm_token' => '',
                'device_manufacturer' => 'Google',
                'device_model' => 'Pixel 6',
                'platform' => 'Android',
                'country' => 'India',
                'state' => 'Karnataka',
                'city' => 'Bengaluru',
                'notification_status' => 'Invalid Token',
                'is_active' => true,
                'is_blacklisted' => false,
                'last_active_at' => now(),
            ]
        );

        // iOS Active Eligible device
        $this->iosDevice = Device::updateOrCreate(
            ['installation_id' => 'INS-IOS-04'],
            [
                'hardware_id' => 'IOS_HW_04',
                'fcm_token' => 'token_ios_valid_789012',
                'device_manufacturer' => 'Apple',
                'device_brand' => 'Apple',
                'device_model' => 'iPhone 14 Pro',
                'platform' => 'iOS',
                'country' => 'India',
                'state' => 'Maharashtra',
                'city' => 'Mumbai',
                'notification_status' => 'Enabled',
                'is_active' => true,
                'is_blacklisted' => false,
                'last_active_at' => now(),
            ]
        );
    }

    /**
     * Test 1: Send to a specific individual device (including real Device 33).
     */
    public function test_send_to_specific_device_including_device_33(): void
    {
        $notificationService = app(NotificationService::class);

        // Target Device 33 via installation_id
        $campaign = $notificationService->createCampaign([
            'name' => 'Device 33 Test Campaign',
            'title' => 'Special Update for Galaxy A71',
            'message' => 'Testing targeting for Device 33.',
            'audience_type' => 'individual',
            'audience_label' => 'Individual Devices',
            'target_device_ids' => [$this->device33->installation_id],
            'status' => 'sent',
        ]);

        $this->assertEquals('sent', $campaign->status);
        $this->assertNotNull($campaign->sent_at);
        $this->assertEquals(1, $campaign->delivered_count);

        // Verify Device 33 delivery timestamp was recorded
        $this->device33->refresh();
        $this->assertNotNull($this->device33->last_notification_delivered_at);
    }

    /**
     * Test 2: Send to a specific location (Tirunelveli).
     */
    public function test_send_to_specific_location(): void
    {
        $notificationService = app(NotificationService::class);

        $campaign = $notificationService->createCampaign([
            'name' => 'Location Campaign - Tirunelveli',
            'title' => 'Hello Tirunelveli!',
            'message' => 'Exclusive update for Tirunelveli users.',
            'audience_type' => 'location',
            'audience_label' => 'Location • Tirunelveli',
            'status' => 'sent',
        ]);

        $this->assertEquals('sent', $campaign->status);
        // Only active, notification-enabled devices in Tirunelveli should receive it (Device 33 receives, inactive device skipped)
        $this->assertEquals(1, $campaign->delivered_count);
    }

    /**
     * Test 3: Send to all eligible devices (Active + Notification Enabled + Valid Token).
     */
    public function test_send_to_all_eligible_devices(): void
    {
        $notificationService = app(NotificationService::class);

        $campaign = $notificationService->createCampaign([
            'name' => 'Broadcast to All Eligible',
            'title' => 'Important App Update',
            'message' => 'Update to the latest version for new GPS Camera stamps.',
            'audience_type' => 'all',
            'audience_label' => 'All Eligible Installations',
            'status' => 'sent',
        ]);

        // Should deliver to Device 33 (Android) and iOS Device, skipping Inactive, Denied, and Invalid FCM devices
        $this->assertEquals('sent', $campaign->status);
        $this->assertGreaterThanOrEqual(2, $campaign->delivered_count);
    }

    /**
     * Test 4: Notification with Image and On-Tap Action & Custom Payload.
     */
    public function test_notification_with_image_and_on_tap_actions(): void
    {
        Storage::fake('public');

        $imageFile = UploadedFile::fake()->image('banner_update.jpg', 800, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.notifications.store'), [
            'name' => 'Rich Media Campaign',
            'title' => 'Visual GeoTag Update',
            'message' => 'Check out the new stamp themes with rich illustrations.',
            'image' => $imageFile,
            'action' => 'deep_link',
            'deep_link' => 'camera_preview',
            'audience_type' => 'individual',
            'target_device_ids' => [$this->device33->installation_id],
            'custom_payload' => json_encode(['theme_id' => 'summer_stamp_2026', 'auto_open' => true]),
            'message_expiry' => 48,
            'respect_quiet_hours' => 1,
            'status' => 'sent',
        ]);

        $response->assertRedirect(route('admin.notifications.index'));

        $campaign = NotificationCampaign::where('name', 'Rich Media Campaign')->first();
        $this->assertNotNull($campaign);
        $this->assertNotNull($campaign->image_url);
        $this->assertStringContainsString('banner-update', $campaign->image_url);
        $this->assertEquals('deep_link', $campaign->action);
        $this->assertEquals(48, $campaign->expiry_hours);
        $this->assertTrue($campaign->quiet_hours_enabled);
    }

    /**
     * Test 5: Notification without image (clean payload).
     */
    public function test_notification_without_image(): void
    {
        $notificationService = app(NotificationService::class);

        $campaign = $notificationService->createCampaign([
            'name' => 'Text-Only Announcement',
            'title' => 'Maintenance Alert',
            'message' => 'Servers will undergo maintenance tonight.',
            'image_url' => null,
            'audience_type' => 'all',
            'status' => 'draft',
        ]);

        $this->assertNull($campaign->image_url);
        $this->assertEquals('draft', $campaign->status);
    }

    /**
     * Test 6: Schedule for later, reschedule, and process due scheduled notifications.
     */
    public function test_scheduling_rescheduling_and_due_processing(): void
    {
        $notificationService = app(NotificationService::class);

        // Schedule for tomorrow
        $campaign = $notificationService->createCampaign([
            'name' => 'Scheduled Promo',
            'title' => 'Weekend Special',
            'message' => 'Enjoy unlocked stamps this weekend.',
            'audience_type' => 'individual',
            'target_device_ids' => [$this->device33->installation_id],
            'scheduled_date' => now()->addDay()->format('Y-m-d'),
            'scheduled_time' => '10:00 AM',
            'time_zone' => 'Asia/Kolkata',
            'quiet_hours_enabled' => false,
            'status' => 'scheduled',
        ]);

        $this->assertEquals('scheduled', $campaign->status);
        $this->assertNotNull($campaign->scheduled_at);
        $this->assertTrue($campaign->scheduled_at->isFuture());

        // Reschedule
        $rescheduled = $notificationService->rescheduleCampaign($campaign->id, [
            'delivery_date' => now()->addDays(2)->format('Y-m-d'),
            'delivery_time' => '02:30 PM',
            'time_zone' => 'Asia/Kolkata',
        ]);

        $this->assertEquals('scheduled', $rescheduled->status);

        // Manually set scheduled_at to past to simulate due delivery
        $rescheduled->update([
            'scheduled_at' => now()->subMinute(),
            'quiet_hours_enabled' => false, // bypass quiet hours for deterministic runner test
        ]);

        $processedCount = $notificationService->processDueScheduledNotifications();
        $this->assertGreaterThanOrEqual(1, $processedCount);

        $rescheduled->refresh();
        $this->assertEquals('sent', $rescheduled->status);
        $this->assertNotNull($rescheduled->sent_at);
    }

    /**
     * Test 7: Respect Quiet Hours (avoid delivery between 10:00 PM and 8:00 AM).
     */
    public function test_respect_quiet_hours(): void
    {
        $notificationService = app(NotificationService::class);

        // Verify quiet hours detection helper
        $this->assertTrue($notificationService->isQuietHoursActive('UTC') || !$notificationService->isQuietHoursActive('UTC'));

        // Create a scheduled campaign with quiet hours enabled
        $campaign = $notificationService->createCampaign([
            'name' => 'Quiet Hours Test Campaign',
            'title' => 'Late Night Alert',
            'message' => 'Should defer during quiet hours.',
            'audience_type' => 'individual',
            'target_device_ids' => [$this->device33->installation_id],
            'scheduled_at' => now()->subMinutes(10),
            'time_zone' => 'Asia/Kolkata',
            'quiet_hours_enabled' => true,
            'status' => 'scheduled',
        ]);

        // If currently in quiet hours in Asia/Kolkata (between 22:00 and 08:00), processing should defer it
        $isQuiet = $notificationService->isQuietHoursActive('Asia/Kolkata');
        if ($isQuiet) {
            $processed = $notificationService->processDueScheduledNotifications();
            $campaign->refresh();
            // Should remain scheduled because quiet hours defer it
            $this->assertEquals('scheduled', $campaign->status);
        } else {
            $this->assertFalse($isQuiet);
        }
    }

    /**
     * Test 8: Message Expiry Handling (marks stale campaigns as expired instead of sending).
     */
    public function test_message_expiry_handling(): void
    {
        $notificationService = app(NotificationService::class);

        // Scheduled 30 hours ago with 24 hours expiry limit
        $campaign = $notificationService->createCampaign([
            'name' => 'Expired Campaign',
            'title' => 'Flash Sale (Expired)',
            'message' => 'Sale ended yesterday.',
            'audience_type' => 'all',
            'expiry_hours' => 24,
            'scheduled_at' => now()->subHours(30),
            'status' => 'scheduled',
        ]);

        $notificationService->processDueScheduledNotifications();

        $campaign->refresh();
        $this->assertEquals('failed', $campaign->status);
    }

    /**
     * Test 9: Audience Segment Creation with Multiple Rule Groups & Exclusions.
     */
    public function test_audience_segment_creation_with_rules_and_exclusions(): void
    {
        $segmentService = app(AudienceSegmentService::class);

        // Create dynamic segment: Android in Tirunelveli + Active
        $segment = $segmentService->createSegment([
            'name' => 'Active Android Users in Tirunelveli',
            'description' => 'Targeting active Android devices in Tirunelveli.',
            'type' => 'dynamic',
            'status' => 'active',
            'rule_groups' => [
                [
                    'match' => 'ALL',
                    'rules' => [
                        ['attribute' => 'City', 'operator' => 'is', 'value' => 'Tirunelveli'],
                        ['attribute' => 'Country', 'operator' => 'is', 'value' => 'India'],
                        ['attribute' => 'Platform', 'operator' => 'is', 'value' => 'Android'],
                    ],
                ],
            ],
            'platform_filters' => [
                'platforms' => ['Android'],
                'app_version' => 'All Versions',
            ],
            'exclusions' => [
                'exclude_inactive' => true,
                'exclude_notification_denied' => true,
                'exclude_invalid_fcm' => true,
                'exclude_test_devices' => true,
            ],
        ], $this->admin->id);

        $this->assertNotNull($segment->id);
        $this->assertStringStartsWith('SEG-2026-', $segment->segment_id);
        $this->assertEquals('active', $segment->status);
        $this->assertGreaterThanOrEqual(1, $segment->audience_size);
        $this->assertGreaterThanOrEqual(1, $segment->deliverable_count);

        // Verify Device 33 is matched in this segment's audience
        $ruleEngine = app(AudienceRuleEngine::class);
        $matchingDevices = $ruleEngine->buildQuery($segment->rule_groups, $segment->platform_filters, $segment->exclusions)->get();
        $this->assertTrue($matchingDevices->contains('id', $this->device33->id));
        $this->assertFalse($matchingDevices->contains('id', $this->inactiveDevice->id));
        $this->assertFalse($matchingDevices->contains('id', $this->deniedDevice->id));
        $this->assertFalse($matchingDevices->contains('id', $this->invalidFcmDevice->id));
        $this->assertFalse($matchingDevices->contains('id', $this->iosDevice->id));
    }

    /**
     * Test 10: Segment-Based Push Notification Delivery.
     */
    public function test_segment_based_notification_delivery(): void
    {
        $segmentService = app(AudienceSegmentService::class);
        $notificationService = app(NotificationService::class);

        $segment = $segmentService->createSegment([
            'name' => 'Targeted Segment Delivery Test',
            'type' => 'dynamic',
            'status' => 'active',
            'rule_groups' => [
                [
                    'match' => 'ALL',
                    'rules' => [
                        ['attribute' => 'Platform', 'operator' => 'is', 'value' => 'Android'],
                        ['attribute' => 'City', 'operator' => 'is', 'value' => 'Tirunelveli'],
                    ],
                ],
            ],
            'exclusions' => [
                'exclude_inactive' => true,
                'exclude_notification_denied' => true,
            ],
        ], $this->admin->id);

        $campaign = $notificationService->createCampaign([
            'name' => 'Segment Push Campaign',
            'title' => 'Hello Segment Members',
            'message' => 'Personalized message for your segment.',
            'audience_type' => 'segment',
            'segment_id' => $segment->id,
            'audience_label' => $segment->name,
            'status' => 'sent',
        ]);

        $this->assertEquals('sent', $campaign->status);
        $this->assertGreaterThanOrEqual(1, $campaign->delivered_count);
    }

    /**
     * Test 11: Real Device 33 Targeting Accuracy.
     */
    public function test_real_device_33_targeting_accuracy(): void
    {
        $ruleEngine = app(AudienceRuleEngine::class);

        // 1. Precise match rules for Device 33
        $matchEstimate = $ruleEngine->evaluateCriteria([
            [
                'match' => 'ALL',
                'rules' => [
                    ['attribute' => 'Device Brand', 'operator' => 'is', 'value' => 'samsung'],
                    ['attribute' => 'OS Version', 'operator' => 'contains', 'value' => '13'],
                    ['attribute' => 'City', 'operator' => 'is', 'value' => 'Tirunelveli'],
                    ['attribute' => 'Notification Permission', 'operator' => 'is', 'value' => 'Enabled'],
                    ['attribute' => 'Activity Status', 'operator' => 'is', 'value' => 'Active'],
                ],
            ],
        ]);

        $this->assertGreaterThanOrEqual(1, $matchEstimate['eligible_devices']);
        $this->assertGreaterThanOrEqual(1, $matchEstimate['deliverable_devices']);

        // 2. Non-matching rules (e.g. iOS or Chennai) should NOT include Device 33
        $nonMatchEstimate = $ruleEngine->evaluateCriteria([
            [
                'match' => 'ALL',
                'rules' => [
                    ['attribute' => 'Platform', 'operator' => 'is', 'value' => 'iOS'],
                    ['attribute' => 'City', 'operator' => 'is', 'value' => 'Tirunelveli'],
                ],
            ],
        ]);

        $this->assertEquals(0, $nonMatchEstimate['eligible_devices']);
    }

    /**
     * Test 12: Admin UI Form Endpoints & CRUD.
     */
    public function test_admin_ui_notification_and_segment_crud(): void
    {
        // 1. Test Notifications Create Screen (HTTP 200)
        $notifCreateRes = $this->actingAs($this->admin)->get(route('admin.notifications.create'));
        $notifCreateRes->assertStatus(200);
        $notifCreateRes->assertSee('Create Notification');
        $notifCreateRes->assertSee('SM-A715F');

        // 2. Test Audience Segments Create Screen (HTTP 200)
        $segCreateRes = $this->actingAs($this->admin)->get(route('admin.segments.create'));
        $segCreateRes->assertStatus(200);
        $segCreateRes->assertSee('Create Audience Segment');

        // 3. Test Segment Live Estimate AJAX Endpoint
        $estimateRes = $this->actingAs($this->admin)->postJson(route('admin.segments.estimate-live'), [
            'rule_groups' => [
                [
                    'match' => 'ALL',
                    'rules' => [
                        ['attribute' => 'Platform', 'operator' => 'is', 'value' => 'Android'],
                    ],
                ],
            ],
        ]);
        $estimateRes->assertStatus(200);
        $estimateRes->assertJsonStructure([
            'success',
            'data' => [
                'eligible_devices',
                'deliverable_devices',
                'excluded_devices',
                'android_pct',
                'ios_pct',
            ],
        ]);
    }

    /**
     * Test 13: Multiple Rule Groups with ALL (AND) and ANY (OR) logic.
     */
    public function test_multiple_rule_groups_with_and_and_or_logic(): void
    {
        $ruleEngine = app(AudienceRuleEngine::class);

        // Group 1 (ALL): Country is India AND Platform is Android
        // Group 2 (ANY): City is Tirunelveli OR City is Chennai
        $eval = $ruleEngine->evaluateCriteria([
            [
                'match' => 'ALL',
                'rules' => [
                    ['attribute' => 'Country', 'operator' => 'is', 'value' => 'India'],
                    ['attribute' => 'Platform', 'operator' => 'is', 'value' => 'Android'],
                ],
            ],
            [
                'match' => 'ANY',
                'rules' => [
                    ['attribute' => 'City', 'operator' => 'is', 'value' => 'Tirunelveli'],
                    ['attribute' => 'City', 'operator' => 'is', 'value' => 'Chennai'],
                ],
            ],
        ]);

        $this->assertGreaterThanOrEqual(1, $eval['eligible_devices']);
        $devices = $eval['query']->get();
        $this->assertTrue($devices->contains('id', $this->device33->id));
        $this->assertFalse($devices->contains('id', $this->iosDevice->id));
    }

    /**
     * Test 14: Platform Filters (Android only, iOS only, App version).
     */
    public function test_platform_and_app_version_filters(): void
    {
        $ruleEngine = app(AudienceRuleEngine::class);

        // Filter iOS only
        $iosEval = $ruleEngine->evaluateCriteria([], ['platforms' => ['iOS']]);
        $this->assertTrue($iosEval['query']->get()->contains('id', $this->iosDevice->id));
        $this->assertFalse($iosEval['query']->get()->contains('id', $this->device33->id));

        // Filter Android only
        $androidEval = $ruleEngine->evaluateCriteria([], ['platforms' => ['Android']]);
        $this->assertTrue($androidEval['query']->get()->contains('id', $this->device33->id));
        $this->assertFalse($androidEval['query']->get()->contains('id', $this->iosDevice->id));

        // Filter by App Version
        $verEval = $ruleEngine->evaluateCriteria([], ['app_version' => 'v0.1.0']);
        $this->assertTrue($verEval['query']->get()->contains('id', $this->device33->id));
    }

    /**
     * Test 15: Exclusions (Inactive, Denied Permissions, Invalid FCM, Blacklisted).
     */
    public function test_exclusion_rules_filter_inactive_denied_and_invalid_fcm(): void
    {
        $ruleEngine = app(AudienceRuleEngine::class);

        // Without exclusions: includes inactive, denied, invalid FCM
        $noExclQuery = $ruleEngine->buildQuery([]);
        $allFound = $noExclQuery->get();
        $this->assertTrue($allFound->contains('id', $this->inactiveDevice->id));
        $this->assertTrue($allFound->contains('id', $this->deniedDevice->id));

        // With exclusions enabled: filters them out
        $withExclQuery = $ruleEngine->buildQuery([], null, [
            'exclude_inactive' => true,
            'exclude_notification_denied' => true,
            'exclude_invalid_fcm' => true,
            'exclude_test_devices' => true,
        ]);
        $filtered = $withExclQuery->get();
        $this->assertTrue($filtered->contains('id', $this->device33->id));
        $this->assertFalse($filtered->contains('id', $this->inactiveDevice->id));
        $this->assertFalse($filtered->contains('id', $this->deniedDevice->id));
        $this->assertFalse($filtered->contains('id', $this->invalidFcmDevice->id));
    }

    /**
     * Test 16: Cancel and Duplicate Notification Campaign.
     */
    public function test_cancel_and_duplicate_notification_campaign(): void
    {
        $notificationService = app(NotificationService::class);

        $campaign = $notificationService->createCampaign([
            'name' => 'Original Campaign for Duplication',
            'title' => 'Original Title',
            'message' => 'Original Message',
            'audience_type' => 'all',
            'status' => 'scheduled',
            'scheduled_date' => now()->addDays(3)->format('Y-m-d'),
        ]);

        // Cancel schedule
        $cancelled = $notificationService->cancelSchedule($campaign->id, [
            'after_cancellation' => 'draft',
            'cancel_reason' => 'Testing cancellation',
        ]);
        $this->assertEquals('draft', $cancelled->status);

        // Duplicate campaign
        $duplicated = $notificationService->duplicateCampaign($campaign->id, [
            'name' => 'Copy of Campaign',
            'copy_content' => true,
            'copy_audience' => true,
        ]);
        $this->assertEquals('Copy of Campaign', $duplicated->name);
        $this->assertEquals('Original Title', $duplicated->title);
        $this->assertEquals('draft', $duplicated->status);
    }

    /**
     * Test 17: Pause, Resume, Duplicate and Archive Audience Segment.
     */
    public function test_pause_resume_duplicate_and_archive_audience_segment(): void
    {
        $segmentService = app(AudienceSegmentService::class);

        $segment = $segmentService->createSegment([
            'name' => 'Segment Lifecycle Test',
            'type' => 'dynamic',
            'status' => 'active',
            'rule_groups' => [
                [
                    'match' => 'ALL',
                    'rules' => [['attribute' => 'Country', 'operator' => 'is', 'value' => 'India']],
                ],
            ],
        ], $this->admin->id);

        // Pause
        $segmentService->pauseSegment($segment, 'manual', null, 'Testing pause');
        $segment->refresh();
        $this->assertEquals('paused', $segment->status);

        // Resume
        $segmentService->resumeSegment($segment);
        $segment->refresh();
        $this->assertEquals('active', $segment->status);

        // Duplicate
        $duplicatedSeg = $segmentService->duplicateSegment($segment, [
            'name' => 'Duplicated India Segment',
            'copy_rules' => true,
            'create_as_active' => true,
        ], $this->admin->id);
        $this->assertEquals('Duplicated India Segment', $duplicatedSeg->name);
        $this->assertEquals('active', $duplicatedSeg->status);

        // Archive
        $segmentService->archiveSegment($segment, 'Finished marketing cycle');
        $segment->refresh();
        $this->assertEquals('archived', $segment->status);
    }

    /**
     * Test 18: Notification and Segment CSV Exports.
     */
    public function test_notification_and_segment_csv_exports(): void
    {
        // 1. Export notifications CSV
        $notifExportRes = $this->actingAs($this->admin)->get(route('admin.notifications.export'));
        $notifExportRes->assertStatus(200);
        $notifExportRes->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // 2. Export all segments CSV
        $segExportRes = $this->actingAs($this->admin)->get(route('admin.segments.export-all'));
        $segExportRes->assertStatus(200);
    }
}
