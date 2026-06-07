<?php

namespace App\Services\Document\DeedOfAbsoluteSale;

use App\Models\DeedOfAbsoluteSaleDocument;
use Illuminate\Support\Facades\Storage;

class TemplateResolver
{

  public function resolve(DeedOfAbsoluteSaleDocument $deed): string
  {
    return storage_path('app/public/' . $deed->deedOfAbsoluteSaleTemplate->document_reference_attachment);
  }
}
