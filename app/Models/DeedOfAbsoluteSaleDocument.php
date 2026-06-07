<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
