<?php

namespace App\Filament\Resources\DownloadableForms;

use App\Filament\Resources\DownloadableForms\Pages\CreateDownloadableForm;
use App\Filament\Resources\DownloadableForms\Pages\EditDownloadableForm;
use App\Filament\Resources\DownloadableForms\Pages\ListDownloadableForms;
use App\Filament\Resources\DownloadableForms\Schemas\DownloadableFormForm;
use App\Filament\Resources\DownloadableForms\Tables\DownloadableFormsTable;
use App\Models\DownloadableForm;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class DownloadableFormResource extends Resource
{
    protected static ?string $model = DownloadableForm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static string|UnitEnum|null $navigationGroup = 'Informasi Publik';

    protected static ?string $navigationLabel = 'Formulir Unduhan';

    protected static ?string $modelLabel = 'Formulir Unduhan';

    protected static ?string $pluralModelLabel = 'Formulir Unduhan';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DownloadableFormForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DownloadableFormsTable::configure($table);
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
            'index' => ListDownloadableForms::route('/'),
            'create' => CreateDownloadableForm::route('/create'),
            'edit' => EditDownloadableForm::route('/{record}/edit'),
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
