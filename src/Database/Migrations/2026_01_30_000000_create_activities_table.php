<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('created_at');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
