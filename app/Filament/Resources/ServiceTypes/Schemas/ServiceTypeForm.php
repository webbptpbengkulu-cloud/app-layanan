<?php

namespace App\Filament\Resources\ServiceTypes\Schemas;

use App\Enums\ServiceHandler;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Jenis Layanan')
                    ->description('Konfigurasi tipe layanan, alur penangan (handler), dan SLA')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Layanan')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Contoh: SK-DTSEN, PBI-JK'),
                        TextInput::make('name')
                            ->label('Nama Layanan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Surat Keterangan Terdaftar DTKS/DTSEN'),
                        TextInput::make('category')
                            ->label('Kategori')
                            ->maxLength(100)
                            ->placeholder('Contoh: Perlindungan Sosial'),
                        Select::make('handler')
                            ->label('Penangan Alur Kerja (Handler)')
                            ->options(collect(ServiceHandler::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->required(),
                        TextInput::make('sla_days')
                            ->label('SLA Penyelesaian (Hari Kerja)')
                            ->numeric()
                            ->minValue(1)
                            ->default(3),
                        Toggle::make('needs_assessment')
                            ->label('Wajib Assessment Peksos')
                            ->default(false),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                        Textarea::make('description')
                            ->label('Deskripsi Layanan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Daftar Persyaratan Berkas')
                    ->description('Dokumen pendukung yang wajib diunggah pemohon saat mengajukan permohonan')
                    ->schema([
                        Repeater::make('requirements')
                            ->relationship('requirements')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Berkas')
                                    ->required()
                                    ->placeholder('Contoh: KTP Pemohon, Kartu Keluarga'),
                                Toggle::make('is_mandatory')
                                    ->label('Wajib Diunggah')
                                    ->default(true),
                                TextInput::make('allowed_mimes')
                                    ->label('Format File Diizinkan')
                                    ->default('pdf,jpg,png'),
                                TextInput::make('sort_order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->orderColumn('sort_order')
                            ->columns(4)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Persyaratan Berkas')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
