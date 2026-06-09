<?php

namespace App\Filament\Resources\DeedOfAbsoluteSaleTemplates\Pages;

use App\Filament\Resources\DeedOfAbsoluteSaleDocuments\DeedOfAbsoluteSaleDocumentResource;
use App\Filament\Resources\DeedOfAbsoluteSaleTemplates\DeedOfAbsoluteSaleTemplateResource;
use App\Models\DeedOfAbsoluteSaleTemplate;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use PhpOffice\PhpWord\TemplateProcessor;

class ViewDeedOfAbsoluteSaleTemplate extends ViewRecord
{
  protected const int TEMPLATE_VARIABLE_CACHE_MINUTES = 5;

  protected static string $resource = DeedOfAbsoluteSaleTemplateResource::class;

  protected function getHeaderActions(): array
  {
    return [
      DeleteAction::make(),
      Action::make('use_template')
        ->label('Use This Template')
        ->icon(Heroicon::DocumentPlus)
        ->action(function ($record) {
          redirect()->to(
            DeedOfAbsoluteSaleDocumentResource::getUrl('create', [
              'templateId' => $record->id,
            ])
          );
        })
    ];
  }

  public function infolist(Schema $schema): Schema
  {
    return $schema
      ->components([
        Section::make()
          ->description('Template records are uneditable. You may only delete it.')
          ->icon('heroicon-o-exclamation-triangle')
          ->iconColor('warning')
          ->extraAttributes(['class' => 'text-center'])
          ->schema([])
          ->columnSpanFull(),
        Section::make('Info')
          ->schema([
            TextEntry::make('creator.name')
              ->label('Created By')
              ->tooltip(function ($record) {
                $user = $record->creator;
                $roles = $user->roles->pluck('name')->join(', ');
                return "Roles: {$roles}";
              })
              ->placeholder('-'),
            TextEntry::make('created_at')
              ->dateTime()
              ->placeholder('-'),
            TextEntry::make('updated_at')
              ->dateTime()
              ->placeholder('-'),
          ])
          ->columns(1),
        Section::make('Usage')
          ->schema([
            TextEntry::make('documents_count')
              ->label('Number of documents using this template')
              ->state(fn($record) => $record->documents()->count())
              ->badge(),
          ]),
        Section::make('Technical Details')
          ->schema([
            TextEntry::make('document_variables')
              ->label('Document Variables')
              ->state(function ($record) {
                $variables = $this->extractTemplateVariables($record);
                return empty($variables) ? [] : $variables;
              })
              ->badge()
              ->placeholder('-'),
          ]),

        Action::make('download')
          ->label(function ($record) {
            $relativePath = $this->resolveTemplatePath($record->document_reference_attachment);

            if (blank($relativePath) || !Storage::disk('public')->exists($relativePath)) {
              return 'Download Template';
            }

            $bytes = Storage::disk('public')->size($relativePath);
            $size = match(true) {
              $bytes >= 1048576 => round($bytes / 1048576, 1) . ' MB',
              $bytes >= 1024    => round($bytes / 1024, 1) . ' KB',
              default           => $bytes . ' B',
            };

            return "Download Template ({$size})";
          })
          ->url(function ($record) {
            $relativePath = $this->resolveTemplatePath($record->document_reference_attachment);

            if (blank($relativePath)) return null;
            if (!Storage::disk('public')->exists($relativePath)) return null;

            return Storage::disk('public')->url($relativePath);
          })
          ->openUrlInNewTab()
          ->icon(Heroicon::ArrowDownTray),
      ])
      ->columns(3);
  }

  // helper
  protected function extractTemplateVariables(DeedOfAbsoluteSaleTemplate $record): array
  {
    $relativePath = $this->resolveTemplatePath($record->document_reference_attachment);

    if (blank($relativePath)) {
      return [];
    }

    $absolutePath = Storage::disk('public')->path($relativePath);
    $cacheVersion = $record->updated_at?->timestamp ?? $record->created_at?->timestamp ?? 0;
    $fileVersion = is_file($absolutePath) ? (string)filemtime($absolutePath) : 'missing';
    $cacheKey = 'deed-template-variables:' . $record->id . ':' . md5($relativePath . '|' . $cacheVersion . '|' . $fileVersion);

    return Cache::remember($cacheKey, now()->addMinutes(self::TEMPLATE_VARIABLE_CACHE_MINUTES), function () use ($record, $relativePath, $absolutePath) {
      try {
        if (!is_file($absolutePath)) {
          return [];
        }

        $variables = (new TemplateProcessor($absolutePath))->getVariables();
        $variables = array_map(static fn($variable) => (string)$variable, $variables);
        $variables = array_filter($variables, static fn($variable) => filled($variable));

        return array_values(array_unique($variables));
      } catch (\Throwable $exception) {
        Log::warning('Failed extracting DOCX template variables.', [
          'template_id' => $record->id,
          'template_path' => $relativePath,
          'error' => $exception->getMessage(),
        ]);

        report($exception);

        return [];
      }
    });
  }

  protected function resolveTemplatePath(mixed $attachment): ?string
  {
    if (filled($attachment) && is_string($attachment)) {
      return $attachment;
    }

    if (!is_array($attachment)) {
      return null;
    }

    foreach ($attachment as $value) {
      $resolved = $this->resolveTemplatePath($value);

      if (filled($resolved)) {
        return $resolved;
      }
    }

    return null;
  }
}
