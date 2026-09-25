<?php

namespace App\Filament\Resources\ReferralInstitutions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReferralInstitutionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lembaga')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('type')
                    ->label('Jenis Lembaga')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('contact')
                    ->label('Kontak')
                    ->searchable(),
                TextColumn::make('referrals_count')
                    ->counts('referrals')
                    ->label('Kasus Dirujuk')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Lembaga')
                    ->options([
                        'Panti / Balai Sosial' => 'Panti / Balai Sosial',
                        'Rumah Sakit / RSJ' => 'Rumah Sakit / RSJ',
                        'LKS / LKSA' => 'Lembaga Kesejahteraan Sosial (LKS)',
                        'Sentra Kemensos' => 'Sentra Kemensos RI',
                        'Lembaga Pemasyarakatan' => 'Bapas / Lapas Anak',
                        'Lainnya' => 'Lainnya',
                    ]),
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
