<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePermissions extends ManageRecords
{
  protected static string $resource = PermissionResource::class;

  protected function getHeaderActions(): array
  {
    return [
      CreateAction::make(),
    ];
  }

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $data['guard_name'] = 'web';

    return $data;
  }
}
