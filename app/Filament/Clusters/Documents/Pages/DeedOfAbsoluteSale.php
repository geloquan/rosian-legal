<?php

namespace App\Filament\Clusters\Documents\Pages;

use App\Filament\Clusters\Documents\DocumentsCluster;
use Filament\Pages\Page;

class DeedOfAbsoluteSale extends Page
{
    protected string $view = 'filament.clusters.documents.pages.deed-of-absolute-sale';

    protected static ?string $cluster = DocumentsCluster::class;
}
