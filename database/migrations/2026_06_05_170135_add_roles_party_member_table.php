<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void
  {
    // 1. Drop the old check constraint (name format: tablename_columnname_check)
    DB::statement("
        ALTER TABLE deed_of_absolute_sale_document_party_members
        DROP CONSTRAINT deed_of_absolute_sale_document_party_members_role_check
    ");

    // 2. Add new check constraint with updated values
    DB::statement("
        ALTER TABLE deed_of_absolute_sale_document_party_members
        ADD CONSTRAINT deed_of_absolute_sale_document_party_members_role_check
        CHECK (role IN (
            'attorney-in-fact',
            'principal-vendor',
            'principal-vendee',
            'vendor-attorney-in-fact',
            'vendee-attorney-in-fact',
            'principal-vendor-husband',
            'principal-vendor-wife',
            'principal-vendee-husband',
            'principal-vendee-wife'
        ))
    ");
  }

  public function down(): void
  {
    DB::statement("
        ALTER TABLE deed_of_absolute_sale_document_party_members
        DROP CONSTRAINT deed_of_absolute_sale_document_party_members_role_check
    ");

    DB::statement("
        ALTER TABLE deed_of_absolute_sale_document_party_members
        ADD CONSTRAINT deed_of_absolute_sale_document_party_members_role_check
        CHECK (role IN (
            'attorney-in-fact',
            'principal-vendor',
            'principal-vendee',
            'vendor-attorney-in-fact',
            'vendee-attorney-in-fact'
            -- restore original values
        ))
    ");
  }
};
