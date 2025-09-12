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
        Schema::create('admin_email_template_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->integer('version');
            $table->string('subject');
            $table->text('body');
            $table->json('variables')->nullable();
            $table->string('layout')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('created_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('template_id')->references('id')->on('admin_emailtemplates')->onDelete('cascade');
            $table->unique(['template_id', 'version']);
            $table->index('is_active');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_email_template_versions');
    }
};