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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->string('pair');
            $table->enum('type', ['BUY', 'SELL']);
            $table->decimal('entry_price', 15, 8);
            $table->decimal('stop_loss', 15, 8);
            $table->decimal('take_profit', 15, 8);
            $table->unsignedTinyInteger('confidence')->default(0)->comment('0-100');
            $table->string('duration');
            $table->enum('category', ['forex', 'crypto']);
            $table->enum('risk_level', ['low', 'medium', 'high']);
            $table->enum('status', ['active', 'tp_hit', 'sl_hit', 'expired'])->default('active');
            $table->text('analysis_summary')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
