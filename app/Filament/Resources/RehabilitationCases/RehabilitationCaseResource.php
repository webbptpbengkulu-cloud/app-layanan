<?php

namespace App\Filament\Resources\RehabilitationCases;

use App\Filament\Resources\RehabilitationCases\Pages\CreateRehabilitationCase;
use App\Filament\Resources\RehabilitationCases\Pages\EditRehabilitationCase;
use App\Filament\Resources\RehabilitationCases\Pages\ListRehabilitationCases;
use App\Filament\Resources\RehabilitationCases\RelationManagers\AssessmentsRelationManager;
use App\Filament\Resources\RehabilitationCases\RelationManagers\MonitoringRecordsRelationManager;
use App\Filament\Resources\RehabilitationCases\RelationManagers\ReferralsRelationManager;
use App\Filament\Resources\RehabilitationCases\Schemas\RehabilitationCaseForm;
use App\Filament\Resources\RehabilitationCases\Tables\RehabilitationCasesTable;
use App\Models\RehabilitationCase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class RehabilitationCaseResource extends Resource
{
    protected static ?string $model = RehabilitationCase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static string|UnitEnum|null $navigationGroup = 'Rehabilitasi Sosial';

    protected static ?string $navigationLabel = 'Kasus Rehabilitasi';

    protected static ?string $modelLabel = 'Kasus Rehabilitasi';

    protected static ?string $pluralModelLabel = 'Kasus Rehabilitasi';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'case_number';

    public static function form(Schema $schema): Schema
    {
        return RehabilitationCaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RehabilitationCasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AssessmentsRelationManager::class,
            ReferralsRelationManager::class,
            MonitoringRecordsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRehabilitationCases::route('/'),
            'create' => CreateRehabilitationCase::route('/create'),
            'edit' => EditRehabilitationCase::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['case_number', 'client.name', 'client.nik'];
    }
}
