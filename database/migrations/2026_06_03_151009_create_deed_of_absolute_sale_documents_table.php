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
    Schema::create('deed_of_absolute_sale_documents', function (Blueprint $table) {
      $table->uuid()->primary();
      $table->double('sale_price', 15, 2);
      $table->foreignId('deed_of_absolute_sale_template_id')->constrained('deed_of_absolute_sale_templates')->cascadeOnDelete();
      $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('deed_of_absolute_sale_documents');
  }
};
