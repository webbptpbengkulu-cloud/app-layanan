<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Enums\PbiReason;
use App\Enums\ServiceRequestStatus;
use App\Models\District;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pemohon & Pengajuan')
                    ->description('Data pokok pemohon dan identitas dokumen kependudukan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('request_number')
                                    ->label('Nomor Tiket Permohonan')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Dibuat otomatis oleh sistem'),
                                Select::make('service_type_id')
                                    ->label('Jenis Layanan')
                                    ->relationship('serviceType', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                Toggle::make('is_priority')
                                    ->label('Pengajuan Prioritas (Darurat)')
                                    ->helperText('Tandai untuk penanganan darurat')
                                    ->default(false),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('applicant_name')
                                    ->label('Nama Lengkap Pemohon')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Sesuai KTP'),
                                TextInput::make('applicant_nik')
                                    ->label('NIK Pemohon')
                                    ->required()
                                    ->length(16)
                                    ->placeholder('3505xxxxxxxxxxxx'),
                                TextInput::make('family_card_number')
                                    ->label('Nomor Kartu Keluarga (KK)')
                                    ->length(16)
                                    ->placeholder('3505xxxxxxxxxxxx'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Nomor WhatsApp / HP')
                                    ->tel()
                                    ->required()
                                    ->placeholder('08xxxxxxxxxx'),
                                Select::make('district_id')
                                    ->label('Kecamatan')
                                    ->options(District::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->dehydrated(false),
                                Select::make('village_id')
                                    ->label('Desa / Kelurahan')
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

                        Textarea::make('address')
                            ->label('Alamat Domisili Lengkap')
                            ->rows(2)
                            ->columnSpanFull()
                            ->placeholder('RT/RW, Dusun, Desa'),
                    ]),

                Section::make('Data Spesifik Surat Keterangan DTKS / DTSEN')
                    ->description('Rincian subjek yang diterangkan dan tujuan SK DTSEN')
                    ->relationship('dtsenCertificate')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('dtsen_purpose_id')
                                    ->label('Tujuan Penggunaan SK DTSEN')
                                    ->relationship('dtsenPurpose', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Select::make('relationship_to_applicant')
                                    ->label('Hubungan dengan Pemohon')
                                    ->options([
                                        'Diri Sendiri' => 'Diri Sendiri',
                                        'Anak Kandung' => 'Anak Kandung',
                                        'Suami / Istri' => 'Suami / Istri',
                                        'Orang Tua' => 'Orang Tua',
                                        'Lainnya' => 'Lainnya',
                                    ])
                                    ->default('Diri Sendiri')
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('subject_name')
                                    ->label('Nama Orang Yang Diterangkan')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('subject_nik')
                                    ->label('NIK Orang Yang Diterangkan')
                                    ->required()
                                    ->length(16),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_registered')
                                    ->label('Tercatat di DTKS (SIKS-NG)')
                                    ->default(true),
                                TextInput::make('decile')
                                    ->label('Peringkat Desil')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10)
                                    ->default(1),
                                DatePicker::make('valid_until')
                                    ->label('Masa Berlaku Hingga')
                                    ->default(now()->addDays(30)),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('certificate_number')
                                    ->label('Nomor SK Terbit')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Diterbitkan otomatis saat TTD Kadis'),
                                TextInput::make('verification_code')
                                    ->label('Kode Verifikasi Keaslian Dokumen')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Generated with QR Code'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Data Spesifik Reaktivasi PBI-JK (BPJS PBI)')
                    ->description('Rincian kepesertaan jaminan kesehatan dan kondisi medis darurat')
                    ->relationship('pbiReactivation')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('participant_name')
                                    ->label('Nama Peserta BPJS')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('participant_nik')
                                    ->label('NIK Peserta')
                                    ->required()
                                    ->length(16),
                                TextInput::make('bpjs_card_number')
                                    ->label('Nomor Kartu KIS / BPJS')
                                    ->required()
                                    ->maxLength(30),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Select::make('reason')
                                    ->label('Alasan Permohonan Reaktivasi')
                                    ->options(collect(PbiReason::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                    ->required(),
                                TextInput::make('health_facility_name')
                                    ->label('Nama Fasilitas Kesehatan Perujuk')
                                    ->placeholder('Contoh: RSUD Ngudi Waluyo Wlingi'),
                                TextInput::make('health_letter_number')
                                    ->label('Nomor Surat Keterangan Rawat/Sakit'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('decile')
                                    ->label('Peringkat Desil DTKS')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10),
                                DatePicker::make('deactivated_date')
                                    ->label('Tanggal Non-Aktif BPJS'),
                            ]),

                        Textarea::make('eligibility_notes')
                            ->label('Catatan Kelayakan Reaktivasi')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Status & Penanganan Petugas')
                    ->description('Pengaturan status alur kerja, penugasan petugas, dan catatan disposisi')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('status')
                                    ->label('Status Alur Kerja')
                                    ->options(collect(ServiceRequestStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                    ->required(),
                                Select::make('officer_id')
                                    ->label('Petugas Penangan')
                                    ->relationship('officer', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('work_unit_id')
                                    ->label('Unit Kerja Bertanggung Jawab')
                                    ->relationship('workUnit', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Textarea::make('officer_notes')
                            ->label('Catatan Petugas / Hasil Pemeriksaan')
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan / Catatan Perbaikan')
                            ->rows(2)
                            ->columnSpanFull()
                            ->visible(fn ($get) => in_array($get('status'), ['rejected', 'ministry_rejected', 'revision_requested'])),
                    ]),
            ]);
    }
}
