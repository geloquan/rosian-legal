<?php

namespace App\Models;

use App\OrdinalDirection;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $parcel_of_land_id
 * @property OrdinalDirection $ordinal_direction
 * @property array<array-key, mixed> $along_aline_range
 * @property int $lot_number
 * @property int $block_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DeedOfAbsoluteSaleDocumentParcelsOfLand $parcelOfLand
 * @method static \Database\Factories\DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirectionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection whereAlongAlineRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection whereBlockNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection whereLotNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection whereOrdinalDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection whereParcelOfLandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
