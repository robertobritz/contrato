<?php

declare(strict_types=1);

use App\Services\ContractImportService;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

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

it('extracts content from a docx file', function () {
    $phpWord = new PhpWord;
    $section = $phpWord->addSection();
    $section->addText('Contrato para $cliente.nome');

    $tempPath = tempnam(sys_get_temp_dir(), 'docx');
    rename($tempPath, $tempPath .= '.docx');

    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($tempPath);

    $file = new UploadedFile($tempPath, 'contrato.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', null, true);

    $service = new ContractImportService;
    $result = $service->extractContent($file);

    expect($result)->toContain('$cliente.nome');

    unlink($tempPath);
});

it('generates title from filename', function () {
    $file = UploadedFile::fake()->createWithContent('contrato_prestacao_servicos.txt', 'content');

    $service = new ContractImportService;
    $title = $service->generateTitle($file);

    expect($title)->toBe('contrato_prestacao_servicos');
});
