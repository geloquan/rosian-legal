<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'ip_address', 'port'])]
#[Hidden(['created_at', 'updated_at'])]
class PrinterConnection extends Model
{
  use SoftDeletes;

}
