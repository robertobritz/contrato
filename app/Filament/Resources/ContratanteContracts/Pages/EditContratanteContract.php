<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContratanteContracts\Pages;

use App\Filament\Resources\ContratanteContracts\ContratanteContractResource;
use App\Services\ClientContractGenerator;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditContratanteContract extends EditRecord
{
    protected static string $resource = ContratanteContractResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['is_manually_edited'] = true;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Exportar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->url(fn() => route('contracts.export.pdf', $this->record))
                ->openUrlInNewTab(),
            Action::make('export_docx')
                ->label('Exportar DOCX')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->url(fn() => route('contracts.export.docx', $this->record))
                ->openUrlInNewTab(),
            Action::make('regenerar')
                ->label('Regenerar Contrato')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('Regenerar Contrato')
                ->modalDescription('Isso sobrescreverá o conteúdo atual com nova geração a partir do contrato-base. Edições manuais serão perdidas.')
                ->action(function () {
                    app(ClientContractGenerator::class)->regenerate($this->record);
                    Notification::make()
                        ->success()
                        ->title('Contrato regenerado com sucesso!')
                        ->send();

                    $this->fillForm();
                }),
            DeleteAction::make(),
        ];
    }
}
