<?php

namespace App\Filament\Clusters\Documents\Pages;

use App\Filament\Clusters\Documents\DeedOfAbsoluteSaleCluster;
use App\Models\DeedOfAbsoluteSaleDocument;
use App\Models\DeedOfAbsoluteSaleTemplate;
use App\PartyMemberRole;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use NumberToWords\NumberToWords;

class Document extends Page implements HasForms, HasTable
{
  use InteractsWithForms, InteractsWithTable;

  protected string $view = 'filament.clusters.documents.pages.deed-of-absolute-sale-document';
  protected static ?string $cluster = DeedOfAbsoluteSaleCluster::class;
  protected static string|BackedEnum|null $navigationIcon = Heroicon::Document;
  protected static ?string $title = 'Documents';

  public bool $showForm = false;
  public ?array $data = [];

  public function mount(): void
  {
    $this->form->fill([
      'party_members' => [],
      'parcels_of_land' => [],
    ]);
  }

  protected function getHeaderActions(): array
  {
    return [
      Action::make('create-document')
        ->label('New Document')
        ->icon(Heroicon::Document)
        ->action(function () {
          $this->form->fill();
          $this->showForm = true;
        }),
    ];
  }

  public function saveDocument(): void
  {
    $data = $this->form->getState();

    $roles = collect($data['party_members'] ?? [])->pluck('role');

    if (!$roles->contains('principal-vendor') || !$roles->contains('principal-vendee')) {
      Notification::make()
        ->title('Missing required principals')
        ->body('You must add exactly one Principal Vendor and one Principal Vendee.')
        ->warning()
        ->send();

      return;
    }

    $isEditing = !blank($data['uuid'] ?? null);

    if ($isEditing) {
      $document = DeedOfAbsoluteSaleDocument::findOrFail($data['uuid']);
      $document->update([
        'sale_price' => $data['sale_price'],
        'deed_of_absolute_sale_template_id' => $data['deed_of_absolute_sale_template_id'],
      ]);
      $document->partyMembers()->delete();
    } else {
      $document = DeedOfAbsoluteSaleDocument::create([
        'sale_price' => $data['sale_price'],
        'deed_of_absolute_sale_template_id' => $data['deed_of_absolute_sale_template_id'],
        'created_by' => auth()->id(),
      ]);
    }

    foreach ($data['party_members'] ?? [] as $member) {
      $document->partyMembers()->create([
        'name' => $member['name'],
        'role' => $member['role'],
        'gender' => $member['gender'],
        'city' => $member['city'] ?? null,
        'province' => $member['province'] ?? null,
      ]);
    }

    Notification::make()
      ->title($isEditing ? 'Document updated' : 'Document saved')
      ->success()
      ->send();

    $this->showForm = false;
    $this->form->fill();
  }

  public function table(Table $table): Table
  {
    return $table
      ->query(DeedOfAbsoluteSaleDocument::query()->with('partyMembers', 'deedOfAbsoluteSaleTemplate'))
      ->columns([
        TextColumn::make('uuid')
          ->label('ID')
          ->limit(7)
          ->searchable()
          ->tooltip(fn($state) => $state),
        TextColumn::make('sale_price')
          ->label('Sale Price')
          ->sortable()
          ->searchable()
          ->formatStateUsing(function ($state) {
            $formatter = new NumberToWords();
            $numberTransformer = $formatter->getNumberTransformer('en');
            return ucwords($numberTransformer->toWords((int) $state));
          }),
        TextColumn::make('deedOfAbsoluteSaleTemplate.id')
          ->label('Template ID')
          ->sortable()
          ->searchable(),
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
        Action::make('edit')
          ->label('Edit')
          ->icon(Heroicon::PencilSquare)
          ->color('primary')
          ->visible(fn(DeedOfAbsoluteSaleDocument $record) => !$record->locked_at)
          ->action(function (DeedOfAbsoluteSaleDocument $record) {
            $this->form->fill([
              'uuid' => $record->uuid,
              'sale_price' => $record->sale_price,
              'deed_of_absolute_sale_template_id' => $record->deed_of_absolute_sale_template_id,
              'party_members' => $record->partyMembers
                ->map(fn($m) => [
                  'name' => $m->name,
                  'role' => $m->role,
                  'gender' => $m->gender,
                  'city' => $m->city,
                  'province' => $m->province,
                ])
                ->toArray(),
            ]);
            $this->showForm = true;
          }),
        Action::make('delete')
          ->label('Delete')
          ->icon(Heroicon::Trash)
          ->color('danger')
          ->visible(fn(DeedOfAbsoluteSaleDocument $record) => Auth::user()->hasRole('super-admin') && !$record->locked_at)
          ->requiresConfirmation()
          ->modalHeading('Delete this record?')
          ->modalDescription('This action cannot be undone.')
          ->action(function (DeedOfAbsoluteSaleDocument $record) {
            $record->delete();

            Notification::make()
              ->title('Record deleted')
              ->success()
              ->send();
          }),
      ]);
  }

  public function form(Schema $schema): Schema
  {
    return $schema
      ->statePath('data')
      ->components([
        Section::make('Creating New Document')
          ->description('Fill in the details for the new document.')
          ->schema([
            Text::make('sale_price_in_words')
              ->content(function (Get $get): string {
                $value = $get('sale_price');

                if (blank($value)) {
                  return '—';
                }

                $numberToWords = new NumberToWords();
                $transformer = $numberToWords->getNumberTransformer('en');

                return ucwords($transformer->toWords((int) $value)) . ' Pesos Only';
              }),
            TextInput::make('sale_price')
              ->label('Sale Price')
              ->required()
              ->numeric()
              ->prefix('₱')
              ->live(debounce: 500),
            Select::make('deed_of_absolute_sale_template_id')
              ->label('Template')
              ->options(
                DeedOfAbsoluteSaleTemplate::all()
                  ->mapWithKeys(fn($t) => [
                    $t->id => "Template #{$t->id} — {$t->created_at->format('M d, Y')}"
                  ])
              )
              ->searchable()
              ->preload()
              ->required(),
            Repeater::make('party_members')
              ->label('Party Members')
              ->hint('Two principals required: one Principal Vendor and one Principal Vendee.')
              ->hintColor('warning')
              ->hintIcon(Heroicon::InformationCircle)
              ->schema([
                TextInput::make('name')
                  ->label('Name')
                  ->required(),
                Select::make('role')
                  ->label('Role')
                  ->options(PartyMemberRole::class)
                  ->required(),
                TextInput::make('city')
                  ->label('City'),
                TextInput::make('province')
                  ->label('Province'),
              ])
              ->columns(2)
              ->addActionLabel('Add Party Member')
              ->defaultItems(0)
              ->reorderable(false),
            Repeater::make('parcels_of_land')
              ->label('Parcels of Land')
              ->columns(4)
              ->schema([
                TextInput::make('transfer_certificate_number')
                  ->label('Transfer Certificate Number')
                  ->required()
                  ->columnSpan(2),
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
                TextInput::make('area_measurement')
                  ->label('Area Measurement')
                  ->required()
                  ->numeric(),
                Select::make('area_measurement_unit')
                  ->label('Area Measurement Unit')
                  ->options([
                    'sqm' => 'Square Meters',
                    'sqft' => 'Square Feet',
                    'hectares' => 'Hectares',
                    'acres' => 'Acres',
                  ])
                  ->required(),
                Repeater::make('ordinal_directions')
                  ->label('Ordinal Directions')
                  ->columns(4)
                  ->columnSpanFull()
                  ->schema([
                    Select::make('ordinal_direction')
                      ->label('Ordinal Direction')
                      ->options([
                        'north' => 'North',
                        'northeast' => 'Northeast',
                        'east' => 'East',
                        'southeast' => 'Southeast',
                        'south' => 'South',
                        'southwest' => 'Southwest',
                        'west' => 'West',
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
                  ]),
              ])
              ->addActionLabel('Add Parcel of Land')
              ->defaultItems(0)
              ->live(),
          ]),
      ]);
  }
}
