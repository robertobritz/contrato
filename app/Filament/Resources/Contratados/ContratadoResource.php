<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contratados;

use App\Filament\Resources\Contratados\Pages\CreateContratado;
use App\Filament\Resources\Contratados\Pages\EditContratado;
use App\Filament\Resources\Contratados\Pages\ListContratados;
use App\Filament\Resources\Contratados\Schemas\ContratadoForm;
use App\Filament\Resources\Contratados\Tables\ContratadosTable;
use App\Models\Contratado;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContratadoResource extends Resource
{
    protected static ?string $model = Contratado::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $modelLabel = 'Contratado';

    protected static ?string $pluralModelLabel = 'Contratados';

    public static function form(Schema $schema): Schema
    {
        return ContratadoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContratadosTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContratados::route('/'),
            'create' => CreateContratado::route('/create'),
            'edit' => EditContratado::route('/{record}/edit'),
        ];
    }
}
