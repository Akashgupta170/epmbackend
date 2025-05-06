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
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->string('accessory_no')->unique();
            $table->string('brand_name')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('vendor_name')->nullable();
            $table->string('condition')->nullable();
            $table->date('purchase_date')->nullable();
            $table->integer('amount')->nullable();
            $table->text('images')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['available', 'in_use', 'damaged', 'under_repair'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};
