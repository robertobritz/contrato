<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContratanteContracts\Schemas;

use App\Models\Contract;
use App\Models\Contratante;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContratanteContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informações')
                    ->columns(2)
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
                            ->hiddenOn('edit'),

                        Placeholder::make('contract_title')
                            ->label('Contrato Base')
                            ->content(fn($record) => $record?->contract?->title ?? '-')
                            ->hiddenOn('create'),

                        Placeholder::make('contratante_name')
                            ->label('Contratante')
                            ->content(fn($record) => $record?->contratante?->name ?? '-')
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
                            ->hiddenOn('create')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
