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
        Schema::create('assesories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('accessory_categories')->onDelete('cascade');
            $table->string('accessory_no')->unique(); // e.g., LTP-001
            $table->string('name'); // e.g., Dell 6420
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assesories');
    }
};
