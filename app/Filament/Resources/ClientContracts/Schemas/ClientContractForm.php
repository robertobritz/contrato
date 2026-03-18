<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientContracts\Schemas;

use App\Models\Client;
use App\Models\Contract;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
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

                        Select::make('client_id')
                            ->label('Cliente')
                            ->options(
                                fn() => Client::query()
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

                        Placeholder::make('client_name')
                            ->label('Cliente')
                            ->content(fn($record) => $record?->client?->name ?? '-')
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
