<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clients\Schemas;

use App\MaritalStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
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
                        TextInput::make('cpf')
                            ->label('CPF')
                            ->required()
                            ->mask('999.999.999-99')
                            ->maxLength(14),
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
