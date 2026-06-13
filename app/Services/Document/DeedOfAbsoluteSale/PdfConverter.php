<?php

namespace App\Services\Document\DeedOfAbsoluteSale;

use Barryvdh\DomPDF\Facade\Pdf;
use RuntimeException;

class PdfConverter
{
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
      ->setOptions([
        'isHtml5ParserEnabled' => true,
        'defaultFont'          => 'times',
      ])
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

    return $this->injectIndentStyles($rawHtml);
  }

  private function injectIndentStyles(string $html): string
  {
    // Inject 1-inch page margins and reset body spacing
    $pageStyle = '
      <style>
        @page { margin: 1in; }
        body  { margin: 0; padding: 0; }
      </style>
    ';

    $html = str_replace('</head>', $pageStyle . '</head>', $html);

    // Duplicate margin-left as padding-left on <p> tags (DomPDF renders it more reliably)
    $html = preg_replace_callback(
      '/<p([^>]*style="([^"]*)"[^>]*)>/i',
      function (array $m) {
        $tag   = $m[1];
        $style = $m[2];

        if (preg_match('/margin-left\s*:\s*([\d.]+)(pt|px|cm|mm|in)/i', $style, $indent)) {
          $value = $indent[1] . $indent[2];
          $style .= ";padding-left:{$value}";
          $tag   = str_replace($m[2], $style, $tag);
        }

        return "<p{$tag}>";
      },
      $html
    );

    // Convert literal tab characters to fixed-width inline spans
    $html = preg_replace('/\t/', '<span style="display:inline-block;width:2em;"></span>', $html);

    return $html;
  }

  private function pdfDiskPath(string $docxDiskPath): string
  {
    return pathinfo($docxDiskPath, PATHINFO_DIRNAME)
      . '/'
      . pathinfo($docxDiskPath, PATHINFO_FILENAME)
      . '.pdf';
  }
}
