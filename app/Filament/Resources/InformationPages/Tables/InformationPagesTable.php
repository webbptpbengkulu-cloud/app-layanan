<?php

namespace App\Filament\Resources\InformationPages\Tables;

use App\Enums\InformationCategory;
use App\Enums\PublishStatus;
use App\Models\InformationPage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InformationPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Layanan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (InformationPage $record): string => "/layanan/{$record->slug}"),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (InformationCategory $state): string => $state->label()),

                TextColumn::make('serviceType.name')
                    ->label('Jenis Layanan')
                    ->placeholder('-'),

                TextColumn::make('publish_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (PublishStatus $state): string => match ($state) {
                        PublishStatus::Published => 'success',
                        PublishStatus::Draft => 'warning',
                        PublishStatus::Archived => 'gray',
                    })
                    ->formatStateUsing(fn (PublishStatus $state): string => $state->label()),

                TextColumn::make('visits_count')
                    ->counts('visits')
                    ->label('Dilihat')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),

                TextColumn::make('published_at')
                    ->label('Tanggal Terbit')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(collect(InformationCategory::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),

                SelectFilter::make('publish_status')
                    ->label('Status Publikasi')
                    ->options(collect(PublishStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
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
