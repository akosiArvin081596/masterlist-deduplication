<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('masterlist_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('masterlist_id')->constrained()->cascadeOnDelete();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('ext_name')->nullable();
            $table->date('birthday')->nullable();
            $table->string('region_name')->nullable();
            $table->string('province_name')->nullable();
            $table->string('city_name')->nullable();
            $table->string('barangay_name')->nullable();
            $table->string('purok_sitio')->nullable();
            $table->timestamps();

            $table->index('masterlist_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masterlist_records');
    }
};
