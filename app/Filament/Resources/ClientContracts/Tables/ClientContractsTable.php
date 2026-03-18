<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientContracts\Tables;

use App\Models\ClientContract;
use App\Services\ClientContractGenerator;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientContractsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contract.title')
                    ->label('Contrato Base')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.cpf')
                    ->label('CPF'),
                IconColumn::make('is_manually_edited')
                    ->label('Editado')
                    ->boolean(),
                TextColumn::make('generated_at')
                    ->label('Gerado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Editar'),
                Action::make('export_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->url(fn(ClientContract $record) => route('contracts.export.pdf', $record))
                    ->openUrlInNewTab(),
                Action::make('export_docx')
                    ->label('DOCX')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn(ClientContract $record) => route('contracts.export.docx', $record))
                    ->openUrlInNewTab(),
                Action::make('regenerar')
                    ->label('Regenerar')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->modalHeading('Regenerar Contrato')
                    ->modalDescription('Isso sobrescreverá o conteúdo atual. Edições manuais serão perdidas.')
                    ->action(function (ClientContract $record) {
                        app(ClientContractGenerator::class)->regenerate($record);

                        Notification::make()
                            ->success()
                            ->title('Contrato regenerado com sucesso!')
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
