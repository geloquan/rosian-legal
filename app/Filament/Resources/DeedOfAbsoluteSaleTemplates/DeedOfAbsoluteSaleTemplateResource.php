<?php

namespace App\Filament\Resources\DeedOfAbsoluteSaleTemplates;

use App\Filament\Clusters\Documents\DeedOfAbsoluteSaleCluster;
use App\Filament\Resources\DeedOfAbsoluteSaleTemplates\Pages\ManageDeedOfAbsoluteSaleTemplates;
use App\Models\DeedOfAbsoluteSaleTemplate;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class DeedOfAbsoluteSaleTemplateResource extends Resource
{
  protected static ?string $model = DeedOfAbsoluteSaleTemplate::class;

  protected static string|BackedEnum|null $navigationIcon = Heroicon::Cube;
//  protected static ?string $title = 'Templates';
  protected static ?string $cluster = DeedOfAbsoluteSaleCluster::class;

  protected static ?string $label = 'Template';
  protected static ?string $slug = 'templates';
  protected static ?string $navigationLabel = 'Templates';

  public static function form(Schema $schema): Schema
  {
    return $schema
      ->statePath('data')
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
//              ->acceptedFileTypes([
//                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
//              ]),
          ])
          ->columnSpanFull(),
      ]);
  }

  public static function infolist(Schema $schema): Schema
  {
    return $schema
      ->components([
        TextEntry::make('created_by')
          ->numeric(),
        TextEntry::make('created_at')
          ->dateTime()
          ->placeholder('-'),
        TextEntry::make('updated_at')
          ->dateTime()
          ->placeholder('-'),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('id')
          ->sortable(),
        TextColumn::make('created_by')
          ->formatStateUsing(function ($state) {
            $user = \App\Models\User::find($state);
            return $user ? $user->name : 'Unknown User';
          })
          ->tooltip(function ($state) {
            $user = \App\Models\User::find($state);
            $roles = $user ? implode(', ', $user->roles->pluck('name')->toArray()) : 'No roles found';
            return $roles;
          }),
        TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        //
      ])
      ->recordActions([
        ViewAction::make(),
        DeleteAction::make(),
      ])
      ->toolbarActions([
        BulkActionGroup::make([
          DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => ManageDeedOfAbsoluteSaleTemplates::route('/'),
      'view' => Pages\ViewDeedOfAbsoluteSaleTemplate::route('/{record}'),
    ];
  }
}
