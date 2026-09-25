<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pertanyaan & Jawaban Populer')
                    ->description('Tanya jawab umum untuk mengedukasi dan memandu masyarakat di portal')
                    ->schema([
                        Select::make('information_page_id')
                            ->label('Terkait Halaman Layanan')
                            ->relationship('informationPage', 'title')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih jika pertanyaan spesifik layanan tertentu'),

                        TextInput::make('question')
                            ->label('Pertanyaan (Question)')
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->placeholder('Contoh: Apakah mengurus SK DTSEN dipungut biaya?'),

                        RichEditor::make('answer')
                            ->label('Jawaban Lengkap (Answer)')
                            ->required()
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('sort_order')
                                    ->label('Urutan Tampil')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Angka lebih kecil tampil lebih dulu'),
                                Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}
