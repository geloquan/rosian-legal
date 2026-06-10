<?php

namespace App\Filament\Resources\DeedOfAbsoluteSaleDocuments\Pages;

use App\Filament\Resources\DeedOfAbsoluteSaleDocuments\DeedOfAbsoluteSaleDocumentResource;
use App\Models\DeedOfAbsoluteSaleTemplate;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use NumberToWords\NumberToWords;

class EditDeedOfAbsoluteSaleDocument extends EditRecord
{
  protected static string $resource = DeedOfAbsoluteSaleDocumentResource::class;

  protected function getHeaderActions(): array
  {
    return [
      ViewAction::make(),
      DeleteAction::make()
        ->visible(fn() => $this->record->trashed() === false || $this->record->locked_at === null || auth()->user()->hasRole('super-admin')),
      RestoreAction::make(),
    ];
  }

  public function form(Schema $schema): Schema
  {
    return $schema
      ->components([
        Hidden::make('uuid'),

        Section::make('Document Overview')
          ->icon(Heroicon::InformationCircle)
          ->schema([
            TextInput::make('sale_price_in_words')
              ->label('Sale Price in Words')
              ->disabled()
              ->dehydrated(false),
            TextInput::make('sale_price')
              ->label('Sale Price')
              ->required()
              ->numeric()
              ->prefix('₱')
              ->live(debounce: 500)
              ->afterStateUpdated(function ($state, callable $set) {
                if (blank($state)) {
                  $set('sale_price_in_words', '—');
                  return;
                }
                $numberToWords = new NumberToWords();
                $transformer = $numberToWords->getNumberTransformer('en');
                $set('sale_price_in_words', ucwords($transformer->toWords((int)$state)) . ' Pesos Only');
              }),
            Select::make('deed_of_absolute_sale_template_id')
              ->label('Template')
              ->options(
                DeedOfAbsoluteSaleTemplate::query()
                  ->with('documents')
                  ->get()
                  ->sortByDesc(fn($template) => $template->documents?->count())
                  ->mapWithKeys(fn($template) => [
                    $template->id => "Template #{$template->id} — {$template->created_at->format('M d, Y')}"
                  ])
              )
              ->searchable()
              ->preload()
              ->required(),
          ]),

        Section::make('Party Members')
          ->icon(Heroicon::Users)
          ->schema([
            Repeater::make('party_members')
              ->label('Party Members')
              ->hint('Two principals required: one Principal Vendor and one Principal Vendee.')
              ->hintColor('warning')
              ->hintIcon(Heroicon::InformationCircle)
              ->schema([
                TextInput::make('name')
                  ->label('Name')
                  ->required()
                  ->columnSpan(2),
                Select::make('role')
                  ->label('Role')
                  ->options([
                    'Vendor' => [
                      'principal-vendor'         => 'Principal Vendor',
                      'principal-vendor-husband' => 'Vendor (Husband)',
                      'principal-vendor-wife'    => 'Vendor (Wife)',
                      'vendor-attorney-in-fact'  => 'Vendor Attorney-in-Fact',
                    ],
                    'Vendee' => [
                      'principal-vendee'         => 'Principal Vendee',
                      'principal-vendee-husband' => 'Vendee (Husband)',
                      'principal-vendee-wife'    => 'Vendee (Wife)',
                      'vendee-attorney-in-fact'  => 'Vendee Attorney-in-Fact',
                    ],
                  ])
                  ->required(),
                Select::make('gender')
                  ->label('Gender')
                  ->options([
                    'male'   => 'Male',
                    'female' => 'Female',
                  ])
                  ->required(),
                TextInput::make('city')
                  ->label('City')
                  ->placeholder('—'),
                TextInput::make('province')
                  ->label('Province')
                  ->placeholder('—'),
              ])
              ->columns(3)
              ->addActionLabel('Add Party Member')
              ->defaultItems(0)
              ->reorderable(false),
          ]),

        Section::make('Parcels of Land')
          ->icon(Heroicon::Tag)
          ->schema([
            Repeater::make('parcels_of_land')
              ->label('Parcels of Land')
              ->columns(4)
              ->schema([
                TextInput::make('transfer_certificate_number')
                  ->label('Transfer Certificate Number')
                  ->required()
                  ->columnSpan(2),
                TextInput::make('area_measurement')
                  ->label('Area Measurement')
                  ->required()
                  ->numeric(),
                Select::make('area_measurement_unit')
                  ->label('Unit')
                  ->options([
                    'sqm'      => 'Square Meters (sqm)',
                    'sqft'     => 'Square Feet (sqft)',
                    'hectares' => 'Hectares (ha)',
                    'acres'    => 'Acres (ac)',
                  ])
                  ->required(),
                TextInput::make('barangay')
                  ->label('Barangay')
                  ->required(),
                TextInput::make('city_municipality')
                  ->label('City/Municipality')
                  ->required(),
                TextInput::make('province')
                  ->label('Province')
                  ->required(),
                TextInput::make('island')
                  ->label('Island')
                  ->required(),
                Repeater::make('ordinal_directions')
                  ->label('Ordinal Directions')
                  ->columns(4)
                  ->columnSpanFull()
                  ->schema([
                    Select::make('ordinal_direction')
                      ->label('Ordinal Direction')
                      ->options([
                        'north'     => 'North',
                        'northeast' => 'Northeast',
                        'east'      => 'East',
                        'southeast' => 'Southeast',
                        'south'     => 'South',
                        'southwest' => 'Southwest',
                        'west'      => 'West',
                        'northwest' => 'Northwest',
                      ])
                      ->required(),
                    TextInput::make('along_aline_range.0')
                      ->label('Along A-Line (Start)')
                      ->hint('e.g. "A", "1"')
                      ->required(),
                    TextInput::make('along_aline_range.1')
                      ->label('Along A-Line (End)')
                      ->hint('e.g. "B", "2"')
                      ->required(),
                    TextInput::make('lot_number')
                      ->label('Lot Number')
                      ->required()
                      ->numeric()
                      ->minValue(0)
                      ->extraInputAttributes(['min' => 0, 'step' => 1]),
                    TextInput::make('block_number')
                      ->label('Block Number')
                      ->required()
                      ->numeric()
                      ->minValue(0)
                      ->extraInputAttributes(['min' => 0, 'step' => 1]),
                  ])
                  ->addActionLabel('Add Ordinal Direction')
                  ->defaultItems(0),
              ])
              ->addActionLabel('Add Parcel of Land')
              ->defaultItems(0)
              ->live(),
          ]),
      ])
      ->columns(1);
  }

  protected function mutateFormDataBeforeFill(array $data): array
  {
    if (isset($data['sale_price'])) {
      $numberToWords = new NumberToWords();
      $transformer = $numberToWords->getNumberTransformer('en');
      $data['sale_price_in_words'] = ucwords($transformer->toWords((int)$data['sale_price'])) . ' Pesos Only';
    }

    $data['party_members'] = $this->record
      ->partyMembers()
      ->get()
      ->map(fn($m) => [
        'name'     => $m->name,
        'role'     => $m->role instanceof \BackedEnum ? $m->role->value : $m->role,
        'gender'   => $m->gender,
        'city'     => $m->city,
        'province' => $m->province,
      ])
      ->toArray();

    return $data;
  }

  protected function mutateFormDataBeforeSave(array $data): array
  {
    unset($data['party_members']);

    return $data;
  }

  protected function afterSave(): void
  {
    $partyMembers = $this->data['party_members'] ?? [];

    $this->record->partyMembers()->delete();

    foreach ($partyMembers as $member) {
      $this->record->partyMembers()->create([
        'name'     => $member['name'],
        'role'     => $member['role'],
        'gender'   => $member['gender'],
        'city'     => $member['city'] ?? null,
        'province' => $member['province'] ?? null,
      ]);
    }
  }

  protected function getFormActions(): array
  {
    return [
      $this->getSaveFormAction(),
      $this->getCancelFormAction(),
    ];
  }
}
