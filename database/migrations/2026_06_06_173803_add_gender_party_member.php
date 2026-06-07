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
    Schema::table('deed_of_absolute_sale_document_party_members', function (Blueprint $table) {
      $table->enum('gender', ['male', 'female'])->default('male')->after('name');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('deed_of_absolute_sale_document_party_members', function (Blueprint $table) {
      $table->dropColumn('gender');
    });
  }
};
