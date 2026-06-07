<?php

namespace App\Filament\Clusters\Documents\DeedOfAbsoluteSaleCluster\Pages;

use App\Filament\Clusters\Documents\DeedOfAbsoluteSaleCluster;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Export extends Page
{
  protected string $view = 'filament.clusters.documents.deed-of-absolute-sale-cluster.pages.export';

  protected static ?string $cluster = DeedOfAbsoluteSaleCluster::class;
  protected static string | BackedEnum | null $navigationIcon = Heroicon::ArrowDownTray;
  protected static ?int $navigationSort = 3;
}
