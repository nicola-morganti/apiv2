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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string("user_id");
            $table->foreignId("product_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string("guild_id");
            $table->string("token")->unique();
            $table->dateTime("expires_at")->nullable();
            $table->bigInteger("license_changes")->default(0);
            $table->boolean("state")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
