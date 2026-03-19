<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContratanteContracts\Schemas;

use App\Models\Contract;
use App\Models\Contratado;
use App\Models\Contratante;
use App\Models\ObjetoContrato;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ContratanteContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informações')
                    ->columns(3)
                    ->schema([
                        Select::make('contract_id')
                            ->label('Contrato Base')
                            ->options(
                                fn() => Contract::query()
                                    ->where('user_id', auth()->id())
                                    ->orderBy('title')
                                    ->pluck('title', 'id')
                            )
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $body = Contract::find($state)?->body;
                                $set('body', $body ?? '');
                            })
                            ->hiddenOn('edit'),

                        Select::make('contratante_id')
                            ->label('Contratante')
                            ->options(
                                fn() => Contratante::query()
                                    ->where('user_id', auth()->id())
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required()
                            ->live()
                            ->hiddenOn('edit'),

                        Select::make('contratado_id')
                            ->label('Contratado')
                            ->options(
                                fn() => Contratado::query()
                                    ->where('user_id', auth()->id())
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required()
                            ->live()
                            ->hiddenOn('edit'),

                        Select::make('objeto_contrato_id')
                            ->label('Objeto de Contrato')
                            ->options(
                                fn(Get $get) => ($get('contratante_id') && $get('contratado_id'))
                                    ? ObjetoContrato::query()
                                    ->where('contratante_id', $get('contratante_id'))
                                    ->where('contratado_id', $get('contratado_id'))
                                    ->orderBy('descricao')
                                    ->pluck('descricao', 'id')
                                    : []
                            )
                            ->disabled(fn(Get $get) => ! $get('contratante_id') || ! $get('contratado_id'))
                            ->placeholder(
                                fn(Get $get) => (! $get('contratante_id') || ! $get('contratado_id'))
                                    ? 'Selecione primeiro o Contratante e o Contratado'
                                    : 'Selecione uma opção'
                            )
                            ->searchable()
                            ->required()
                            ->hiddenOn('edit'),

                        Placeholder::make('contract_title')
                            ->label('Contrato Base')
                            ->content(fn($record) => $record?->contract?->title ?? '-')
                            ->hiddenOn('create'),

                        Placeholder::make('contratante_name')
                            ->label('Contratante')
                            ->content(fn($record) => $record?->contratante?->name ?? '-')
                            ->hiddenOn('create'),

                        Placeholder::make('contratado_name')
                            ->label('Contratado')
                            ->content(fn($record) => $record?->contratado?->name ?? '-')
                            ->hiddenOn('create'),

                        Placeholder::make('objeto_contrato_descricao')
                            ->label('Objeto de Contrato')
                            ->content(fn($record) => $record?->objetoContrato?->descricao ?? '-')
                            ->hiddenOn('create'),

                        Placeholder::make('generated_at_display')
                            ->label('Gerado em')
                            ->content(fn($record) => $record?->generated_at?->format('d/m/Y H:i') ?? '-'),

                        Placeholder::make('is_manually_edited_display')
                            ->label('Editado Manualmente')
                            ->content(fn($record) => $record?->is_manually_edited ? 'Sim' : 'Não'),
                    ]),

                Section::make('Conteúdo do Contrato')
                    ->schema([
                        RichEditor::make('body')
                            ->label('Corpo do Contrato')
                            ->required(fn(string $operation): bool => $operation === 'edit')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
