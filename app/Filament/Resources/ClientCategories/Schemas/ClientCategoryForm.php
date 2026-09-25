<?php

namespace App\Filament\Resources\ClientCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kategori Pemerlu Pelayanan Kesejahteraan Sosial (PPKS)')
                    ->description('Kelompok sasaran rehabilitasi sosial (lansia terlantar, anak berhadapan hukum, ODGJ, disabilitas, dll.)')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori Klien')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Lansia Terlantar, Penyandang Disabilitas'),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                        Textarea::make('description')
                            ->label('Deskripsi & Kriteria')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
