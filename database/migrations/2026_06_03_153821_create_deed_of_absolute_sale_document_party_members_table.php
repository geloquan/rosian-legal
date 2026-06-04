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
    Schema::create('deed_of_absolute_sale_document_party_members', function (Blueprint $table) {
      $table->id();
      $table->foreignUuid('document_id')
        ->constrained(
          table: 'deed_of_absolute_sale_documents',
          column: 'uuid'
        )
        ->cascadeOnDelete();
      $table->string('name');
      $table->enum('role', ['vendor', 'vendee', 'attorney-in-fact']);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('deed_of_absolute_sale_document_party_members');
  }
};
