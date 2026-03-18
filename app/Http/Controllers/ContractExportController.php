<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ClientContract;
use App\Services\ContractExportService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractExportController extends Controller
{
    public function __construct(private readonly ContractExportService $exportService) {}

    public function pdf(ClientContract $clientContract): Response
    {
        abort_unless(
            $clientContract->contract->user_id === auth()->id(),
            403
        );

        return response(
            $this->exportService->toPdfContent($clientContract),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $this->exportService->filename($clientContract, 'pdf') . '"',
            ]
        );
    }

    public function docx(ClientContract $clientContract): StreamedResponse
    {
        abort_unless(
            $clientContract->contract->user_id === auth()->id(),
            403
        );

        $filename = $this->exportService->filename($clientContract, 'docx');
        $content = $this->exportService->toDocxContent($clientContract);

        return response()->streamDownload(
            fn() => print($content),
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
        );
    }
}
