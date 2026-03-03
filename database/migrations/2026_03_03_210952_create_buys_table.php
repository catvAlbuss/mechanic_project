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
        Schema::create('buys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_provider')->references('id')->on('providers')->onDelete('cascade');
            $table->foreignId('id_component')->references('id')->on('components')->onDelete('cascade');
            $table->foreignId('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->string('num_voucher');
            $table->enum('type_voucher',['receipt','invoice'])->default('invoice');
            $table->enum('payment_method',['cash','card','plin','yape']);
            $table->enum('payment_state',['paid','pending','rejected']);
            $table->integer('quantity');
            $table->decimal('unit_price');
            $table->decimal('higher_price');
            $table->decimal('total');
            $table->date('registration_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buys');
    }
};
