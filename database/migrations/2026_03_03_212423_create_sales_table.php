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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('id_component')->references('id')->on('components')->onDelete('cascade');
            $table->foreignId('id_service')->references('id')->on('services')->onDelete('cascade');

            $table->string('num_voucher')->unique();
            $table->enum('type_voucher',['receipt','invoice'])->default('invoice');
            $table->enum('payment_method',['cash','card','plin','yape']);
            $table->enum('payment_state',['paid','pending','rejected'])->default('pending');
            $table->integer('quantity');
            $table->decimal('igv', 10,2)->default(0.00);
            $table->decimal('total', 10,2);
            $table->date('registration_date')->useCurrent();
            $table->unsignedTinyInteger('descuento')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
