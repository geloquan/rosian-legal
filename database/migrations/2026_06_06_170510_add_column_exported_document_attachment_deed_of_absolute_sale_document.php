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
    Schema::table('deed_of_absolute_sale_documents', function (Blueprint $table) {
      $table->jsonb('exported_document_attachment')->nullable()->after('locked_at');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('deed_of_absolute_sale_documents', function (Blueprint $table) {
      $table->dropColumn('exported_document_attachment');
    });
  }
};
