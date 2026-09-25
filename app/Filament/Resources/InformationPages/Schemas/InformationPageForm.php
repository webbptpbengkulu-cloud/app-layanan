<?php

namespace App\Filament\Resources\InformationPages\Schemas;

use App\Enums\InformationCategory;
use App\Enums\PublishStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class InformationPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Judul & Kategori Publikasi')
                    ->description('Informasi judul halaman layanan dan status publikasi di portal web')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Informasi Layanan')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Select::make('category')
                                    ->label('Kategori Konten')
                                    ->options(collect(InformationCategory::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                    ->required(),
                                Select::make('service_type_id')
                                    ->label('Terkait Jenis Layanan')
                                    ->relationship('serviceType', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('publish_status')
                                    ->label('Status Publikasi')
                                    ->options(collect(PublishStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                    ->default(PublishStatus::Draft)
                                    ->required(),
                            ]),

                        DateTimePicker::make('published_at')
                            ->label('Waktu Diterbitkan')
                            ->default(now()),
                    ]),

                Section::make('Konten & Uraian Layanan')
                    ->description('Penjelasan rinci mengenai deskripsi, persyaratan berkas, dan alur permohonan')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Deskripsi & Penjelasan Umum')
                            ->columnSpanFull(),
                        RichEditor::make('requirements')
                            ->label('Persyaratan Dokumen & Kriteria Penerima')
                            ->columnSpanFull(),
                        RichEditor::make('procedure')
                            ->label('Prosedur & Alur Pengajuan')
                            ->columnSpanFull(),
                    ]),

                Section::make('Informasi Operasional & Kontak')
                    ->description('Waktu pelayanan, lokasi loket, dan narahubung resmi Dinas Sosial')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('service_hours')
                                    ->label('Jam Pelayanan')
                                    ->placeholder('Senin - Jumat: 08.00 - 15.00 WIB'),
                                TextInput::make('location')
                                    ->label('Lokasi Pelayanan / Ruang Loket')
                                    ->placeholder('Loket SAPA SOSIAL, Kantor Dinsos Kanigoro'),
                                TextInput::make('contact')
                                    ->label('Kontak / Hotline WhatsApp')
                                    ->placeholder('081234567890 (Layanan Linjamsos)'),
                            ]),
                    ]),
            ]);
    }
}
