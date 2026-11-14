<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('store_name');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_owner_name')->nullable();
            $table->string('meta_phone_number_id')->nullable();
            $table->string('meta_waba_id')->nullable();
            $table->text('meta_access_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_profiles');
    }
};
