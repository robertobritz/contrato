<?php

declare(strict_types=1);

namespace App\Filament\Resources\Contratantes;

use App\Filament\Resources\Contratantes\Pages\CreateContratante;
use App\Filament\Resources\Contratantes\Pages\EditContratante;
use App\Filament\Resources\Contratantes\Pages\ListContratantes;
use App\Filament\Resources\Contratantes\Schemas\ContratanteForm;
use App\Filament\Resources\Contratantes\Tables\ContratantesTable;
use App\Models\Contratante;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContratanteResource extends Resource
{
    protected static ?string $model = Contratante::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $modelLabel = 'Contratante';

    protected static ?string $pluralModelLabel = 'Contratantes';

    public static function form(Schema $schema): Schema
    {
        return ContratanteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContratantesTable::configure($table);
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
            'index' => ListContratantes::route('/'),
            'create' => CreateContratante::route('/create'),
            'edit' => EditContratante::route('/{record}/edit'),
        ];
    }
}
