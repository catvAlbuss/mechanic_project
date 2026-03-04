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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_company')->references('id')->on('companies')->onDelete('cascade');
            $table->string('ruc', 11);
            $table->string('company_name');
            $table->string('address');
            $table->string('email');
            $table->string('contact', 20);
            $table->enum('state',['active', 'inactive'])->default('active');
            $table->timestamp('registration_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
