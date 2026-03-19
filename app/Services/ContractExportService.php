<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ContratanteContract;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class ContractExportService
{
    public function toPdfContent(ContratanteContract $contract): string
    {
        $html = $this->buildHtml($contract);
        $pdf = Pdf::loadHTML($html)->setPaper('a4');

        return $pdf->output();
    }

    public function toDocxContent(ContratanteContract $contract): string
    {
        $word = new PhpWord;
        $section = $word->addSection();

        Html::addHtml($section, $this->stripOuterHtml($contract->body), false, false);

        $tempPath = sys_get_temp_dir() . '/' . uniqid('contract_', true) . '.docx';
        $writer = IOFactory::createWriter($word, 'Word2007');
        $writer->save($tempPath);

        $content = file_get_contents($tempPath);
        unlink($tempPath);

        return $content;
    }

    public function filename(ContratanteContract $contract, string $extension): string
    {
        $title = $contract->contract->title ?? 'contrato';
        $contratante = $contract->contratante->name ?? 'contratante';
        $slug = str($title . '_' . $contratante)->slug('_')->lower();

        return $slug . '.' . $extension;
    }

    private function buildHtml(ContratanteContract $contract): string
    {
        $title = e($contract->contract->title ?? 'Contrato');
        $body = $contract->body;

        return <<<HTML
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 12pt; line-height: 1.6; margin: 2cm; }
                h1 { font-size: 16pt; text-align: center; margin-bottom: 24pt; }
                p { margin: 0 0 10pt 0; text-align: justify; }
            </style>
        </head>
        <body>
            <h1>{$title}</h1>
            {$body}
        </body>
        </html>
        HTML;
    }

    private function stripOuterHtml(string $html): string
    {
        // PhpWord's Html::addHtml expects a body fragment, not a full document
        return preg_replace('/<br\s*\/?>/i', "\n", $html) ?? $html;
    }
}
