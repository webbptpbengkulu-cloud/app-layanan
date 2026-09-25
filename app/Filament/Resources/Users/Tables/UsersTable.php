<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record): string => $record->email),
                TextColumn::make('roles.name')
                    ->label('Peran (Role)')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'administrator' => 'danger',
                        'pimpinan' => 'warning',
                        'pejabat_penandatangan' => 'purple',
                        'petugas_dinsos' => 'primary',
                        'operator_wilayah' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('workUnit.name')
                    ->label('Unit Kerja')
                    ->placeholder('-')
                    ->limit(25)
                    ->tooltip(fn ($record): ?string => $record->workUnit?->name),
                TextColumn::make('district.name')
                    ->label('Kecamatan')
                    ->placeholder('-'),
                TextColumn::make('village.name')
                    ->label('Desa')
                    ->placeholder('-'),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Peran (Role)')
                    ->relationship('roles', 'name'),
                SelectFilter::make('work_unit_id')
                    ->label('Unit Kerja')
                    ->relationship('workUnit', 'name'),
                SelectFilter::make('district_id')
                    ->label('Kecamatan')
                    ->relationship('district', 'name'),
                SelectFilter::make('is_active')
                    ->label('Status Akun')
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
