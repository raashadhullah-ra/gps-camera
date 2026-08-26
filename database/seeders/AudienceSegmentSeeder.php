<?php

namespace Database\Seeders;

use App\Models\AudienceSegment;
use App\Models\Device;
use App\Services\AudienceSegmentService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AudienceSegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $service = app(AudienceSegmentService::class);

        $sampleSegments = [
            [
                'name'        => 'Tirunelveli Active Users',
                'description' => 'Active GPS Camera users in Tirunelveli with notifications enabled.',
                'type'        => 'dynamic',
                'status'      => 'active',
                'rule_groups' => [
                    [
                        'match' => 'ALL',
                        'rules' => [
                            ['attribute' => 'Country', 'operator' => 'is', 'value' => 'India'],
                            ['attribute' => 'State / Region', 'operator' => 'is', 'value' => 'Tamil Nadu'],
                            ['attribute' => 'City', 'operator' => 'is', 'value' => 'Tirunelveli'],
                            ['attribute' => 'Activity Status', 'operator' => 'is', 'value' => 'Active'],
                            ['attribute' => 'Last Active', 'operator' => 'within', 'value' => '30 days'],
                            ['attribute' => 'Notification Permission', 'operator' => 'is', 'value' => 'Enabled'],
                        ],
                    ],
                ],
                'platform_filters' => [
                    'platforms'           => ['Android', 'iOS'],
                    'app_version'         => 'All Versions',
                    'location_permission' => 'Any',
                ],
                'exclusions' => [
                    'exclude_inactive'            => true,
                    'exclude_invalid_fcm'         => true,
                    'exclude_notification_denied' => true,
                ],
            ],
            [
                'name'        => 'India Android Users',
                'description' => 'All Android users across India with notifications turned on.',
                'type'        => 'dynamic',
                'status'      => 'active',
                'rule_groups' => [
                    [
                        'match' => 'ALL',
                        'rules' => [
                            ['attribute' => 'Country', 'operator' => 'is', 'value' => 'India'],
                            ['attribute' => 'Platform', 'operator' => 'is', 'value' => 'Android'],
                            ['attribute' => 'Activity Status', 'operator' => 'is', 'value' => 'Active'],
                            ['attribute' => 'Notification Permission', 'operator' => 'is', 'value' => 'Enabled'],
                        ],
                    ],
                ],
                'platform_filters' => [
                    'platforms' => ['Android'],
                ],
            ],
            [
                'name'        => 'New Users - Last 7 Days',
                'description' => 'Devices first installed in the last 7 days.',
                'type'        => 'dynamic',
                'status'      => 'active',
                'rule_groups' => [
                    [
                        'match' => 'ALL',
                        'rules' => [
                            ['attribute' => 'First Open', 'operator' => 'within', 'value' => '7 days'],
                            ['attribute' => 'Activity Status', 'operator' => 'is', 'value' => 'Active'],
                        ],
                    ],
                ],
            ],
            [
                'name'        => 'Tamil Nadu GPS Users',
                'description' => 'Users in Tamil Nadu with precise location granted.',
                'type'        => 'dynamic',
                'status'      => 'active',
                'rule_groups' => [
                    [
                        'match' => 'ALL',
                        'rules' => [
                            ['attribute' => 'State / Region', 'operator' => 'is', 'value' => 'Tamil Nadu'],
                            ['attribute' => 'Location Permission', 'operator' => 'is', 'value' => 'Precise'],
                        ],
                    ],
                ],
            ],
            [
                'name'        => 'App Version v1.4.2',
                'description' => 'Active devices running latest app build 1.4.2.',
                'type'        => 'dynamic',
                'status'      => 'active',
                'rule_groups' => [
                    [
                        'match' => 'ALL',
                        'rules' => [
                            ['attribute' => 'App Version', 'operator' => 'is', 'value' => '1.4.2'],
                            ['attribute' => 'Activity Status', 'operator' => 'is', 'value' => 'Active'],
                        ],
                    ],
                ],
            ],
            [
                'name'        => 'Inactive 30+ Days',
                'description' => 'Users who have not opened the app in the last month.',
                'type'        => 'dynamic',
                'status'      => 'paused',
                'rule_groups' => [
                    [
                        'match' => 'ALL',
                        'rules' => [
                            ['attribute' => 'Last Active', 'operator' => 'not within', 'value' => '30 days'],
                        ],
                    ],
                ],
            ],
            [
                'name'        => 'iOS Notification Users',
                'description' => 'Apple iOS devices with active push tokens.',
                'type'        => 'static',
                'status'      => 'active',
                'rule_groups' => [
                    [
                        'match' => 'ALL',
                        'rules' => [
                            ['attribute' => 'Platform', 'operator' => 'is', 'value' => 'iOS'],
                            ['attribute' => 'Notification Permission', 'operator' => 'is', 'value' => 'Enabled'],
                        ],
                    ],
                ],
            ],
            [
                'name'        => 'Campaign Test Audience',
                'description' => 'Internal test devices for notification testing.',
                'type'        => 'static',
                'status'      => 'draft',
                'rule_groups' => [
                    [
                        'match' => 'ALL',
                        'rules' => [
                            ['attribute' => 'Activity Status', 'operator' => 'is', 'value' => 'Active'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($sampleSegments as $data) {
            $service->createSegment($data);
        }
    }
}
