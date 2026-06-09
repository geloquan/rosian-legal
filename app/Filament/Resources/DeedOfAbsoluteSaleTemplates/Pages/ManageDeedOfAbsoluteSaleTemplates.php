<?php

namespace App\Filament\Resources\DeedOfAbsoluteSaleTemplates\Pages;

use App\Filament\Resources\DeedOfAbsoluteSaleTemplates\DeedOfAbsoluteSaleTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDeedOfAbsoluteSaleTemplates extends ManageRecords
{
  protected static string $resource = DeedOfAbsoluteSaleTemplateResource::class;

  protected function getHeaderActions(): array
  {
    return [
      CreateAction::make()
        ->createAnother(false),

    ];
  }
}
