<?php

namespace App\Models;

use App\PartyMemberRole;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//#[Fillable(['document_id', 'name', 'role'])]
class DeedOfAbsoluteSaleDocumentPartyMember extends Model
{
  /** @use HasFactory<\Database\Factories\DeedOfAbsoluteSaleDocumentPartyMemberFactory> */
  use HasFactory;
  protected $fillable = [
    'document_id',
    'name',
    'role',
    'city',
    'province',
  ];
  protected $casts = [
    'role' => PartyMemberRole::class,
  ];
}
