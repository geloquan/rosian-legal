<?php

namespace App\Filament\Clusters\Documents\DeedOfAbsoluteSaleCluster\Pages\DeedOfAbsoluteSaleDocument;

use App\Filament\Clusters\Documents\DeedOfAbsoluteSaleCluster;
use Filament\Pages\Page;

class View extends Page
{
    protected string $view = 'filament.clusters.documents.deed-of-absolute-sale-cluster.pages.deed-of-absolute-sale-document.view';

    protected static ?string $cluster = DeedOfAbsoluteSaleCluster::class;
}
