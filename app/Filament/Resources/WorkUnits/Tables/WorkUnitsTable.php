<?php

namespace App\Filament\Resources\WorkUnits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WorkUnitsTable
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
                    ->label('Nama Unit Kerja')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Jumlah Pegawai')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
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
