<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_registrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('email');
            $table->string('organization_name')->nullable();
            $table->string('subdomain')->unique();
            $table->string('status')->default('pending');
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('dns_configured')->default(false);
            $table->boolean('ssl_configured')->default(false);
            $table->integer('provisioning_attempts')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_registrations');
    }
};
