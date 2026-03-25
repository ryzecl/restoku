<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_tokens', function (Blueprint $table) {
            $table->id();
            $table->integer('table_number');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['table_number', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_tokens');
    }
};
