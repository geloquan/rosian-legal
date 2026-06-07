<?php

namespace App\Documents\DeedOfAbsoluteSale;

use App\Services\Builders\PartyProseBuilder;

class VendorProseBuilder extends PartyProseBuilder
{
  protected function label(): string
  {
    return 'VENDOR';
  }
}
