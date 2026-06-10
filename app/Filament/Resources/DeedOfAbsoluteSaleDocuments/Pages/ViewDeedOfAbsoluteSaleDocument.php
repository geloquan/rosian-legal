<?php

namespace App\Filament\Resources\DeedOfAbsoluteSaleDocuments\Pages;

use App\Filament\Resources\DeedOfAbsoluteSaleDocuments\DeedOfAbsoluteSaleDocumentResource;
use App\Models\DeedOfAbsoluteSaleDocument;
use App\Services\Document\DocumentService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class ViewDeedOfAbsoluteSaleDocument extends ViewRecord
{
  protected static string $resource = DeedOfAbsoluteSaleDocumentResource::class;

  protected function getHeaderActions(): array
  {
    return [
      EditAction::make()
        ->label('Edit')
        ->icon(Heroicon::PencilSquare)
        ->visible(fn() => $this->record->locked_at === null && $this->record->trashed() === false),
      Action::make('export_document')
        ->label('Export')
        ->icon(Heroicon::ArrowDownTray)
        ->action(function (DeedOfAbsoluteSaleDocument $record) {
          $pdfPath = app(DocumentService::class)->generatePdf($record, true);

          if ($pdfPath) {
            $this->js("window.open('".Storage::url($pdfPath)."', '_blank')");
          } else {
            Notification::make()
              ->title('Failed to export document.')
              ->danger()
              ->send();
          }
        })
    ];
  }
}
