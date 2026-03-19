<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contracts\Schemas;

use App\ContractSourceType;
use App\Models\Client;
use App\Services\ContractImportService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informações do Contrato')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),

                        ToggleButtons::make('source_type')
                            ->label('Modo de criação')
                            ->options([
                                ContractSourceType::Upload->value => ContractSourceType::Upload->label(),
                                ContractSourceType::Manual->value => ContractSourceType::Manual->label(),
                            ])
                            ->default(ContractSourceType::Manual->value)
                            ->inline()
                            ->live()
                            ->required()
                            ->hiddenOn('edit'),

                        FileUpload::make('original_file_path')
                            ->label('Arquivo Word (.doc ou .docx)')
                            ->directory('contracts/originals')
                            ->acceptedFileTypes([
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(5120)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (! $state instanceof TemporaryUploadedFile) {
                                    return;
                                }

                                $importService = app(ContractImportService::class);

                                if (blank($get('title'))) {
                                    $set('title', $importService->generateTitle($state));
                                }

                                $set('body', $importService->extractContent($state));
                            })
                            ->hiddenOn('edit')
                            ->visible(fn(callable $get): bool => $get('source_type') === ContractSourceType::Upload->value),
                    ]),

                Section::make('Conteúdo')
                    ->schema([
                        RichEditor::make('body')
                            ->label('Corpo do Contrato')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Use variáveis como $cliente.nome, $cliente.cpf, etc. para dados dinâmicos.')
                            ->hintAction(
                                Action::make('variablesHelper')
                                    ->label('Variáveis disponíveis')
                                    ->icon(Heroicon::OutlinedInformationCircle)
                                    ->modalHeading('Variáveis de Cliente Disponíveis')
                                    ->modalDescription('Copie e cole qualquer variável abaixo no corpo do contrato para preencher automaticamente com os dados do cliente.')
                                    ->modalContent(function (): \Illuminate\Contracts\View\View {
                                        return view('filament.contract-variables-helper', [
                                            'variables' => Client::availableVariableLabels(),
                                        ]);
                                    })
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel('Fechar')
                            ),
                    ]),
            ]);
    }
}
