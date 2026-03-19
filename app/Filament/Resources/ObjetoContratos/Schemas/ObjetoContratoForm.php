<?php

declare(strict_types=1);

namespace App\Filament\Resources\ObjetoContratos\Schemas;

use App\Models\Contratado;
use App\Models\Contratante;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ObjetoContratoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Partes do Contrato')
                    ->columns(2)
                    ->schema([
                        Select::make('contratante_id')
                            ->label('Contratante')
                            ->options(
                                fn() => Contratante::query()
                                    ->where('user_id', auth()->id())
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required(),

                        Select::make('contratado_id')
                            ->label('Contratado')
                            ->options(
                                fn() => Contratado::query()
                                    ->where('user_id', auth()->id())
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Detalhes do Objeto')
                    ->columns(2)
                    ->schema([
                        Select::make('tipo')
                            ->label('Tipo')
                            ->options([
                                'servico' => 'Serviço',
                                'produto' => 'Produto',
                            ])
                            ->required(),

                        TextInput::make('descricao')
                            ->label('Descrição')
                            ->required()
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        TextInput::make('quantidade')
                            ->label('Quantidade')
                            ->numeric()
                            ->minValue(0)
                            ->default(1)
                            ->required(),

                        TextInput::make('unidade')
                            ->label('Unidade')
                            ->placeholder('ex: un, hr, kg, m²')
                            ->maxLength(20),

                        TextInput::make('valor')
                            ->label('Valor (R$)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('R$')
                            ->required(),
                    ]),
            ]);
    }
}
