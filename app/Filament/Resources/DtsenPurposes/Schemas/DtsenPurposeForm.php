<?php

namespace App\Filament\Resources\DtsenPurposes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DtsenPurposeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tujuan Penggunaan SK DTSEN')
                    ->description('Kriteria batasan desil dan masa berlaku surat keterangan')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Tujuan')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Contoh: SPMB, PIP, KIP-K'),
                        TextInput::make('name')
                            ->label('Nama Tujuan Penggunaan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Pendaftaran Sekolah (SPMB / Afirmasi)'),
                        TextInput::make('max_decile')
                            ->label('Maksimal Desil DTSEN')
                            ->helperText('Batas desil tertinggi yang berhak menerima SK (misal: 4 untuk Desil 1–4)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->required()
                            ->default(4),
                        TextInput::make('validity_days')
                            ->label('Masa Berlaku (Hari)')
                            ->helperText('Jumlah hari surat berlaku sebelum kedaluwarsa')
                            ->numeric()
                            ->minValue(1)
                            ->default(30),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
