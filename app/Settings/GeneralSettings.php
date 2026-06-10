<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
  // app settings
  public string $site_name;
  public string $site_description;

  // formatting settings
  public string $date_format;
  public string $currency;

  // document template settings
  public string $city;
  public string $province;
  public string $measurement_unit;
  public string $island;

  public static function group(): string
  {
    return 'general';
  }
}
