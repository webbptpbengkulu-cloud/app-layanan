<?php

namespace App\Filament\Resources\WorkUnits;

use App\Filament\Resources\WorkUnits\Pages\CreateWorkUnit;
use App\Filament\Resources\WorkUnits\Pages\EditWorkUnit;
use App\Filament\Resources\WorkUnits\Pages\ListWorkUnits;
use App\Filament\Resources\WorkUnits\Schemas\WorkUnitForm;
use App\Filament\Resources\WorkUnits\Tables\WorkUnitsTable;
use App\Models\WorkUnit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class WorkUnitResource extends Resource
{
    protected static ?string $model = WorkUnit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Unit Kerja';

    protected static ?string $modelLabel = 'Unit Kerja';

    protected static ?string $pluralModelLabel = 'Unit Kerja';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return WorkUnitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkUnitsTable::configure($table);
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
            'index' => ListWorkUnits::route('/'),
            'create' => CreateWorkUnit::route('/create'),
            'edit' => EditWorkUnit::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
