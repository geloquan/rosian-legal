<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
  public string $site_name;
  public string $site_description;
  public string $date_format;
  public string $currency;
  public string $allow_registration;
  public string $city;
  public string $province;

  public static function group(): string
  {
    return 'general';
  }
}
