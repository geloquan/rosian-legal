<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class GeneralSetting extends SettingsPage
{
  protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

  protected static string $settings = GeneralSettings::class;
  protected static ?string $cluster = SettingsCluster::class;
  protected static ?string $title = 'General';

  public function form(Schema $schema): Schema
  {
    return $schema
      ->components([
        Section::make('Site Information')
          ->schema([
            TextInput::make('site_name')
              ->label('Site Name')
              ->required()
              ->maxLength(255),

            Textarea::make('site_description')
              ->label('Description')
              ->rows(3)
              ->maxLength(500)
              ->columnSpanFull(),
          ])->columns(2),

        Section::make('Localization')
          ->schema([
            Select::make('date_format')
              ->label('Date Format')
              ->options([
                'Y-m-d' => '2025-01-31 (Y-m-d)',
                'd/m/Y' => '31/01/2025 (d/m/Y)',
                'm/d/Y' => '01/31/2025 (m/d/Y)',
                'd M Y' => '31 Jan 2025 (d M Y)',
              ])
              ->required(),

            Select::make('currency')
              ->label('Currency')
              ->options([
                'PHP' => 'Philippine Peso (PHP)',
                'USD' => 'US Dollar (USD)',
                'EUR' => 'Euro (EUR)',
              ])
              ->required(),

            Select::make('measurement_unit')
              ->label('Land Area Unit')
              ->options([
                'sqm' => 'Square Meter (sqm)',
                'sqft' => 'Square Foot (sq ft)',
                'ha' => 'Hectare (ha)',
                'acre' => 'Acre',
              ])
              ->required(),
          ])->columns(2),

        Section::make('Location')
          ->schema([
            TextInput::make('island')
              ->label('Island Region')
              ->required()
              ->columnSpanFull(),
            TextInput::make('city')
              ->label('City')
              ->required()
              ->maxLength(100),

            TextInput::make('province')
              ->label('Province')
              ->required()
              ->maxLength(100)
              ->columnSpanFull(),
          ])->columns(2),
      ]);
  }
}
