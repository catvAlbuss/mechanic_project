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
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_category')->references('id')->on('categories')->onDelete('cascade');
            $table->string('sku');
            $table->string('brand');
            $table->decimal('cost_price');
            $table->decimal('sale_price');
            $table->integer('stock');
            $table->date('factory_date');
            $table->string('made');
            $table->enum('state',['active','inactive']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
