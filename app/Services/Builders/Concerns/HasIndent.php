<?php

namespace App\Services\Builders\Concerns;

use PhpOffice\PhpWord\Element\TextRun;

trait HasIndent
{
  public function indent(TextRun $textRun): void
  {
    $textRun->addText('______', ['bold' => true, 'color' => 'FFFFFF']);
  }
}
