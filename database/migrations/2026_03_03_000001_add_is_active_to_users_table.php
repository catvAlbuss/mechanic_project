<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add an activation flag to users.
     *
     * This allows administrators to disable an account without deleting it.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_active')
                ->default(true)
                ->after('remember_token')
                ->comment('If false the user cannot authenticate');
        });
    }

    /**
     * Rollback activation flag.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_active');
        });
    }
};
