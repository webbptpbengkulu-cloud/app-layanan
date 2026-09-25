<?php

namespace App\Filament\Resources\Complaints\RelationManagers;

use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DispositionsRelationManager extends RelationManager
{
    protected static string $relationship = 'dispositions';

    protected static ?string $title = 'Riwayat Disposisi Pengaduan';

    protected static ?string $modelLabel = 'Disposisi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('to_user_id')
                    ->label('Tujuan Disposisi (Petugas)')
                    ->options(User::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Textarea::make('notes')
                    ->label('Instruksi / Catatan Disposisi')
                    ->required()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('notes')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu Disposisi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('fromUser.name')
                    ->label('Dari')
                    ->weight('bold')
                    ->placeholder('Sistem'),
                TextColumn::make('toUser.name')
                    ->label('Kepada')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('notes')
                    ->label('Instruksi Disposisi')
                    ->wrap(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Buat Disposisi')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['from_user_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
