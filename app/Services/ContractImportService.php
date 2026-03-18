<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ContractImportService
{
    public function extractContent(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $content = file_get_contents($file->getRealPath());

        return match ($extension) {
            'html', 'htm' => $content,
            'txt' => $this->textToHtml($content),
            default => $this->textToHtml($content),
        };
    }

    public function generateTitle(UploadedFile $file): string
    {
        return pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    }

    private function textToHtml(string $text): string
    {
        $paragraphs = preg_split('/\n{2,}/', trim($text));

        return implode('', array_map(
            fn (string $paragraph): string => '<p>'.nl2br(e(trim($paragraph)), false).'</p>',
            $paragraphs,
        ));
    }
}
