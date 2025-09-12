<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_email_ab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('template_id');
            $table->json('variants'); // Array of version IDs with percentages
            $table->enum('status', ['draft', 'running', 'completed', 'cancelled'])->default('draft');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('sample_size')->nullable();
            $table->json('metrics')->nullable(); // open_rate, click_rate, conversion_rate
            $table->unsignedBigInteger('winner_version_id')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
            
            $table->foreign('template_id')->references('id')->on('admin_emailtemplates')->onDelete('cascade');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_email_ab_tests');
    }
};