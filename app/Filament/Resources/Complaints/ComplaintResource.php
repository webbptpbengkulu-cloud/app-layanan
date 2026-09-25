<?php

namespace App\Filament\Resources\Complaints;

use App\Filament\Resources\Complaints\Pages\CreateComplaint;
use App\Filament\Resources\Complaints\Pages\EditComplaint;
use App\Filament\Resources\Complaints\Pages\ListComplaints;
use App\Filament\Resources\Complaints\RelationManagers\AttachmentsRelationManager;
use App\Filament\Resources\Complaints\RelationManagers\DispositionsRelationManager;
use App\Filament\Resources\Complaints\RelationManagers\StatusHistoriesRelationManager;
use App\Filament\Resources\Complaints\Schemas\ComplaintForm;
use App\Filament\Resources\Complaints\Tables\ComplaintsTable;
use App\Models\Complaint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaduan';

    protected static ?string $navigationLabel = 'Layanan Pengaduan';

    protected static ?string $modelLabel = 'Pengaduan';

    protected static ?string $pluralModelLabel = 'Pengaduan Masyarakat';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'complaint_number';

    public static function form(Schema $schema): Schema
    {
        return ComplaintForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComplaintsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AttachmentsRelationManager::class,
            StatusHistoriesRelationManager::class,
            DispositionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComplaints::route('/'),
            'create' => CreateComplaint::route('/create'),
            'edit' => EditComplaint::route('/{record}/edit'),
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
        return ['complaint_number', 'reporter_name', 'reporter_phone', 'description'];
    }
}
