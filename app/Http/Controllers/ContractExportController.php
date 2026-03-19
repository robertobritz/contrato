<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ContratanteContract;
use App\Services\ContractExportService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractExportController extends Controller
{
    public function __construct(private readonly ContractExportService $exportService) {}

    public function pdf(ContratanteContract $contratanteContract): Response
    {
        abort_unless(
            $contratanteContract->contract->user_id === auth()->id(),
            403
        );

        return response(
            $this->exportService->toPdfContent($contratanteContract),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $this->exportService->filename($contratanteContract, 'pdf') . '"',
            ]
        );
    }

    public function docx(ContratanteContract $contratanteContract): StreamedResponse
    {
        abort_unless(
            $contratanteContract->contract->user_id === auth()->id(),
            403
        );

        $filename = $this->exportService->filename($contratanteContract, 'docx');
        $content = $this->exportService->toDocxContent($contratanteContract);

        return response()->streamDownload(
            fn() => print($content),
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
        );
    }
}
