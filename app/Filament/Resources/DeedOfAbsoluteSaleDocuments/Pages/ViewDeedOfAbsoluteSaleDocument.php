<?php

namespace App\Filament\Resources\DeedOfAbsoluteSaleDocuments\Pages;

use App\Filament\Resources\DeedOfAbsoluteSaleDocuments\DeedOfAbsoluteSaleDocumentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewDeedOfAbsoluteSaleDocument extends ViewRecord
{
  protected static string $resource = DeedOfAbsoluteSaleDocumentResource::class;

  protected function getHeaderActions(): array
  {
    return [
      EditAction::make()
        ->label('Edit Document')
        ->visible(fn() => $this->record->locked_at === null && $this->record->trashed() === false),
    ];
  }
}
