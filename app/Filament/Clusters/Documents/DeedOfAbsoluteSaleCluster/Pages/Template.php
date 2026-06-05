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
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Throwable;

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
      ->recordAction('preview')
      ->columns([
        TextColumn::make('id')
          ->label('ID')
          ->sortable(),
        TextColumn::make('document_reference_attachment')
          ->label('File')
          ->formatStateUsing(fn(mixed $state) => basename((string) ($this->extractAttachmentPath($state) ?? '')))
          ->limit(40)
          ->tooltip(fn(mixed $state) => $this->extractAttachmentPath($state)),
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
        Action::make('preview')
          ->label('Preview')
          ->icon(Heroicon::Eye)
          ->modalHeading(fn(DeedOfAbsoluteSaleTemplate $record) => 'Template #' . $record->id)
          ->modalSubmitAction(false)
          ->modalCancelActionLabel('Close')
          ->modalWidth('7xl')
          ->modalContent(function (DeedOfAbsoluteSaleTemplate $record): View {
            return view('filament.clusters.documents.deed-of-absolute-sale-cluster.pages.template-preview', $this->buildPreviewData($record));
          }),
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

  protected function buildPreviewData(DeedOfAbsoluteSaleTemplate $record): array
  {
    $attachmentPath = $this->extractAttachmentPath($record->document_reference_attachment);

    if (!$attachmentPath || !Storage::disk('public')->exists($attachmentPath)) {
      return [
        'fileName' => null,
        'variables' => [],
        'previewHtml' => null,
        'errorMessage' => 'Template file is missing from storage.',
      ];
    }

    $absolutePath = Storage::disk('public')->path($attachmentPath);
    $variables = [];
    $previewHtml = null;
    $errorMessage = null;

    try {
      $templateProcessor = new TemplateProcessor($absolutePath);
      $variables = collect($templateProcessor->getVariables())
        ->filter(fn(mixed $variable) => filled($variable))
        ->map(fn(mixed $variable) => (string) $variable)
        ->unique()
        ->sort()
        ->values()
        ->all();
    } catch (Throwable) {
      $errorMessage = 'Unable to detect template variables.';
    }

    try {
      $phpWord = IOFactory::load($absolutePath, 'Word2007');
      $writer = IOFactory::createWriter($phpWord, 'HTML');
      ob_start();
      $writer->save('php://output');
      $previewHtml = ob_get_clean() ?: null;
    } catch (Throwable) {
      $errorMessage = $errorMessage ?? 'Unable to render document preview.';
    }

    return [
      'fileName' => basename($attachmentPath),
      'variables' => $variables,
      'previewHtml' => $previewHtml,
      'errorMessage' => $errorMessage,
    ];
  }

  protected function extractAttachmentPath(mixed $attachment): ?string
  {
    if (is_string($attachment) && filled($attachment)) {
      return $attachment;
    }

    if (is_array($attachment)) {
      $path = data_get($attachment, '0') ?? data_get($attachment, 'path');

      if (is_string($path) && filled($path)) {
        return $path;
      }
    }

    return null;
  }
}
