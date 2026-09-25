<?php

namespace App\Filament\Resources\Districts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DistrictForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Wilayah Kecamatan')
                    ->description('Kode Kemendagri / BPS dan Nama Kecamatan di Kabupaten Blitar')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Kecamatan')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->placeholder('Contoh: 35.05.03'),
                        TextInput::make('name')
                            ->label('Nama Kecamatan')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: Kanigoro'),
                    ])
                    ->columns(2),
            ]);
    }
}
