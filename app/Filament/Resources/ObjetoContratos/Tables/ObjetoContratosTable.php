<?php

declare(strict_types=1);

namespace App\Filament\Resources\ObjetoContratos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ObjetoContratosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contratante.name')
                    ->label('Contratante')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contratado.name')
                    ->label('Contratado')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->formatStateUsing(fn(string $state) => $state === 'servico' ? 'Serviço' : 'Produto')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'servico' => 'info',
                        'produto' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('descricao')
                    ->label('Descrição')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('quantidade')
                    ->label('Qtd.')
                    ->numeric(),
                TextColumn::make('unidade')
                    ->label('Unidade')
                    ->toggleable(),
                TextColumn::make('valor')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
