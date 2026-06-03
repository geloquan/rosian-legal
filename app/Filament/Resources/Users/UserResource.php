<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
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
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
  protected static ?string $model = User::class;

  protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

  public static function form(Schema $schema): Schema
  {
    return $schema
      ->components([
        TextInput::make('name')
          ->required(),
        TextInput::make('email')
          ->label('Email address')
          ->email()
          ->required(),
        DateTimePicker::make('email_verified_at'),
        TextInput::make('password')
          ->password()
          ->required(),
      ]);
  }

  public static function infolist(Schema $schema): Schema
  {
    return $schema
      ->components([
        TextEntry::make('name'),
        TextEntry::make('email')
          ->label('Email address'),
        TextEntry::make('email_verified_at')
          ->dateTime()
          ->placeholder('-'),
        TextEntry::make('created_at')
          ->dateTime()
          ->placeholder('-'),
        TextEntry::make('updated_at')
          ->dateTime()
          ->placeholder('-'),
        TextEntry::make('deleted_at')
          ->dateTime()
          ->visible(fn(User $record): bool => $record->trashed()),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('name')
          ->searchable(),
        TextColumn::make('email')
          ->label('Email address')
          ->searchable(),
        TextColumn::make('email_verified_at')
          ->dateTime()
          ->sortable(),
        TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('deleted_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        TrashedFilter::make(),
      ])
      ->recordActions([
        ViewAction::make(),
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
      'index' => ManageUsers::route('/'),
    ];
  }

  public static function getRecordRouteBindingEloquentQuery(): Builder
  {
    return parent::getRecordRouteBindingEloquentQuery()
      ->withoutGlobalScopes([
        SoftDeletingScope::class,
      ]);
  }

  // Authorization
  public static function canAccess(): bool
  {
    return auth()->user()->hasAnyRole(['super-admin']);
  }
}
