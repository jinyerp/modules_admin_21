<?php

namespace Jiny\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminSmsProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'provider_name' => 'Vonage (Nexmo)',
                'provider_type' => 'vonage',
                'api_key' => env('VONAGE_API_KEY', ''),
                'api_secret' => env('VONAGE_API_SECRET', ''),
                'from_number' => env('VONAGE_SMS_FROM', ''),
                'from_name' => 'Vonage SMS',
                'is_active' => !empty(env('VONAGE_API_KEY')),
                'is_default' => true,
                'description' => 'Vonage (구 Nexmo) SMS API - 글로벌 SMS 서비스 제공업체',
                'webhook_url' => null,
                'settings' => json_encode([
                    'api_endpoint' => 'https://rest.nexmo.com',
                    'supports_unicode' => true,
                    'max_message_length' => 1600,
                    'supports_delivery_receipt' => true,
                    'pricing_per_sms' => 0.045
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'provider_name' => 'Twilio',
                'provider_type' => 'twilio',
                'api_key' => env('TWILIO_SID', ''),
                'api_secret' => env('TWILIO_AUTH_TOKEN', ''),
                'from_number' => env('TWILIO_FROM', ''),
                'from_name' => 'Twilio SMS',
                'is_active' => false,
                'is_default' => false,
                'description' => 'Twilio SMS API - 클라우드 통신 플랫폼',
                'webhook_url' => null,
                'settings' => json_encode([
                    'api_endpoint' => 'https://api.twilio.com',
                    'supports_unicode' => true,
                    'max_message_length' => 1600,
                    'supports_delivery_receipt' => true,
                    'supports_mms' => true,
                    'pricing_per_sms' => 0.0075
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'provider_name' => 'AWS SNS',
                'provider_type' => 'aws_sns',
                'api_key' => env('AWS_ACCESS_KEY_ID', ''),
                'api_secret' => env('AWS_SECRET_ACCESS_KEY', ''),
                'from_number' => env('AWS_SNS_FROM', ''),
                'from_name' => 'AWS SNS',
                'is_active' => false,
                'is_default' => false,
                'description' => 'Amazon Simple Notification Service - AWS 메시징 서비스',
                'webhook_url' => null,
                'settings' => json_encode([
                    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
                    'supports_unicode' => true,
                    'max_message_length' => 1600,
                    'supports_delivery_receipt' => true,
                    'pricing_per_sms' => 0.00645,
                    'topic_arn' => env('AWS_SNS_TOPIC_ARN', '')
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'provider_name' => 'MessageBird',
                'provider_type' => 'messagebird',
                'api_key' => env('MESSAGEBIRD_ACCESS_KEY', ''),
                'api_secret' => '',
                'from_number' => env('MESSAGEBIRD_FROM', ''),
                'from_name' => 'MessageBird',
                'is_active' => false,
                'is_default' => false,
                'description' => 'MessageBird - 유럽 기반 글로벌 SMS 서비스',
                'webhook_url' => null,
                'settings' => json_encode([
                    'api_endpoint' => 'https://rest.messagebird.com',
                    'supports_unicode' => true,
                    'max_message_length' => 1377,
                    'supports_delivery_receipt' => true,
                    'pricing_per_sms' => 0.043
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'provider_name' => 'Clickatell',
                'provider_type' => 'clickatell',
                'api_key' => env('CLICKATELL_API_KEY', ''),
                'api_secret' => '',
                'from_number' => env('CLICKATELL_FROM', ''),
                'from_name' => 'Clickatell',
                'is_active' => false,
                'is_default' => false,
                'description' => 'Clickatell - 엔터프라이즈 SMS 솔루션',
                'webhook_url' => null,
                'settings' => json_encode([
                    'api_endpoint' => 'https://platform.clickatell.com',
                    'supports_unicode' => true,
                    'max_message_length' => 1530,
                    'supports_delivery_receipt' => true,
                    'pricing_per_sms' => 0.038
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'provider_name' => 'Plivo',
                'provider_type' => 'plivo',
                'api_key' => env('PLIVO_AUTH_ID', ''),
                'api_secret' => env('PLIVO_AUTH_TOKEN', ''),
                'from_number' => env('PLIVO_FROM', ''),
                'from_name' => 'Plivo',
                'is_active' => false,
                'is_default' => false,
                'description' => 'Plivo - 클라우드 통신 플랫폼',
                'webhook_url' => null,
                'settings' => json_encode([
                    'api_endpoint' => 'https://api.plivo.com',
                    'supports_unicode' => true,
                    'max_message_length' => 1600,
                    'supports_delivery_receipt' => true,
                    'supports_mms' => true,
                    'pricing_per_sms' => 0.0035
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'provider_name' => 'Sinch',
                'provider_type' => 'sinch',
                'api_key' => env('SINCH_SERVICE_PLAN_ID', ''),
                'api_secret' => env('SINCH_API_TOKEN', ''),
                'from_number' => env('SINCH_FROM', ''),
                'from_name' => 'Sinch',
                'is_active' => false,
                'is_default' => false,
                'description' => 'Sinch - 스웨덴 기반 통신 API 플랫폼',
                'webhook_url' => null,
                'settings' => json_encode([
                    'api_endpoint' => 'https://sms.api.sinch.com',
                    'supports_unicode' => true,
                    'max_message_length' => 1600,
                    'supports_delivery_receipt' => true,
                    'pricing_per_sms' => 0.042
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'provider_name' => 'Textlocal',
                'provider_type' => 'textlocal',
                'api_key' => env('TEXTLOCAL_API_KEY', ''),
                'api_secret' => '',
                'from_number' => env('TEXTLOCAL_FROM', ''),
                'from_name' => 'Textlocal',
                'is_active' => false,
                'is_default' => false,
                'description' => 'Textlocal - 영국/인도 SMS 서비스',
                'webhook_url' => null,
                'settings' => json_encode([
                    'api_endpoint' => 'https://api.textlocal.in',
                    'supports_unicode' => true,
                    'max_message_length' => 918,
                    'supports_delivery_receipt' => true,
                    'pricing_per_sms' => 0.025
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'provider_name' => 'Bandwidth',
                'provider_type' => 'bandwidth',
                'api_key' => env('BANDWIDTH_API_TOKEN', ''),
                'api_secret' => env('BANDWIDTH_API_SECRET', ''),
                'from_number' => env('BANDWIDTH_FROM', ''),
                'from_name' => 'Bandwidth',
                'is_active' => false,
                'is_default' => false,
                'description' => 'Bandwidth - 미국 기반 통신 API 플랫폼',
                'webhook_url' => null,
                'settings' => json_encode([
                    'api_endpoint' => 'https://messaging.bandwidth.com',
                    'account_id' => env('BANDWIDTH_ACCOUNT_ID', ''),
                    'application_id' => env('BANDWIDTH_APPLICATION_ID', ''),
                    'supports_unicode' => true,
                    'max_message_length' => 2048,
                    'supports_delivery_receipt' => true,
                    'supports_mms' => true,
                    'pricing_per_sms' => 0.005
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'provider_name' => 'Infobip',
                'provider_type' => 'infobip',
                'api_key' => env('INFOBIP_API_KEY', ''),
                'api_secret' => '',
                'from_number' => env('INFOBIP_FROM', ''),
                'from_name' => 'Infobip',
                'is_active' => false,
                'is_default' => false,
                'description' => 'Infobip - 글로벌 클라우드 통신 플랫폼',
                'webhook_url' => null,
                'settings' => json_encode([
                    'api_endpoint' => 'https://api.infobip.com',
                    'supports_unicode' => true,
                    'max_message_length' => 1600,
                    'supports_delivery_receipt' => true,
                    'supports_mms' => true,
                    'pricing_per_sms' => 0.041
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // 국내 SMS 제공업체 추가
        $koreanProviders = [
            [
                'provider_name' => '알리고 (Aligo)',
                'provider_type' => 'aligo',
                'api_key' => env('ALIGO_API_KEY', ''),
                'api_secret' => env('ALIGO_USER_ID', ''),
                'from_number' => env('ALIGO_FROM', ''),
                'from_name' => 'Aligo SMS',
                'is_active' => false,
                'is_default' => false,
                'description' => '알리고 - 국내 대량 SMS 발송 서비스',
                'webhook_url' => null,
                'settings' => json_encode([
                    'api_endpoint' => 'https://apis.aligo.in',
                    'supports_unicode' => true,
                    'max_message_length' => 2000,
                    'supports_delivery_receipt' => true,
                    'supports_mms' => true,
                    'pricing_per_sms' => 11,
                    'currency' => 'KRW'
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'provider_name' => '솔루션링크 (SolutionLink)',
                'provider_type' => 'solutionlink',
                'api_key' => env('SOLUTIONLINK_API_KEY', ''),
                'api_secret' => '',
                'from_number' => env('SOLUTIONLINK_FROM', ''),
                'from_name' => 'SolutionLink',
                'is_active' => false,
                'is_default' => false,
                'description' => '솔루션링크 - 국내 SMS/LMS/MMS 서비스',
                'webhook_url' => null,
                'settings' => json_encode([
                    'api_endpoint' => 'https://api.coolsms.co.kr',
                    'supports_unicode' => true,
                    'max_message_length' => 2000,
                    'supports_delivery_receipt' => true,
                    'supports_mms' => true,
                    'pricing_per_sms' => 12,
                    'currency' => 'KRW'
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'provider_name' => 'NHN Toast',
                'provider_type' => 'toast',
                'api_key' => env('TOAST_APP_KEY', ''),
                'api_secret' => env('TOAST_SECRET_KEY', ''),
                'from_number' => env('TOAST_FROM', ''),
                'from_name' => 'NHN Toast',
                'is_active' => false,
                'is_default' => false,
                'description' => 'NHN Toast Notification - 국내 클라우드 메시징 서비스',
                'webhook_url' => null,
                'settings' => json_encode([
                    'api_endpoint' => 'https://api-sms.cloud.toast.com',
                    'supports_unicode' => true,
                    'max_message_length' => 2000,
                    'supports_delivery_receipt' => true,
                    'supports_mms' => true,
                    'pricing_per_sms' => 10,
                    'currency' => 'KRW'
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'last_used_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        $allProviders = array_merge($providers, $koreanProviders);

        foreach ($allProviders as $provider) {
            // 이미 존재하는 제공업체는 건너뛰기
            $exists = DB::table('admin_sms_providers')
                ->where('provider_type', $provider['provider_type'])
                ->exists();

            if (!$exists) {
                DB::table('admin_sms_providers')->insert($provider);
            }
        }

        $this->command->info('SMS providers have been seeded successfully!');
        $this->command->info('Total providers added: ' . count($allProviders));
        
        // 환경 변수 설정 안내
        if (empty(env('VONAGE_API_KEY'))) {
            $this->command->warn('Vonage API credentials not found in .env file.');
            $this->command->info('Please add the following to your .env file:');
            $this->command->line('VONAGE_API_KEY=your_api_key');
            $this->command->line('VONAGE_API_SECRET=your_api_secret');
            $this->command->line('VONAGE_SMS_FROM=your_sender_number');
        }
    }
}