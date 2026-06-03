<?php

namespace App\Filament\Resources\PrinterConnections;

use App\Filament\Resources\PrinterConnections\Pages\ManagePrinterConnections;
use App\Models\PrinterConnection;
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
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrinterConnectionResource extends Resource
{
  protected static ?string $model = PrinterConnection::class;

  protected static string|BackedEnum|null $navigationIcon = Heroicon::Printer;

  public static function form(Schema $schema): Schema
  {
    return $schema
      ->components([
        TextInput::make('name')
          ->required()
          ->maxLength(255),
        TextInput::make('host')
          ->required()
          ->ip()
          ->maxLength(255),
        TextInput::make('port')
          ->required()
          ->numeric()
          ->maxLength(5),
        Actions::make([
          Action::make('testConnection')
            ->label('Test Connection')
            ->icon('heroicon-o-signal')
            ->color('info')
            ->action(function (Get $get) {
              $host = $get('host');
              $port = (int) $get('port');

              $connection = @fsockopen($host, $port, timeout: 5);

              if (is_resource($connection)) {
                fclose($connection);

                Notification::make()
                  ->title('Connection successful')
                  ->body("Reached {$host}:{$port}")
                  ->success()
                  ->send();
              } else {
                Notification::make()
                  ->title('Connection failed')
                  ->body("Could not reach {$host}:{$port}")
                  ->danger()
                  ->send();
              }
            }),
        ]),
      ]);
  }

  public static function infolist(Schema $schema): Schema
  {
    return $schema
      ->components([
        //
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        //
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
      'index' => ManagePrinterConnections::route('/'),
    ];
  }

  public static function getRecordRouteBindingEloquentQuery(): Builder
  {
    return parent::getRecordRouteBindingEloquentQuery()
      ->withoutGlobalScopes([
        SoftDeletingScope::class,
      ]);
  }
  public static function canAccess(): bool
  {
    return auth()->user()->hasRole('super-admin');
  }
}
