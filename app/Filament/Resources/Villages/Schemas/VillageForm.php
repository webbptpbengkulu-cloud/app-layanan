<?php

namespace App\Filament\Resources\Villages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VillageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Desa / Kelurahan')
                    ->description('Pilih kecamatan induk dan tentukan kode serta nama desa/kelurahan')
                    ->schema([
                        Select::make('district_id')
                            ->label('Kecamatan Induk')
                            ->relationship('district', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('code')
                            ->label('Kode Desa / Kelurahan')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->placeholder('Contoh: 35.05.03.2001'),
                        TextInput::make('name')
                            ->label('Nama Desa / Kelurahan')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: Satreyan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
