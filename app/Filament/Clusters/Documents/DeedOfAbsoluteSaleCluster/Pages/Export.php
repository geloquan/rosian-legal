<?php

namespace App\Filament\Clusters\Documents\DeedOfAbsoluteSaleCluster\Pages;

use App\Filament\Clusters\Documents\DeedOfAbsoluteSaleCluster;
use App\Models\DeedOfAbsoluteSaleDocument;
use App\Services\Document\DocumentService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use NumberToWords\NumberToWords;

class Export extends Page implements HasTable, HasForms
{
  use InteractsWithTable, InteractsWithForms;

  protected string $view = 'filament.clusters.documents.deed-of-absolute-sale-cluster.pages.export';

  protected static ?string $cluster = DeedOfAbsoluteSaleCluster::class;
  protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowDownTray;
  protected static ?int $navigationSort = 3;
  protected static ?string $title = 'Export Document';

  public bool $showForm = false;
  public ?array $data = [];

  public function mount(): void
  {
    $this->form->fill();
  }

  public function table(Table $table): Table
  {
    return $table
      ->query(DeedOfAbsoluteSaleDocument::query())
      ->columns([
        TextColumn::make('uuid')
          ->label('Document ID')
          ->copyable()
          ->copyableState(fn($state) => $state)
          ->copyMessage('Document ID copied!')
          ->copyMessageDuration(1500)
          ->icon('heroicon-o-document-duplicate')
          ->iconPosition('after')
          ->searchable()
          ->limit(10)
          ->tooltip(fn($state) => $state)
          ->sortable(),
        TextColumn::make('partyMembers')
          ->label('No. of Parties')
          ->getStateUsing(function (DeedOfAbsoluteSaleDocument $record) {
            $count = $record->partyMembers()->count();
            return NumberToWords::transformNumber('en', $count) . " ({$count})";
          })
          ->badge()
          ->color('primary'),

        TextColumn::make('parcelsOfLand')
          ->label('No. of Parcels of Land')
          ->getStateUsing(function (DeedOfAbsoluteSaleDocument $record) {
            $count = $record->parcelsOfLand()->count();
            return NumberToWords::transformNumber('en', $count) . " ({$count})";
          })
          ->badge()
          ->color('success'),
      ])
      ->filters([])
      ->recordActions([
        Action::make('downloadExisting')
          ->label('Download Existing')
          ->icon(Heroicon::ArrowDownTray)
          ->color('primary')
          ->tooltip('Download the previously generated existing document')
          ->requiresConfirmation()
          ->modalHeading('Download Existing Document')
          ->modalDescription(function (DeedOfAbsoluteSaleDocument $record) {
            return $record->exported_document_attachment
              ? 'This will download the existing generated document. If you want the latest version, use "Rebuild & Export" instead.'
              : 'No existing document found. A new one will be generated and downloaded for you.';
          })
          ->modalSubmitActionLabel('Yes, download')
          ->action(function (DeedOfAbsoluteSaleDocument $record, DocumentService $service) {
            $attachment = $record->exported_document_attachment;

            if (!$attachment) {
              $pdfPath = $service->generatePdf($record);

              Notification::make()
                ->title('Document generated')
                ->body('A new document has been generated and will be downloaded.')
                ->success()
                ->send();

              $attachment = $pdfPath;
            } else {
              Notification::make()
                ->title('Downloading existing document')
                ->body('To get the latest version, use "Rebuild & Export".')
                ->info()
                ->send();
            }

            $this->js("window.open('" . Storage::url($attachment) . "', '_blank')");
          }),

        Action::make('rebuildExport')
          ->label('Rebuild & Export')
          ->icon(Heroicon::ArrowPath)
          ->color('warning')
          ->tooltip('Regenerate the document with the latest data, then download it — this replaces the cached file')
          ->requiresConfirmation()
          ->modalHeading('Rebuild & Export Document')
          ->modalDescription('This will regenerate the document using the latest data and overwrite the cached file. The old version will be replaced. Continue?')
          ->modalSubmitActionLabel('Yes, rebuild and export')
          ->action(function (DeedOfAbsoluteSaleDocument $record) {
            // rebuild logic here
          }),
      ]);
  }

  public function form(Schema $schema): Schema
  {
    return $schema
      ->statePath('data')
      ->components([
        // You can add form components here if needed
      ]);
  }
}
