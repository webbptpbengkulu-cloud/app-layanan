<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Enums\Gender;
use App\Models\District;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Klien / Penerima Pelayanan (PPKS)')
                    ->description('Data pokok Pemerlu Pelayanan Kesejahteraan Sosial')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap Klien')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('client_category_id')
                                    ->label('Kategori PPKS')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        Gender::Male->value => 'Laki-laki',
                                        Gender::Female->value => 'Perempuan',
                                    ])
                                    ->required(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('nik')
                                    ->label('NIK')
                                    ->length(16)
                                    ->placeholder('3505xxxxxxxxxxxx'),
                                DatePicker::make('birth_date')
                                    ->label('Tanggal Lahir'),
                                TextInput::make('phone')
                                    ->label('Nomor Telepon / Kontak Keluarga')
                                    ->tel(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('district_id')
                                    ->label('Kecamatan')
                                    ->options(District::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->dehydrated(false),
                                Select::make('village_id')
                                    ->label('Desa / Kelurahan Domisili')
                                    ->relationship('village', 'name', modifyQueryUsing: function ($query, $get) {
                                        $districtId = $get('district_id');
                                        if ($districtId) {
                                            $query->where('district_id', $districtId);
                                        }
                                    })
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Textarea::make('address')
                            ->label('Alamat Domisili / Tempat Ditemukan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
