<?php

namespace Database\Seeders;

use App\Models\DtsenPurpose;
use Illuminate\Database\Seeder;

class DtsenPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purposes = [
            [
                'code' => 'spmb',
                'name' => 'Pendaftaran SPMB Jalur Afirmasi',
                'max_decile' => 5,
                'validity_days' => 30,
                'is_active' => true,
            ],
            [
                'code' => 'pip',
                'name' => 'Pengusulan / Pencairan Program Indonesia Pintar (PIP)',
                'max_decile' => 4,
                'validity_days' => 60,
                'is_active' => true,
            ],
            [
                'code' => 'kip_kuliah',
                'name' => 'Pendaftaran KIP Kuliah',
                'max_decile' => 4,
                'validity_days' => 90,
                'is_active' => true,
            ],
            [
                'code' => 'bansos',
                'name' => 'Pengusulan Bantuan Sosial Daerah',
                'max_decile' => 3,
                'validity_days' => 60,
                'is_active' => true,
            ],
            [
                'code' => 'kesehatan',
                'name' => 'Pelayanan Kesehatan Afirmasi / Jamkesda',
                'max_decile' => 4,
                'validity_days' => 30,
                'is_active' => true,
            ],
            [
                'code' => 'lainnya',
                'name' => 'Administrasi dan Keperluan Sosial Lainnya',
                'max_decile' => 5,
                'validity_days' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($purposes as $purpose) {
            DtsenPurpose::updateOrCreate(
                ['code' => $purpose['code']],
                $purpose
            );
        }
    }
}
