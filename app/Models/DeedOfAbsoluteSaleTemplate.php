<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

  public function documents()
  {
    return $this->hasMany(DeedOfAbsoluteSaleDocument::class, 'deed_of_absolute_sale_template_id');
  }
}
