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
    Schema::create('deed_of_absolute_sale_document_parcels_of_land_ordinal_directions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('parcel_of_land_id')->constrained('deed_of_absolute_sale_document_parcels_of_lands')->cascadeOnDelete();
      $table->enum('ordinal_direction', ['north', 'northeast', 'east', 'southeast', 'south', 'southwest', 'west', 'northwest']);
      $table->jsonb('along_aline_range');
      $table->integer('lot_number');
      $table->integer('block_number');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('deed_of_absolute_sale_document_parcels_of_land_ordinal_directions');
  }
};
