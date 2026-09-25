<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Enums\Gender;
use App\Models\Client;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Klien')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Client $record): string => $record->nik ? "NIK: {$record->nik}" : 'NIK: Belum tercatat'),

                TextColumn::make('category.name')
                    ->label('Kategori PPKS')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('gender')
                    ->label('L/P')
                    ->badge()
                    ->color(fn ($state): string => $state === Gender::Male ? 'info' : 'pink')
                    ->formatStateUsing(fn ($state): string => $state === Gender::Male ? 'L' : 'P')
                    ->alignCenter(),

                TextColumn::make('birth_date')
                    ->label('Usia')
                    ->formatStateUsing(fn ($state): string => $state ? Carbon::parse($state)->age.' Thn' : '-')
                    ->alignCenter(),

                TextColumn::make('village.name')
                    ->label('Wilayah Domisili')
                    ->formatStateUsing(fn ($state, Client $record): string => $record->village ? "{$record->village->name} ({$record->village->district?->name})" : '-')
                    ->searchable(),

                TextColumn::make('cases_count')
                    ->counts('cases')
                    ->label('Kasus Rehsos')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('client_category_id')
                    ->label('Kategori PPKS')
                    ->relationship('category', 'name'),

                SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                    ]),

                SelectFilter::make('village_id')
                    ->label('Desa')
                    ->relationship('village', 'name')
                    ->searchable()
                    ->preload(),
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
