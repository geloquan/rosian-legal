<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $document_id
 * @property int $transfer_certification_of_title_number
 * @property string $barangay
 * @property string $city_municipality
 * @property string $province
 * @property string $island
 * @property string $area_measurement
 * @property string $area_measurement_unit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeedOfAbsoluteSaleDocumentParcelsOfLandOrdinalDirection> $ordinalDirections
 * @property-read int|null $ordinal_directions_count
 * @method static \Database\Factories\DeedOfAbsoluteSaleDocumentParcelsOfLandFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand whereAreaMeasurement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand whereAreaMeasurementUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand whereBarangay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand whereCityMunicipality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand whereIsland($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand whereTransferCertificationOfTitleNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentParcelsOfLand whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
