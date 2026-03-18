<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\HTML;

class ContractImportService
{
    public function extractContent(UploadedFile|string $file): string
    {
        if ($file instanceof UploadedFile) {
            $extension = strtolower($file->getClientOriginalExtension());
            $realPath = $file->getRealPath();
        } else {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $realPath = Storage::path($file);
        }

        return match ($extension) {
            'doc', 'docx' => $this->wordToHtml($realPath),
            'html', 'htm' => (string) file_get_contents($realPath),
            default => $this->textToHtml((string) file_get_contents($realPath)),
        };
    }

    public function generateTitle(UploadedFile|string $file): string
    {
        if ($file instanceof UploadedFile) {
            return pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        }

        return pathinfo(basename($file), PATHINFO_FILENAME);
    }

    private function wordToHtml(string $path): string
    {
        $phpWord = IOFactory::load($path);

        ob_start();
        $writer = new HTML($phpWord);
        $writer->save('php://output');
        $rawHtml = (string) ob_get_clean();

        // Extract only the <body> content to avoid full HTML document overhead
        if (preg_match('/<body[^>]*>(.*?)<\/body>/si', $rawHtml, $matches)) {
            return trim($matches[1]);
        }

        return $rawHtml;
    }

    private function textToHtml(string $text): string
    {
        $paragraphs = preg_split('/\n{2,}/', trim($text));

        return implode('', array_map(
            fn (string $paragraph): string => '<p>'.nl2br(e(trim($paragraph)), false).'</p>',
            (array) $paragraphs,
        ));
    }
}
