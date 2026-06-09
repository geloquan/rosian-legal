<?php

namespace App\Filament\Resources\DeedOfAbsoluteSaleDocuments;

use App\Filament\Clusters\Documents\DeedOfAbsoluteSaleCluster;
use App\Filament\Resources\DeedOfAbsoluteSaleDocuments\Pages\ManageDeedOfAbsoluteSaleDocuments;
use App\Models\DeedOfAbsoluteSaleDocument;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class DeedOfAbsoluteSaleDocumentResource extends Resource
{
  protected static ?string $model = DeedOfAbsoluteSaleDocument::class;

  protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
  protected static ?string $cluster = DeedOfAbsoluteSaleCluster::class;
  protected static ?string $label = 'Document';

  public static function form(Schema $schema): Schema
  {
    return $schema
      ->components([
        TextInput::make('sale_price')
          ->required()
          ->numeric()
          ->prefix('$'),
        Select::make('deed_of_absolute_sale_template_id')
          ->relationship('deedOfAbsoluteSaleTemplate', 'id')
          ->required(),
        TextInput::make('created_by')
          ->required()
          ->numeric(),
        DateTimePicker::make('locked_at'),
        TextInput::make('exported_document_attachment'),
      ]);
  }

  public static function infolist(Schema $schema): Schema
  {
    return $schema
      ->components([
        TextEntry::make('uuid')
          ->label('UUID'),
        TextEntry::make('sale_price')
          ->money(),
        TextEntry::make('deedOfAbsoluteSaleTemplate.id')
          ->label('Deed of absolute sale template'),
        TextEntry::make('created_by')
          ->numeric(),
        TextEntry::make('deleted_at')
          ->dateTime()
          ->visible(fn(DeedOfAbsoluteSaleDocument $record): bool => $record->trashed()),
        TextEntry::make('created_at')
          ->dateTime()
          ->placeholder('-'),
        TextEntry::make('updated_at')
          ->dateTime()
          ->placeholder('-'),
        TextEntry::make('locked_at')
          ->dateTime()
          ->placeholder('-'),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('uuid')
          ->label('ID')
          ->limit(7)
          ->searchable()
          ->tooltip(fn($state) => $state),
        TextColumn::make('sale_price')
          ->money('PHP')
          ->sortable(),
        TextColumn::make('deedOfAbsoluteSaleTemplate.id')
          ->searchable(),
        TextColumn::make('created_by')
          ->numeric()
          ->sortable()
          ->formatStateUsing(function ($state) {
            $user = \App\Models\User::find($state);
            return $user->name;
          })
          ->tooltip(function ($state) {
            $user = \App\Models\User::find($state);
            $roles = implode(', ', $user->roles()->pluck('name')->toArray());

            return $roles;
          }),
        TextColumn::make('deleted_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('locked_at')
          ->dateTime()
          ->sortable(),
      ])
      ->filters([
        TrashedFilter::make(),
      ])
      ->recordActions([
        ViewAction::make(),
        Action::make('lock')
          ->label(fn(DeedOfAbsoluteSaleDocument $record) => $record->locked_at ? 'Unlock' : 'Lock')
          ->icon(fn(DeedOfAbsoluteSaleDocument $record) => $record->locked_at ? Heroicon::LockOpen : Heroicon::LockClosed)
          ->color(fn(DeedOfAbsoluteSaleDocument $record) => $record->locked_at ? 'warning' : 'danger')
          ->visible(fn() => Auth::user()->hasRole('super-admin'))
          ->requiresConfirmation()
          ->modalHeading(fn(DeedOfAbsoluteSaleDocument $record) => $record->locked_at ? 'Unlock this record?' : 'Lock this record?')
          ->modalDescription(fn(DeedOfAbsoluteSaleDocument $record) => $record->locked_at
            ? 'This will allow the record to be edited again.'
            : 'This will prevent any further edits to this record.'
          )
          ->action(function (DeedOfAbsoluteSaleDocument $record) {
            $record->update([
              'locked_at' => $record->locked_at ? null : now(),
            ]);

            Notification::make()
              ->title($record->locked_at ? 'Record locked' : 'Record unlocked')
              ->success()
              ->send();
          }),
        EditAction::make(),
        DeleteAction::make(),
        ForceDeleteAction::make(),
        RestoreAction::make(),
      ])
      ->toolbarActions([
        BulkActionGroup::make([
          DeleteBulkAction::make(),
          ForceDeleteBulkAction::make(),
          RestoreBulkAction::make(),
        ]),
      ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => ManageDeedOfAbsoluteSaleDocuments::route('/'),
    ];
  }

  public static function getRecordRouteBindingEloquentQuery(): Builder
  {
    return parent::getRecordRouteBindingEloquentQuery()
      ->withoutGlobalScopes([
        SoftDeletingScope::class,
      ]);
  }
}
