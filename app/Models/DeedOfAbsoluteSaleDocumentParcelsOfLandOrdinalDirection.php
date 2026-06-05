<?php

namespace App\Models;

use App\OrdinalDirection;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection extends Model
{
  /** @use HasFactory<\Database\Factories\DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirectionFactory> */
  use HasFactory;

  protected $fillable = [
    'parcel_of_land_id',
    'ordinal_direction',
    'along_aline_range',
    'lot_number',
    'block_number'
  ];

  protected $casts = [
    'parcel_of_land_id' => 'integer',
    'ordinal_direction' => OrdinalDirection::class,
    'along_aline_range' => 'array',
    'lot_number' => 'integer',
    'block_number' => 'integer',
  ];


   // relationships
   public function parcelOfLand()
   {
     return $this->belongsTo(DeedOfAbsoluteSaleDocumentParcelsOfLand::class, 'parcel_of_land_id', 'id');
   }
}
