<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('password_resets', function (Blueprint $table) {
      $table->id();
      $table->string('email')->index();
      $table->string('token');
      $table->boolean('is_verified')->default(false);
      $table->integer('attempts')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('password_resets');
  }
};
