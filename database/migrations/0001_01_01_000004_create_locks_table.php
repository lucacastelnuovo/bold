<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('bold_id')->unique();
            $table->string('bold_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locks');
    }
};
