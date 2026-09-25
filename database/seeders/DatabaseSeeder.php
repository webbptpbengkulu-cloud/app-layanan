<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Data Master & Organisasi
            WorkUnitSeeder::class,
            DistrictVillageSeeder::class,
            UserSeeder::class,

            // 2. Data Master Layanan & Kategori
            ServiceTypeSeeder::class,
            DtsenPurposeSeeder::class,
            ComplaintCategorySeeder::class,
            RehabilitationMasterSeeder::class,

            // 3. Portal Informasi Publik
            InformationPortalSeeder::class,

            // 4. Data Transaksional & Alur Kerja
            ServiceRequestSeeder::class,
            ComplaintSeeder::class,
            RehabilitationCaseSeeder::class,

            // 5. Penomoran & Audit Log
            NumberSequenceSeeder::class,
            ActivityLogSeeder::class,
        ]);
    }
}
