<?php

namespace App\Filament\Resources\DownloadableForms\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DownloadableFormForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Berkas Formulir Unduhan')
                    ->description('Format blangko atau formulir permohonan yang dapat diunduh masyarakat')
                    ->schema([
                        Select::make('information_page_id')
                            ->label('Halaman Informasi Induk')
                            ->relationship('informationPage', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama Formulir')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Formulir Permohonan SK DTSEN'),
                        FileUpload::make('file_path')
                            ->label('File Blangko / Formulir')
                            ->directory('downloadable-forms')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->maxSize(10240)
                            ->required()
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('version')
                                    ->label('Versi Formulir')
                                    ->default('1.0')
                                    ->placeholder('Contoh: 1.0, 2026.1'),
                                Toggle::make('is_current')
                                    ->label('Versi Aktif (Berlaku)')
                                    ->default(true)
                                    ->helperText('Hanya versi aktif yang akan ditampilkan di portal unduhan masyarakat'),
                            ]),
                    ]),
            ]);
    }
}
