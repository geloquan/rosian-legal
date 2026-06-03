<?php

namespace App\Filament\Resources\PrinterConnections\Pages;

use App\Filament\Resources\PrinterConnections\PrinterConnectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePrinterConnections extends ManageRecords
{
  protected static string $resource = PrinterConnectionResource::class;

  protected function getHeaderActions(): array
  {
    return [
      CreateAction::make(),
    ];
  }
}
