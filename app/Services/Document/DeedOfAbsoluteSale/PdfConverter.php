<?php

namespace App\Services\Document\DeedOfAbsoluteSale;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use TCPDF;

class PdfConverter
{
  private const PAGE_FORMAT   = 'A4';
  private const PAGE_UNIT     = 'mm';
  private const MARGIN_LEFT   = 25;
  private const MARGIN_RIGHT  = 25;
  private const MARGIN_TOP    = 25;
  private const MARGIN_BOTTOM = 25;
  private const FONT_FAMILY   = 'times';
  private const FONT_SIZE     = 12;

  public function convert(string $docxDiskPath): string
  {
    $absolutePath = storage_path('app/public/' . $docxDiskPath);

    if (! file_exists($absolutePath)) {
      throw new RuntimeException("Source file not found: {$absolutePath}");
    }

    $html        = $this->extractHtml($absolutePath);
    $pdfDiskPath = $this->pdfDiskPath($docxDiskPath);
    $pdfAbsPath  = storage_path('app/public/' . $pdfDiskPath);

    Pdf::loadHtml($html)
      ->setPaper('a4', 'portrait')
      ->save($pdfAbsPath);

    if (! file_exists($pdfAbsPath)) {
      throw new RuntimeException("PDF not found after conversion: {$pdfDiskPath}");
    }

    return $pdfDiskPath;
  }
  private function extractHtml(string $absolutePath): string
  {
    $phpWord    = \PhpOffice\PhpWord\IOFactory::load($absolutePath);
    $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');

    ob_start();
    $htmlWriter->save('php://output');
    $rawHtml = ob_get_clean();

    preg_match('/<body[^>]*>(.*?)<\/body>/si', $rawHtml, $matches);

    return $matches[1] ?? $rawHtml;
  }

  private function makePdf(): TCPDF
  {
    $pdf = new TCPDF(
      'P',
      self::PAGE_UNIT,
      self::PAGE_FORMAT,
      true,
      'UTF-8',
      false
    );

    $pdf->SetCreator('DeedOfAbsoluteSale');
    $pdf->SetMargins(self::MARGIN_LEFT, self::MARGIN_TOP, self::MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(true, self::MARGIN_BOTTOM);
    $pdf->SetFont(self::FONT_FAMILY, '', self::FONT_SIZE);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    return $pdf;
  }

  private function pdfDiskPath(string $docxDiskPath): string
  {
    return pathinfo($docxDiskPath, PATHINFO_DIRNAME)
      . '/'
      . pathinfo($docxDiskPath, PATHINFO_FILENAME)
      . '.pdf';
  }
}
