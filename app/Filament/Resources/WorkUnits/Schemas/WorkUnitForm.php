<?php

namespace App\Filament\Resources\WorkUnits\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WorkUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Unit Kerja')
                    ->description('Data bidang atau bagian di lingkungan Dinas Sosial')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Unit')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Contoh: SEKRETARIAT, LINJAMSOS'),
                        TextInput::make('name')
                            ->label('Nama Unit Kerja')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Bidang Perlindungan dan Jaminan Sosial'),
                        Textarea::make('description')
                            ->label('Deskripsi / Uraian Tugas')
                            ->rows(3)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Unit kerja yang non-aktif tidak dapat dipilih saat pembuatan akun baru'),
                    ])
                    ->columns(2),
            ]);
    }
}
