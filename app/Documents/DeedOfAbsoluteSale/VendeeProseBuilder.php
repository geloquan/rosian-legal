<?php

namespace App\Documents\DeedOfAbsoluteSale;

use App\Services\Builders\PartyProseBuilder;

class VendeeProseBuilder extends PartyProseBuilder
{
  protected function label(): string
  {
    return 'VENDEE';
  }
}
