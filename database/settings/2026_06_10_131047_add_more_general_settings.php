<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
  public function up(): void
  {
    $this->migrator->add('general.measurement_unit', 'sqm');
    $this->migrator->add('general.island', 'Negros Island Region (NIR)');
  }
};
