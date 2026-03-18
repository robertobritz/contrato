<?php

declare(strict_types=1);

use App\Services\ContractImportService;
use Illuminate\Http\UploadedFile;

it('extracts content from a txt file', function () {
    $content = 'Contrato de prestação de serviços para $cliente.nome';
    $file = UploadedFile::fake()->createWithContent('contrato.txt', $content);

    $service = new ContractImportService;
    $result = $service->extractContent($file);

    expect($result)->toBe('<p>Contrato de prestação de serviços para $cliente.nome</p>');
});

it('extracts content from an html file', function () {
    $content = '<h1>Contrato</h1><p>Para $cliente.nome</p>';
    $file = UploadedFile::fake()->createWithContent('contrato.html', $content);

    $service = new ContractImportService;
    $result = $service->extractContent($file);

    expect($result)->toBe('<h1>Contrato</h1><p>Para $cliente.nome</p>');
});

it('generates title from filename', function () {
    $file = UploadedFile::fake()->createWithContent('contrato_prestacao_servicos.txt', 'content');

    $service = new ContractImportService;
    $title = $service->generateTitle($file);

    expect($title)->toBe('contrato_prestacao_servicos');
});
