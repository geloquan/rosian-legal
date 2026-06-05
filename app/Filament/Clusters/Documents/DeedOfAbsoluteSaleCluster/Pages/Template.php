<?php

namespace App\Filament\Clusters\Documents\DeedOfAbsoluteSaleCluster\Pages;

use App\Filament\Clusters\Documents\DeedOfAbsoluteSaleCluster;
use App\Models\DeedOfAbsoluteSaleTemplate;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class Template extends Page implements HasForms, HasTable
{
  use InteractsWithForms, InteractsWithTable;
  protected string $view = 'filament.clusters.documents.deed-of-absolute-sale-cluster.pages.template';

  protected static ?string $cluster = DeedOfAbsoluteSaleCluster::class;

  protected static string | BackedEnum | null $navigationIcon = HeroIcon::Cube;

  protected static ?string $title = 'Templates';

  public bool $showForm = false;
  public ?array $data = [];

  public function mount(): void
  {
    $this->form->fill();
  }

  protected function getHeaderActions(): array
  {
    return [
      Action::make('create-template')
        ->label('New Template')
        ->icon(Heroicon::Cog6Tooth)
        ->action(function () {
          $this->form->fill();
          $this->showForm = true;
        }),
    ];
  }

  public function saveTemplate(): void
  {
    $data = $this->form->getState();

    DeedOfAbsoluteSaleTemplate::create([
      'document_reference_attachment' => $data['document_reference_attachment'],
      'created_by' => auth()->id(),
    ]);

    Notification::make()
      ->title('Template saved')
      ->success()
      ->send();

    $this->showForm = false;
    $this->form->fill();
  }

  public function table(Table $table): Table
  {
    return $table
      ->query(DeedOfAbsoluteSaleTemplate::query())
      ->columns([
        TextColumn::make('id')
          ->label('ID')
          ->sortable(),
        TextColumn::make('document_reference_attachment')
          ->label('File')
          ->limit(40)
          ->tooltip(fn($state) => $state),
        TextColumn::make('created_at')
          ->label('Created At')
          ->since()
          ->sortable()
          ->tooltip(fn($state) => $state),
        TextColumn::make('updated_at')
          ->label('Updated At')
          ->since()
          ->sortable()
          ->tooltip(fn($state) => $state),
      ])
      ->recordActions([
        Action::make('delete')
          ->label('Delete')
          ->icon(Heroicon::Trash)
          ->color('danger')
          ->visible(fn() => Auth::user()->hasRole('super-admin'))
          ->requiresConfirmation()
          ->modalHeading('Delete this template?')
          ->modalDescription('This action cannot be undone. Documents referencing this template will be affected.')
          ->action(function (DeedOfAbsoluteSaleTemplate $record) {
            $record->delete();

            Notification::make()
              ->title('Template deleted')
              ->success()
              ->send();
          }),
      ]);
  }

  public function form(Schema $schema): Schema
  {
    return $schema
      ->statePath('templateData')
      ->components([
        Section::make('Creating New Template')
          ->description('Upload the document template to be used.')
          ->schema([
            FileUpload::make('document_reference_attachment')
              ->label('Upload Document')
              ->required()
              ->disk('public')
              ->directory('deed-of-absolute-sale-documents')
              ->maxSize(10240)
              ->acceptedFileTypes([
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
              ]),
          ]),
      ]);
  }
}
