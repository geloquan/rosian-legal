<?php

namespace App\Filament\Resources\DeedOfAbsoluteSaleDocuments;

use App\Filament\Clusters\Documents\DeedOfAbsoluteSaleCluster;
use App\Filament\Resources\DeedOfAbsoluteSaleDocuments\Pages\CreateDeedOfAbsoluteSaleDocument;
use App\Filament\Resources\DeedOfAbsoluteSaleDocuments\Pages\EditDeedOfAbsoluteSaleDocument;
use App\Filament\Resources\DeedOfAbsoluteSaleDocuments\Pages\ManageDeedOfAbsoluteSaleDocuments;
use App\Filament\Resources\DeedOfAbsoluteSaleDocuments\Pages\ViewDeedOfAbsoluteSaleDocument;
use App\Filament\Resources\DeedOfAbsoluteSaleTemplates\Pages\ViewDeedOfAbsoluteSaleTemplate;
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
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use NumberToWords\NumberToWords;

class DeedOfAbsoluteSaleDocumentResource extends Resource
{
  protected static ?string $model = DeedOfAbsoluteSaleDocument::class;

  protected static string|BackedEnum|null $navigationIcon = Heroicon::Document;
  protected static ?string $cluster = DeedOfAbsoluteSaleCluster::class;
  protected static ?string $label = 'Document';
  protected static ?string $slug = 'documents';
  protected static ?string $navigationLabel = 'Documents';

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
        Section::make('Document Overview')
          ->columns(3)
          ->schema([
            TextEntry::make('uuid')
              ->label('UUID')
              ->copyable()
              ->copyMessage('UUID copied')
              ->fontFamily(FontFamily::Mono)
              ->columnSpan(2),
            TextEntry::make('locked_at')
              ->label('Status')
              ->placeholder('Unlocked')
              ->badge()
              ->color(fn ($state) => $state ? 'danger' : 'success')
              ->formatStateUsing(fn ($state) => $state ? 'Locked' : 'Active'),
            TextEntry::make('sale_price')
              ->label('Sale Price')
              ->money('PHP')
              ->weight(FontWeight::Bold),
            TextEntry::make('sale_price_in_words')
              ->label('Sale Price in Words')
              ->getStateUsing(function ($record) {
                if (blank($record->sale_price)) return '—';
                $numberToWords = new NumberToWords();
                $transformer = $numberToWords->getNumberTransformer('en');
                return ucwords($transformer->toWords((int) $record->sale_price)) . ' Pesos Only';
              })
              ->columnSpanFull(),
            TextEntry::make('deedOfAbsoluteSaleTemplate.id')
              ->label('Template')
              ->icon(Heroicon::ArrowTopRightOnSquare)
              ->url(fn ($record) => ViewDeedOfAbsoluteSaleTemplate::getUrl([
                'record' => $record->deedOfAbsoluteSaleTemplate->id,
              ])),
          ]),

        Section::make('Party Members')
          ->schema([
            RepeatableEntry::make('party_members')
              ->label('')
              ->schema([
                TextEntry::make('name')
                  ->label('Name')
                  ->weight(FontWeight::Medium),
                TextEntry::make('role')
                  ->label('Role')
                  ->badge()
                  ->color(fn ($state) => match ($state) {
                    'principal-vendor' => 'warning',
                    'principal-vendee' => 'info',
                    default => 'gray',
                  })
                  ->formatStateUsing(fn ($state) => match ($state) {
                    'principal-vendor' => 'Principal Vendor',
                    'principal-vendee' => 'Principal Vendee',
                    default => $state,
                  }),
                TextEntry::make('gender')
                  ->label('Gender')
                  ->formatStateUsing(fn ($state) => ucfirst($state))
                  ->badge()
                  ->color('gray'),
                TextEntry::make('city')
                  ->label('City')
                  ->placeholder('—'),
                TextEntry::make('province')
                  ->label('Province')
                  ->placeholder('—'),
              ])
              ->columns(5),
          ]),

        Section::make('Parcels of Land')
          ->schema([
            RepeatableEntry::make('parcels_of_land')
              ->label('')
              ->schema([
                TextEntry::make('transfer_certificate_number')
                  ->label('Transfer Certificate No.')
                  ->weight(FontWeight::Medium)
                  ->columnSpan(2),
                TextEntry::make('area_measurement')
                  ->label('Area')
                  ->formatStateUsing(fn ($state, $record) => $state . ' ' . match ($record['area_measurement_unit'] ?? '') {
                      'sqm' => 'sqm',
                      'sqft' => 'sqft',
                      'hectares' => 'ha',
                      'acres' => 'ac',
                      default => '',
                    }),
                TextEntry::make('barangay')->label('Barangay'),
                TextEntry::make('city_municipality')->label('City/Municipality'),
                TextEntry::make('province')->label('Province'),
                TextEntry::make('island')->label('Island'),

                RepeatableEntry::make('ordinal_directions')
                  ->label('Ordinal Directions')
                  ->columnSpanFull()
                  ->schema([
                    TextEntry::make('ordinal_direction')
                      ->label('Direction')
                      ->badge()
                      ->color('info')
                      ->formatStateUsing(fn ($state) => ucfirst($state)),
                    TextEntry::make('along_aline_range')
                      ->label('A-Line Range')
                      ->formatStateUsing(fn ($state) => is_array($state)
                        ? ($state[0] ?? '—') . ' → ' . ($state[1] ?? '—')
                        : '—'
                      ),
                    TextEntry::make('lot_number')->label('Lot No.'),
                    TextEntry::make('block_number')->label('Block No.'),
                  ])
                  ->columns(4),
              ])
              ->columns(4),
          ]),

        Section::make('Metadata')
          ->columns(4)
          ->collapsed()
          ->schema([
            TextEntry::make('created_by')
              ->label('Created By')
              ->numeric(),
            TextEntry::make('created_at')
              ->label('Created')
              ->dateTime()
              ->placeholder('—'),
            TextEntry::make('updated_at')
              ->label('Last Updated')
              ->dateTime()
              ->placeholder('—'),
            TextEntry::make('deleted_at')
              ->label('Deleted At')
              ->dateTime()
              ->visible(fn (DeedOfAbsoluteSaleDocument $record): bool => $record->trashed())
              ->placeholder('—'),
          ]),
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
          ->since()
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
        EditAction::make()
          ->visible(fn(DeedOfAbsoluteSaleDocument $record) => !$record->locked_at),
        DeleteAction::make()
          ->visible(fn(DeedOfAbsoluteSaleDocument $record) => !$record->locked_at),
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
      'create' => CreateDeedOfAbsoluteSaleDocument::route('/create'),
      'view'  => ViewDeedOfAbsoluteSaleDocument::route('/{record}'),
      'edit' => EditDeedOfAbsoluteSaleDocument::route('/{record}/edit'),
    ];
  }
  public static function getRecordRouteKeyName(): string
  {
    return 'uuid';
  }
  public static function getRecordRouteBindingEloquentQuery(): Builder
  {
    return parent::getRecordRouteBindingEloquentQuery()
      ->withoutGlobalScopes([
        SoftDeletingScope::class,
      ]);
  }
}
