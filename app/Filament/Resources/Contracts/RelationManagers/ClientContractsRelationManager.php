<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contracts\RelationManagers;

use App\Models\Client;
use App\Models\ClientContract;
use App\Services\ClientContractGenerator;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'clientContracts';

    protected static ?string $title = 'Clientes Vinculados';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('body')
                    ->label('Corpo do Contrato')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('client.name')
            ->columns([
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.cpf')
                    ->label('CPF'),
                IconColumn::make('is_manually_edited')
                    ->label('Editado Manualmente')
                    ->boolean(),
                TextColumn::make('generated_at')
                    ->label('Gerado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('vincular_cliente')
                    ->label('Vincular Cliente')
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Select::make('client_id')
                            ->label('Cliente')
                            ->options(function () {
                                $contractId = $this->getOwnerRecord()->getKey();
                                $existingClientIds = ClientContract::query()
                                    ->where('contract_id', $contractId)
                                    ->pluck('client_id');

                                return Client::query()
                                    ->where('user_id', auth()->id())
                                    ->whereNotIn('id', $existingClientIds)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $contract = $this->getOwnerRecord();
                        $client = Client::findOrFail($data['client_id']);

                        app(ClientContractGenerator::class)->generate($contract, $client);

                        Notification::make()
                            ->success()
                            ->title('Cliente vinculado com sucesso!')
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Editar')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['is_manually_edited'] = true;

                        return $data;
                    }),
                Action::make('regenerar')
                    ->label('Regenerar')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->modalHeading('Regenerar Contrato')
                    ->modalDescription('Isso sobrescreverá o conteúdo atual do contrato com uma nova geração a partir do contrato-base. Edições manuais serão perdidas.')
                    ->action(function (ClientContract $record) {
                        app(ClientContractGenerator::class)->regenerate($record);

                        Notification::make()
                            ->success()
                            ->title('Contrato regenerado com sucesso!')
                            ->send();
                    }),
                DeleteAction::make()
                    ->label('Remover'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
