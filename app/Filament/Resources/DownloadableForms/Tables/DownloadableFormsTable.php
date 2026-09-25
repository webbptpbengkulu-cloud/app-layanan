<?php

namespace App\Filament\Resources\DownloadableForms\Tables;

use App\Models\DownloadableForm;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DownloadableFormsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Formulir')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('informationPage.title')
                    ->label('Layanan Terkait')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('version')
                    ->label('Versi')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                IconColumn::make('is_current')
                    ->label('Versi Aktif')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Unggah')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('information_page_id')
                    ->label('Layanan Terkait')
                    ->relationship('informationPage', 'title'),

                SelectFilter::make('is_current')
                    ->label('Status Versi')
                    ->options([
                        '1' => 'Versi Aktif',
                        '0' => 'Versi Arsip',
                    ]),
            ])
            ->recordActions([
                Action::make('unduh')
                    ->label('Unduh File')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (DownloadableForm $record): bool => ! empty($record->file_path))
                    ->url(fn (DownloadableForm $record): string => Storage::disk('public')->url($record->file_path), shouldOpenInNewTab: true),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
