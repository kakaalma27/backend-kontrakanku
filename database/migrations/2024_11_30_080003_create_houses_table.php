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
    Schema::create('houses', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->float('price')->default(0);
      $table->longText('description');
      $table->string('tags')->nullable();
      $table->string('kamar');
      $table->string('wc');
      $table->boolean('available')->default(false);
      $table->string('quantity');
      $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
      $table->bigInteger('address_id')->nullable();;
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('houses');
  }
};