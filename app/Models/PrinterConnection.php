<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $ip_address
 * @property int $port
 * @property int $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrinterConnection withoutTrashed()
 * @mixin \Eloquent
 */
#[Fillable(['name', 'ip_address', 'port'])]
#[Hidden(['created_at', 'updated_at'])]
class PrinterConnection extends Model
{
  use SoftDeletes;

}
