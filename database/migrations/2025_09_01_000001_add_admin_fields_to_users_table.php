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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('isAdmin')->default(false)->after('password');
            $table->string('utype', 50)->nullable()->after('isAdmin');
            
            // Foreign key to admin_user_types table
            $table->foreign('utype')
                ->references('code')
                ->on('admin_user_types')
                ->onDelete('set null');
            
            // Index for performance
            $table->index('isAdmin');
            $table->index('utype');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['utype']);
            $table->dropIndex(['isAdmin']);
            $table->dropIndex(['utype']);
            $table->dropColumn(['isAdmin', 'utype']);
        });
    }
};