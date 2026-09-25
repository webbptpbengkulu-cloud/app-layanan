<?php

namespace App\Filament\Resources\InformationPages;

use App\Filament\Resources\InformationPages\Pages\CreateInformationPage;
use App\Filament\Resources\InformationPages\Pages\EditInformationPage;
use App\Filament\Resources\InformationPages\Pages\ListInformationPages;
use App\Filament\Resources\InformationPages\Schemas\InformationPageForm;
use App\Filament\Resources\InformationPages\Tables\InformationPagesTable;
use App\Models\InformationPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class InformationPageResource extends Resource
{
    protected static ?string $model = InformationPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Informasi Publik';

    protected static ?string $navigationLabel = 'Halaman Informasi';

    protected static ?string $modelLabel = 'Informasi Layanan';

    protected static ?string $pluralModelLabel = 'Halaman Informasi Publik';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return InformationPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InformationPagesTable::configure($table);
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
            'index' => ListInformationPages::route('/'),
            'create' => CreateInformationPage::route('/create'),
            'edit' => EditInformationPage::route('/{record}/edit'),
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
