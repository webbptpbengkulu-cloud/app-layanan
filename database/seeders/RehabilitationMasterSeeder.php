<?php

namespace Database\Seeders;

use App\Models\ClientCategory;
use App\Models\ReferralInstitution;
use Illuminate\Database\Seeder;

class RehabilitationMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Lanjut Usia Terlantar (LUT)',
                'description' => 'Lansia berusia 60 tahun ke atas yang tidak memiliki keluarga atau terlantar tanpa dukungan nafkah dan perawatan.',
            ],
            [
                'name' => 'Penyandang Disabilitas Terlantar',
                'description' => 'Penyandang disabilitas fisik, sensorik, intelektual, maupun ganda yang membutuhkan pemenuhan hak dasar dan rehabilitasi.',
            ],
            [
                'name' => 'Orang Dengan Gangguan Jiwa (ODGJ) Terlantar',
                'description' => 'Individu dengan gangguan kejiwaan yang terlantar di ruang publik atau dipasung dan membutuhkan penanganan medis serta sosial.',
            ],
            [
                'name' => 'Anak Terlantar / Anak Berhadapan dengan Hukum (ABH)',
                'description' => 'Anak tanpa pengasuhan orang tua, korban penelantaran, atau anak yang berhadapan dengan hukum yang membutuhkan pendampingan peksos.',
            ],
            [
                'name' => 'Korban Tindak Kekerasan (KTK) & Perempuan Rentan',
                'description' => 'Korban kekerasan dalam rumah tangga (KDRT), tindak pidana perdagangan orang (TPPO), atau perempuan rentan sosial ekonomi.',
            ],
            [
                'name' => 'Gelandangan dan Pengemis (Gepeng)',
                'description' => 'Warga tanpa tempat tinggal tetap atau pengemis jalanan yang memerlukan penertiban humanis, pembinaan, dan resosialisasi.',
            ],
        ];

        foreach ($categories as $cat) {
            ClientCategory::updateOrCreate(
                ['name' => $cat['name']],
                $cat
            );
        }

        $institutions = [
            [
                'name' => 'UPT Pelayanan Sosial Tresna Werdha (PSTW) Blitar',
                'type' => 'panti',
                'address' => 'Jl. Merdeka No. 12, Kepanjenkidul, Kota Blitar',
                'contact' => '(0342) 801234',
                'is_active' => true,
            ],
            [
                'name' => 'RSUD Ngudi Waluyo Wlingi',
                'type' => 'RS',
                'address' => 'Jl. Dokter Soetomo No. 1, Wlingi, Kabupaten Blitar',
                'contact' => '(0342) 691006',
                'is_active' => true,
            ],
            [
                'name' => 'RSUD Srengat Kabupaten Blitar',
                'type' => 'RS',
                'address' => 'Jl. Raya Dandong No. 1, Srengat, Kabupaten Blitar',
                'contact' => '(0342) 551234',
                'is_active' => true,
            ],
            [
                'name' => 'RS Jiwa Menur Surabaya (Rujukan Jiwa Provinsi Jawa Timur)',
                'type' => 'RS',
                'address' => 'Jl. Menur No. 120, Surabaya',
                'contact' => '(031) 5021635',
                'is_active' => true,
            ],
            [
                'name' => 'UPT Balai Rehabilitasi Sosial Bina Daksa Bangil',
                'type' => 'balai',
                'address' => 'Jl. Raya Bangil - Pandaan, Pasuruan',
                'contact' => '(0343) 741289',
                'is_active' => true,
            ],
            [
                'name' => 'LKS Lembaga Kesejahteraan Sosial Kasih Ibu Kanigoro',
                'type' => 'LKS',
                'address' => 'Jl. Satreyan Indah No. 45, Kanigoro, Blitar',
                'contact' => '081233445566',
                'is_active' => true,
            ],
            [
                'name' => 'Panti Asuhan dan Asrama Anak Al-Hidayah Garum',
                'type' => 'panti',
                'address' => 'Jl. Raya Garum No. 88, Garum, Kabupaten Blitar',
                'contact' => '(0342) 561789',
                'is_active' => true,
            ],
        ];

        foreach ($institutions as $inst) {
            ReferralInstitution::updateOrCreate(
                ['name' => $inst['name']],
                $inst
            );
        }
    }
}
