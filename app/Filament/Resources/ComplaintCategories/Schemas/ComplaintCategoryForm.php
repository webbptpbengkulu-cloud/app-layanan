<?php

namespace App\Filament\Resources\ComplaintCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ComplaintCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kategori Pengaduan Masyarakat')
                    ->description('Klasifikasi jenis masalah atau keluhan sosial (bansos tidak tepat sasaran, pemerlu bantuan darurat, dll.)')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori Pengaduan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Bansos Tidak Tepat Sasaran, Dugaan Pungli'),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                        Textarea::make('description')
                            ->label('Deskripsi Penjelasan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
