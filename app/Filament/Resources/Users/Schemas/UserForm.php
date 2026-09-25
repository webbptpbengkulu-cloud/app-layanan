<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Pengguna')
                    ->description('Informasi login, nama lengkap, NIK, dan kontak')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Ahmad Mu\'amar Muzakki'),
                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('nama@blitarkab.go.id'),
                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText('Kosongkan jika tidak ingin mengubah kata sandi'),
                        TextInput::make('phone')
                            ->label('Nomor Telepon / WA')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('081234567890'),
                        TextInput::make('nik')
                            ->label('Nomor Induk Kependudukan (NIK)')
                            ->length(16)
                            ->placeholder('3505xxxxxxxxxxxx')
                            ->unique(ignoreRecord: true),
                        Toggle::make('is_active')
                            ->label('Akun Aktif')
                            ->default(true)
                            ->helperText('Akun non-aktif tidak dapat masuk ke sistem'),
                    ])
                    ->columns(2),

                Section::make('Penugasan & Peran Akses')
                    ->description('Tentukan hak akses role, unit kerja kedinasan, atau wilayah penugasan operator')
                    ->schema([
                        Select::make('roles')
                            ->label('Peran / Hak Akses (Role)')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Pilih satu atau lebih role sistem (administrator, petugas_dinsos, pejabat_penandatangan, pimpinan, operator_wilayah, masyarakat)')
                            ->columnSpanFull(),
                        Select::make('work_unit_id')
                            ->label('Unit Kerja / Bidang (Dinas Sosial)')
                            ->relationship('workUnit', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih jika pegawai internal Dinas'),
                        Grid::make(2)
                            ->schema([
                                Select::make('district_id')
                                    ->label('Wilayah Kecamatan')
                                    ->relationship('district', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->placeholder('Pilih jika operator kecamatan/desa'),
                                Select::make('village_id')
                                    ->label('Wilayah Desa / Kelurahan')
                                    ->relationship('village', 'name', modifyQueryUsing: function ($query, $get) {
                                        $districtId = $get('district_id');
                                        if ($districtId) {
                                            $query->where('district_id', $districtId);
                                        }
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih jika operator desa spesifik'),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }
}
