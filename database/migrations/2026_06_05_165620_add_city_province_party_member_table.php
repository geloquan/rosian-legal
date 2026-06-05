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
    Schema::table('deed_of_absolute_sale_document_party_members', function (Blueprint $table) {
      $table->string('city')->nullable()->after('role');
      $table->string('province')->nullable()->after('city');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('deed_of_absolute_sale_document_party_members', function (Blueprint $table) {
      $table->dropColumn(['city', 'province']);
    });
  }
};
