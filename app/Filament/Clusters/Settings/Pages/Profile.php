<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\NavigationGroup;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Profile extends Page implements HasForms
{
  use InteractsWithForms;
  protected static string | BackedEnum | null $navigationIcon = HeroIcon::User;

  protected static ?string $navigationLabel = 'My Profile';
  protected static ?string $title = 'My Profile';
  protected string $view = 'filament.clusters.settings.pages.profile';
  protected static ?string $cluster = SettingsCluster::class;

  public ?array $data = [];

  public function mount(): void
  {
    $this->form->fill(auth()->user()->toArray());
  }

  public function form(Schema $schema): Schema
  {
    return $schema
      ->components([
        Section::make('Profile Information')
          ->description('Update your personal details.')
          ->icon('heroicon-o-user')
          ->schema([
            TextInput::make('name')
              ->required()
              ->maxLength(255),

            TextInput::make('email')
              ->email()
              ->readOnly()
              ->maxLength(255),
          ])->columns(2),

        Section::make('Change Password')
          ->description('Leave blank to keep current password.')
          ->icon('heroicon-o-lock-closed')
          ->schema([
            TextInput::make('current_password')
              ->password()
              ->currentPassword(),

            TextInput::make('password')
              ->password()
              ->revealable()
              ->confirmed(),

            TextInput::make('password_confirmation')
              ->password()
              ->revealable(),
          ])->columns(3),
      ])
      ->statePath('data');
  }

  public
  function save(): void
  {
    $data = $this->form->getState();

    $user = auth()->user();

    // only update password if filled
    if (empty($data['password'])) {
      unset($data['password'], $data['password_confirmation'], $data['current_password']);
    }

    $user->update($data);

    Notification::make()
      ->title('Profile updated successfully!')
      ->success()
      ->send();
  }
}
