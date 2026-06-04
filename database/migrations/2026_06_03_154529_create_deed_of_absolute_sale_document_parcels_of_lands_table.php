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
    Schema::create('deed_of_absolute_sale_document_parcels_of_lands', function (Blueprint $table) {
      $table->id();
      $table->foreignUuid('document_id')
        ->constrained(
          table: 'deed_of_absolute_sale_documents',
          column: 'uuid'
        )
        ->cascadeOnDelete();
      $table->integer('transfer_certification_of_title_number');
      $table->string('barangay');
      $table->string('city_municipality');
      $table->string('province');
      $table->string('island');
      $table->string('area_measurement');
      $table->string('area_measurement_unit');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('deed_of_absolute_sale_document_parcels_of_lands');
  }
};
