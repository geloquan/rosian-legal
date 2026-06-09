<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
