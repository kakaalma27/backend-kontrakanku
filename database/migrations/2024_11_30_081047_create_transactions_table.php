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
    Schema::create('transactions', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('user_id');
      $table->bigInteger('house_id');
      $table->string('payment')->default('MANUAL');
      $table->float('price')->default(0);
      $table->tinyInteger('status')->default(0); // Misalnya, 0 untuk PENDING
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('transactions');
  }
};
