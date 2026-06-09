<?php

namespace App\Filament\Resources\DeedOfAbsoluteSaleDocuments\Pages;

use App\Filament\Resources\DeedOfAbsoluteSaleDocuments\DeedOfAbsoluteSaleDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDeedOfAbsoluteSaleDocuments extends ManageRecords
{
    protected static string $resource = DeedOfAbsoluteSaleDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
