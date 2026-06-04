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
    Schema::create('deed_of_absolute_sale_templates', function (Blueprint $table) {
      $table->id();
      $table->jsonb('document_reference_attachment');
      $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('deed_of_absolute_sale_templates');
  }
};
