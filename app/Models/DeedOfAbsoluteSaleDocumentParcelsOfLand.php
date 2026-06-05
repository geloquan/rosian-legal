<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeedOfAbsoluteSaleDocumentParcelsOfLand extends Model
{
  /** @use HasFactory<\Database\Factories\DeedOfAbsoluteSaleDocumentParcelsOfLandFactory> */
  use HasFactory;

  protected $fillable = [
    'document_id',
    'transfer_certificate_number',
    'barangay',
    'city_municipality',
    'province',
    'island',
    'area_measurement',
    'area_measurement_unit'
  ];

  // relationships
  public function ordinalDirections()
  {
    return $this->hasMany(DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection::class, 'parcel_of_land_id', 'id');
  }
}
