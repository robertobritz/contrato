<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contracts\RelationManagers;

use App\Models\Contratante;
use App\Models\ContratanteContract;
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
    protected static string $relationship = 'contratanteContracts';

    protected static ?string $title = 'Contratantes Vinculados';

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
            ->recordTitleAttribute('contratante.name')
            ->columns([
                TextColumn::make('contratante.name')
                    ->label('Contratante')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contratante.cpf')
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
                Action::make('vincular_contratante')
                    ->label('Vincular Contratante')
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Select::make('contratante_id')
                            ->label('Contratante')
                            ->options(function () {
                                $contractId = $this->getOwnerRecord()->getKey();
                                $existingContratanteIds = ContratanteContract::query()
                                    ->where('contract_id', $contractId)
                                    ->pluck('contratante_id');

                                return Contratante::query()
                                    ->where('user_id', auth()->id())
                                    ->whereNotIn('id', $existingContratanteIds)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $contract = $this->getOwnerRecord();
                        $contratante = Contratante::findOrFail($data['contratante_id']);

                        app(ClientContractGenerator::class)->generate($contract, $contratante);

                        Notification::make()
                            ->success()
                            ->title('Contratante vinculado com sucesso!')
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
                    ->action(function (ContratanteContract $record) {
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
