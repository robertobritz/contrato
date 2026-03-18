<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contracts\Pages;

use App\Filament\Resources\Contracts\ContractResource;
use App\Services\ContractImportService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        if (isset($data['original_file_path']) && $data['original_file_path'] instanceof UploadedFile) {
            $importService = app(ContractImportService::class);
            $file = $data['original_file_path'];

            if (empty($data['body'])) {
                $data['body'] = $importService->extractContent($file);
            }

            if (empty($data['title'])) {
                $data['title'] = $importService->generateTitle($file);
            }
        }

        return $data;
    }
}
