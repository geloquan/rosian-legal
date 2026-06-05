<?php

namespace App\Filament\Clusters\Documents;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class DeedOfAbsoluteSaleCluster extends Cluster
{
  protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;
  protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
