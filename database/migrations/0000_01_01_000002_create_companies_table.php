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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('avatar')->nullable();
            $table->string('ruc')->unique();
            $table->string('company_name', 150);
            $table->string('address', 255);
            $table->string('district', 150);
            $table->string('province', 150);
            $table->string('department', 150);
            $table->enum('state',['active', 'inactive'])->default('active');
            $table->date('registration_date')->nullable();
            $table->json('config')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
