<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duplicate_pairs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('masterlist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('record_1_id')->constrained('masterlist_records')->cascadeOnDelete();
            $table->foreignId('record_2_id')->constrained('masterlist_records')->cascadeOnDelete();
            $table->string('match_type');
            $table->unsignedTinyInteger('confidence');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['masterlist_id', 'status']);
            $table->unique(['record_1_id', 'record_2_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duplicate_pairs');
    }
};
