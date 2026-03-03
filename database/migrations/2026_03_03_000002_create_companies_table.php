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
            $table->string('avatar');
            $table->string('ruc');
            $table->string('company_name');
            $table->string('address');
            $table->string('district');
            $table->string('province');
            $table->string('department');
            $table->enum('state',['active', 'inactive'])->default('active');
            $table->timestamp('registration_date');
            $table->json('config');
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
