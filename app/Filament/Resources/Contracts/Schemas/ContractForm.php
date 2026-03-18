<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contracts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Contrato')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('original_file_path')
                            ->label('Arquivo Original (opcional)')
                            ->directory('contracts/originals')
                            ->acceptedFileTypes([
                                'text/plain',
                                'text/html',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(5120)
                            ->hiddenOn('edit'),
                    ]),
                Section::make('Conteúdo')
                    ->schema([
                        RichEditor::make('body')
                            ->label('Corpo do Contrato')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Use variáveis como $cliente.nome, $cliente.cpf, etc. para dados dinâmicos.'),
                    ]),
            ]);
    }
}
