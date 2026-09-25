<?php

namespace App\Filament\Resources\Complaints\Schemas;

use App\Enums\ComplaintStatus;
use App\Models\District;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ComplaintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Laporan Pengaduan')
                    ->description('Rincian isi keluhan atau masalah sosial masyarakat')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('complaint_number')
                                    ->label('Nomor Pengaduan')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Dibuat otomatis oleh sistem'),
                                Select::make('complaint_category_id')
                                    ->label('Kategori Pengaduan')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Textarea::make('description')
                            ->label('Uraian Pengaduan / Keluhan Masalah')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull()
                            ->placeholder('Jelaskan secara rinci kronologi kejadian, subjek yang diadukan, dan situasi terkini di lapangan.'),
                    ]),

                Section::make('Identitas Pelapor & Lokasi Kejadian')
                    ->description('Data kontak pelapor dan titik lokasi kejadian di wilayah Kabupaten Blitar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('reporter_name')
                                    ->label('Nama Lengkap Pelapor')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('reporter_phone')
                                    ->label('Nomor WhatsApp / HP Pelapor')
                                    ->tel()
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('district_id')
                                    ->label('Kecamatan Kejadian')
                                    ->options(District::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->dehydrated(false),
                                Select::make('village_id')
                                    ->label('Desa / Kelurahan Kejadian')
                                    ->relationship('village', 'name', modifyQueryUsing: function ($query, $get) {
                                        $districtId = $get('district_id');
                                        if ($districtId) {
                                            $query->where('district_id', $districtId);
                                        }
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        Textarea::make('location_detail')
                            ->label('Detail Alamat Kejadian / Patokan Lokasi')
                            ->rows(2)
                            ->columnSpanFull()
                            ->placeholder('RT/RW, Dusun, dekat fasilitas umum tertentu...'),
                    ]),

                Section::make('Status Penanganan & Verifikasi')
                    ->description('Penetapan status alur kerja, petugas penangan, dan catatan hasil tindak lanjut')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('status')
                                    ->label('Status Pengaduan')
                                    ->options(collect(ComplaintStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                    ->default(ComplaintStatus::Received)
                                    ->required(),
                                Select::make('officer_id')
                                    ->label('Petugas Penangan')
                                    ->relationship('officer', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('duplicate_of_id')
                                    ->label('Laporan Induk (Jika Duplikat)')
                                    ->relationship('duplicateOf', 'complaint_number')
                                    ->searchable()
                                    ->placeholder('Pilih jika laporan ini duplikat'),
                            ]),

                        Textarea::make('verification_result')
                            ->label('Hasil Verifikasi Lapangan / Telaah Dokumen')
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('action_taken')
                            ->label('Tindakan / Solusi Yang Telah Diberikan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Catat intervensi yang telah dilakukan oleh petugas Dinsos.'),

                        DateTimePicker::make('resolved_at')
                            ->label('Waktu Selesai Ditangani'),
                    ]),
            ]);
    }
}
