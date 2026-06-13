<?php

namespace App\Services\Document;

use App\Models\DeedOfAbsoluteSaleDocument;
use App\Services\Document\DeedOfAbsoluteSale\DocumentBuilder;
use App\Services\Document\DeedOfAbsoluteSale\PdfConverter;
use Illuminate\Support\Facades\Log;

class DocumentService
{
  public function __construct(
    private readonly DocumentBuilder $builder,
    private readonly PdfConverter    $converter,
  )
  {
  }

  public function generatePdf(DeedOfAbsoluteSaleDocument $deed): string
  {
//    Log::info('Generating PDF for deed ID: ' . $deed->uuid . ' using template: ' . $deed->deedOfAbsoluteSaleTemplate->document_reference_attachment . ' does the file exist? ' . (file_exists(storage_path('app/public/' . $deed->deedOfAbsoluteSaleTemplate->document_reference_attachment)) ? 'Yes' : 'No'));

    $wordPath = $this->builder->build($deed);

    $pdfPath = $this->converter->convert($wordPath);

    $deed->exported_document_attachment = $pdfPath;
    $deed->save();

    return $pdfPath;
  }
}
