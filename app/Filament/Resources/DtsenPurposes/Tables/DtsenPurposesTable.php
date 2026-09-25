<?php

namespace App\Filament\Resources\DtsenPurposes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DtsenPurposesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Tujuan Penggunaan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('max_decile')
                    ->label('Batas Maks Desil')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 2 => 'danger',
                        $state <= 4 => 'warning',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (int $state): string => "Desil ≤ {$state}")
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('validity_days')
                    ->label('Masa Berlaku')
                    ->suffix(' Hari')
                    ->alignCenter()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status Aktif')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Non-Aktif',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
