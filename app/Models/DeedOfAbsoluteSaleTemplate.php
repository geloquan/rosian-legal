<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property array<array-key, mixed> $document_reference_attachment
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeedOfAbsoluteSaleDocument> $documents
 * @property-read int|null $documents_count
 * @method static \Database\Factories\DeedOfAbsoluteSaleTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleTemplate whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleTemplate whereDocumentReferenceAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleTemplate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DeedOfAbsoluteSaleTemplate extends Model
{
  /** @use HasFactory<\Database\Factories\DeedOfAbsoluteSaleTemplateFactory> */
  use HasFactory;

  protected $fillable = [
    'document_reference_attachment',
    'created_by'
  ];

  protected $casts = [
    'document_reference_attachment' => 'array',
    'created_by' => 'integer'
  ];


  // Relationships
  public function documents()
  {
    return $this->hasMany(DeedOfAbsoluteSaleDocument::class, 'deed_of_absolute_sale_template_id');
  }
  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }
}
