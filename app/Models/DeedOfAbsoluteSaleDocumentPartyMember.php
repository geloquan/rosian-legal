<?php

namespace App\Models;

use App\PartyMemberRole;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $document_id
 * @property string $name
 * @property PartyMemberRole $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $city
 * @property string|null $province
 * @property string $gender
 * @method static \Database\Factories\DeedOfAbsoluteSaleDocumentPartyMemberFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentPartyMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentPartyMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentPartyMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentPartyMember whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentPartyMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentPartyMember whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentPartyMember whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentPartyMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentPartyMember whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentPartyMember whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentPartyMember whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeedOfAbsoluteSaleDocumentPartyMember whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DeedOfAbsoluteSaleDocumentPartyMember extends Model
{
  /** @use HasFactory<\Database\Factories\DeedOfAbsoluteSaleDocumentPartyMemberFactory> */
  use HasFactory;
  protected $fillable = [
    'document_id',
    'name',
    'gender',
    'role',
    'city',
    'province',
  ];
  protected $casts = [
    'role' => PartyMemberRole::class,
  ];
}
