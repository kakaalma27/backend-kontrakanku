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
    Schema::create('transactions_houses', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('user_id');
       $table->bigInteger('booking_id');
      $table->string('payment')->default('cash');
      $table->float('price')->default(0);
      $table->tinyInteger('status')->default(0); 
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('transactions_houses');
  }
};
