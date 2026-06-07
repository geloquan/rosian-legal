<?php

namespace App\Services\Document\DeedOfAbsoluteSale;

use App\Models\DeedOfAbsoluteSaleDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentBuilder
{
  public function __construct(
    private readonly TemplateResolver $resolver,
    private readonly VariableExtractor $extractor,
  ) {}

  public function build(DeedOfAbsoluteSaleDocument $deed): string
  {
    if ($deed->partyMembers->isEmpty()) {
      Log::error(
        'Deed document must have at least one party member. Deed ID: ' . $deed->uuid
      );
    }

    $template = new TemplateProcessor(
      $this->resolver->resolve($deed)
    );

    $variables = $this->extractor->extract($deed);

    foreach ($variables as $key => $value) {
      $template->setComplexValue($key, $value);
    }

    $relativePath = "app/deeds/deed_{$deed->uuid}.docx";
    $absolutePath = storage_path('app/public/' . $relativePath);

    if (! is_dir(dirname($absolutePath))) {
      mkdir(dirname($absolutePath), 0755, true);
    }

    $template->saveAs($absolutePath);

    return $relativePath;
  }
}
