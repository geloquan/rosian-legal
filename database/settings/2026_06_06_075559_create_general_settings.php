<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
  public function up(): void
  {
    $this->migrator->add('general.site_name', 'Rosian Legal');
    $this->migrator->add('general.site_description', '');
    $this->migrator->add('general.date_format', 'Y-m-d');
    $this->migrator->add('general.currency', 'PHP');
    $this->migrator->add('general.allow_registration', false);
    $this->migrator->add('general.city', 'Bacolod');
    $this->migrator->add('general.province','Negros Occidental');
  }
};
