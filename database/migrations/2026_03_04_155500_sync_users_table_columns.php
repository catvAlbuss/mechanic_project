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
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'id_company')) {
                $table->unsignedBigInteger('id_company')->nullable()->index();
            }

            if (! Schema::hasColumn('users', 'dni')) {
                $table->string('dni')->nullable();
            }

            if (! Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname')->nullable();
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }

            if (! Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable();
            }

            if (! Schema::hasColumn('users', 'registration_date')) {
                $table->timestamp('registration_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'registration_date')) {
                $table->dropColumn('registration_date');
            }

            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('users', 'lastname')) {
                $table->dropColumn('lastname');
            }

            if (Schema::hasColumn('users', 'dni')) {
                $table->dropColumn('dni');
            }

            if (Schema::hasColumn('users', 'id_company')) {
                $table->dropColumn('id_company');
            }
        });
    }
};
