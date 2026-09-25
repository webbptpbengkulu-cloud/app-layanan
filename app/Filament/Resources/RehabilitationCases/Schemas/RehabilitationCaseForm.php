<?php

namespace App\Filament\Resources\RehabilitationCases\Schemas;

use App\Enums\RehabilitationCaseStatus;
use App\Enums\RehabilitationHandlingType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RehabilitationCaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kasus Rehabilitasi Sosial')
                    ->description('Data nomor kasus, klien yang ditangani, dan asal sumber laporan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('case_number')
                                    ->label('Nomor Kasus')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Dibuat otomatis oleh sistem'),
                                Select::make('client_id')
                                    ->label('Klien PPKS')
                                    ->relationship('client', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('officer_id')
                                    ->label('Pekerja Sosial / Petugas Penangan')
                                    ->relationship('officer', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Select::make('handling_type')
                                    ->label('Rencana Penanganan')
                                    ->options(collect(RehabilitationHandlingType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                    ->default(RehabilitationHandlingType::Direct),
                                Select::make('status')
                                    ->label('Status Penanganan Kasus')
                                    ->options(collect(RehabilitationCaseStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                    ->default(RehabilitationCaseStatus::Received)
                                    ->required(),
                                DateTimePicker::make('received_at')
                                    ->label('Waktu Penerimaan Kasus')
                                    ->default(now()),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('service_request_id')
                                    ->label('Terkait Pengajuan Layanan (Jika Ada)')
                                    ->relationship('serviceRequest', 'request_number')
                                    ->searchable(),
                                Select::make('complaint_id')
                                    ->label('Terkait Pengaduan Masyarakat (Jika Ada)')
                                    ->relationship('complaint', 'complaint_number')
                                    ->searchable(),
                            ]),

                        Textarea::make('handling_result')
                            ->label('Hasil Akhir Penanganan & Rekomendasi Kasus')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Wajib diisi saat kasus dinyatakan selesai / ditutup.'),

                        DateTimePicker::make('closed_at')
                            ->label('Waktu Kasus Ditutup / Selesai'),
                    ]),
            ]);
    }
}
