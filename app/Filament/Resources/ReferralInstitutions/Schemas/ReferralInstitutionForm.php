<?php

namespace App\Filament\Resources\ReferralInstitutions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReferralInstitutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Lembaga Rujukan')
                    ->description('Lembaga mitra rehabilitasi sosial (Panti Asuhan, Balai Rehabilitasi, RSJ, LKSA, dll.)')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lembaga')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Balai Besar Rehabilitasi Sosial Prof. Dr. Soeharso'),
                        Select::make('type')
                            ->label('Tipe / Jenis Lembaga')
                            ->options([
                                'Panti / Balai Sosial' => 'Panti / Balai Sosial',
                                'Rumah Sakit / RSJ' => 'Rumah Sakit / RSJ',
                                'LKS / LKSA' => 'Lembaga Kesejahteraan Sosial (LKS)',
                                'Sentra Kemensos' => 'Sentra Kemensos RI',
                                'Lembaga Pemasyarakatan' => 'Bapas / Lapas Anak',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->required(),
                        TextInput::make('contact')
                            ->label('Nomor Kontak / WhatsApp / Penanggung Jawab')
                            ->maxLength(100)
                            ->placeholder('Contoh: 081234567890 (Humas)'),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                        Textarea::make('address')
                            ->label('Alamat Lembaga')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
