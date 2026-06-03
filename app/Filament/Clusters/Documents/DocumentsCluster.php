<?php

namespace App\Filament\Clusters\Documents;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class DocumentsCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;
}
