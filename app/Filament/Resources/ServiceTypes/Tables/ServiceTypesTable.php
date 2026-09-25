<?php

namespace App\Filament\Resources\ServiceTypes\Tables;

use App\Enums\ServiceHandler;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ServiceTypesTable
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
                    ->label('Nama Layanan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('handler')
                    ->label('Penangan')
                    ->badge()
                    ->color(fn (ServiceHandler $state): string => match ($state) {
                        ServiceHandler::Dtsen => 'primary',
                        ServiceHandler::Pbi => 'success',
                        ServiceHandler::Generic => 'gray',
                    })
                    ->formatStateUsing(fn (ServiceHandler $state): string => $state->label()),
                TextColumn::make('sla_days')
                    ->label('SLA')
                    ->suffix(' Hari')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('requirements_count')
                    ->counts('requirements')
                    ->label('Persyaratan')
                    ->badge()
                    ->alignCenter(),
                IconColumn::make('needs_assessment')
                    ->label('Assessment')
                    ->boolean()
                    ->alignCenter(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('handler')
                    ->label('Tipe Penangan')
                    ->options(collect(ServiceHandler::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
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
