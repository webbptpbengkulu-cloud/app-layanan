<?php

namespace App\Filament\Resources\ServiceRequests\RelationManagers;

use App\Enums\ServiceRequestStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'statusHistories';

    protected static ?string $title = 'Riwayat Status Permohonan';

    protected static ?string $modelLabel = 'Riwayat Status';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('to_status')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu Kejadian')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('from_status')
                    ->label('Status Sebelumnya')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(function (?string $state): string {
                        if (! $state) {
                            return 'Awal';
                        }
                        $enum = ServiceRequestStatus::tryFrom($state);

                        return $enum ? $enum->label() : $state;
                    }),

                TextColumn::make('to_status')
                    ->label('Status Menjadi')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(function (string $state): string {
                        $enum = ServiceRequestStatus::tryFrom($state);

                        return $enum ? $enum->label() : $state;
                    }),

                TextColumn::make('notes')
                    ->label('Catatan Perubahan')
                    ->placeholder('-')
                    ->wrap(),

                TextColumn::make('user.name')
                    ->label('Petugas / Pelaku')
                    ->placeholder('Sistem Otomatis')
                    ->badge()
                    ->color('info'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
