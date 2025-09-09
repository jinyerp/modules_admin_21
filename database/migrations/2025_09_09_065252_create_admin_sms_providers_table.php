<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_sms_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name')->comment('제공업체명 (Vonage, Twilio 등)');
            $table->string('provider_type')->nullable()->comment('제공업체 타입');
            $table->string('api_key')->comment('API 키');
            $table->string('api_secret')->nullable()->comment('API 시크릿');
            $table->string('from_number')->nullable()->comment('발신번호');
            $table->string('from_name')->nullable()->comment('발신자명');
            $table->json('config')->nullable()->comment('추가 설정 (JSON)');
            $table->json('settings')->nullable()->comment('제공업체별 설정');
            $table->boolean('is_active')->default(false)->comment('활성화 상태');
            $table->boolean('is_default')->default(false)->comment('기본 제공업체 여부');
            $table->integer('priority')->default(0)->comment('우선순위');
            $table->text('description')->nullable()->comment('설명');
            $table->string('webhook_url')->nullable()->comment('웹훅 URL');
            $table->integer('sent_count')->default(0)->comment('발송 건수');
            $table->integer('failed_count')->default(0)->comment('실패 건수');
            $table->decimal('balance', 10, 2)->nullable()->comment('잔액');
            $table->timestamp('last_used_at')->nullable()->comment('마지막 사용 시간');
            $table->timestamps();
        });

        // 기본 Vonage 제공업체 등록
        $this->seedInitialProviders();
    }

    /**
     * 초기 SMS 제공업체 데이터 삽입
     */
    private function seedInitialProviders()
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
                'settings' => json_encode([
                    'api_endpoint' => 'https://rest.nexmo.com',
                    'supports_unicode' => true,
                    'max_message_length' => 1600,
                    'supports_delivery_receipt' => true,
                    'pricing_per_sms' => 0.045
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
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
                'settings' => json_encode([
                    'api_endpoint' => 'https://api.twilio.com',
                    'supports_unicode' => true,
                    'max_message_length' => 1600,
                    'supports_mms' => true,
                    'pricing_per_sms' => 0.0075
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
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
                'description' => 'Amazon Simple Notification Service',
                'settings' => json_encode([
                    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
                    'supports_unicode' => true,
                    'pricing_per_sms' => 0.00645
                ]),
                'sent_count' => 0,
                'failed_count' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('admin_sms_providers')->insert($providers);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_sms_providers');
    }
};