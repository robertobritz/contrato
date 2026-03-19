<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contratados\Schemas;

use App\DocumentType;
use App\MaritalStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class ContratadoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados Pessoais')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(20),
                        ToggleButtons::make('document_type')
                            ->label('Tipo de Documento')
                            ->options(DocumentType::class)
                            ->default(DocumentType::CPF->value)
                            ->inline()
                            ->live()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('cpf')
                            ->label(
                                fn(Get $get) => $get('document_type') instanceof DocumentType
                                    ? ($get('document_type') === DocumentType::CNPJ ? 'CNPJ' : 'CPF')
                                    : ($get('document_type') === DocumentType::CNPJ->value ? 'CNPJ' : 'CPF')
                            )
                            ->required()
                            ->mask(RawJs::make("\$wire.get('data.document_type') === 'cnpj' ? '99.999.999/9999-99' : '999.999.999-99'"))
                            ->maxLength(
                                fn(Get $get) => ($get('document_type') === DocumentType::CNPJ || $get('document_type') === DocumentType::CNPJ->value)
                                    ? DocumentType::CNPJ->maxLength()
                                    : DocumentType::CPF->maxLength()
                            )
                            ->placeholder(
                                fn(Get $get) => ($get('document_type') === DocumentType::CNPJ || $get('document_type') === DocumentType::CNPJ->value)
                                    ? DocumentType::CNPJ->placeholder()
                                    : DocumentType::CPF->placeholder()
                            ),
                        TextInput::make('rg')
                            ->label('RG')
                            ->maxLength(20),
                        DatePicker::make('birth_date')
                            ->label('Data de Nascimento')
                            ->displayFormat('d/m/Y'),
                        TextInput::make('nationality')
                            ->label('Nacionalidade')
                            ->maxLength(255),
                        Select::make('marital_status')
                            ->label('Estado Civil')
                            ->options(MaritalStatus::class),
                        TextInput::make('profession')
                            ->label('Profissão')
                            ->maxLength(255),
                    ]),
                Section::make('Endereço')
                    ->columns(2)
                    ->schema([
                        TextInput::make('address')
                            ->label('Endereço')
                            ->maxLength(255),
                        TextInput::make('address_number')
                            ->label('Número')
                            ->maxLength(20),
                        TextInput::make('address_complement')
                            ->label('Complemento')
                            ->maxLength(255),
                        TextInput::make('neighborhood')
                            ->label('Bairro')
                            ->maxLength(255),
                        TextInput::make('city')
                            ->label('Cidade')
                            ->maxLength(255),
                        TextInput::make('state')
                            ->label('Estado')
                            ->maxLength(2),
                        TextInput::make('zip_code')
                            ->label('CEP')
                            ->mask('99999-999')
                            ->maxLength(9),
                    ]),
            ]);
    }
}
