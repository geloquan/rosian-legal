<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string $uuid
 * @property numeric $sale_price
 * @property int $deed_of_absolute_sale_template_id
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $locked_at
 * @property array<array-key, mixed>|null $exported_document_attachment
 * @property-read \App\Models\DeedOfAbsoluteSaleTemplate $deedOfAbsoluteSaleTemplate
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeedOfAbsoluteSaleDocumentParcelsOfLand> $parcelsOfLand
 * @property-read int|null $parcels_of_land_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeedOfAbsoluteSaleDocumentPartyMember> $partyMembers
 * @property-read int|null $party_members_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument whereDeedOfAbsoluteSaleTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument whereExportedDocumentAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument whereLockedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument whereSalePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocument withoutTrashed()
 * @mixin \Eloquent
 */
class DeedOfAbsoluteSaleDocument extends Model
{
  use SoftDeletes;

  protected $primaryKey = 'uuid';

  public $incrementing = false;

  protected $keyType = 'string';
  protected $fillable = [
    'sale_price',
    'deed_of_absolute_sale_template_id',
    'created_by',
    'locked_at',
    'exported_document_attachment'
  ];
  protected $casts = [
    'sale_price' => 'decimal:2',
    'deed_of_absolute_sale_template_id' => 'integer',
    'exported_document_attachment' => 'array',
  ];

  protected static function booted(): void
  {
    static::creating(function ($model) {
      $model->uuid = Str::uuid();
    });
  }

  //Relationships
  public function deedOfAbsoluteSaleTemplate(): BelongsTo
  {
    return $this->belongsTo(DeedOfAbsoluteSaleTemplate::class, 'deed_of_absolute_sale_template_id');
  }

  public function partyMembers()
  {
    return $this->hasMany(DeedOfAbsoluteSaleDocumentPartyMember::class, 'document_id', 'uuid');
  }

  public function parcelsOfLand()
  {
    return $this->hasMany(DeedOfAbsoluteSaleDocumentParcelsOfLand::class, 'document_id', 'uuid');
  }
}
