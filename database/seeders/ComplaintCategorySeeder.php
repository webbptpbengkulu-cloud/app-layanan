<?php

namespace Database\Seeders;

use App\Models\ComplaintCategory;
use Illuminate\Database\Seeder;

class ComplaintCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Pemerlu Pelayanan Kesejahteraan Sosial (PPKS) Terlantar',
                'description' => 'Laporan temuan warga terlantar, lansia sebatang kara, tuna wisma/gepeng yang membutuhkan penanganan darurat atau penampungan sementara.',
                'is_active' => true,
            ],
            [
                'name' => 'Ketidaktepatan Sasaran Bantuan Sosial (PKH, BPNT, BLT)',
                'description' => 'Laporan adanya penerima bantuan sosial yang dinilai mampu atau warga sangat miskin yang belum tersentuh bantuan sosial.',
                'is_active' => true,
            ],
            [
                'name' => 'Gangguan Ketertiban Sosial & ODGJ Terlantar',
                'description' => 'Laporan penanganan orang dengan gangguan jiwa (ODGJ) terlantar atau yang membutuhkan evakuasi ke fasilitas kesehatan mental.',
                'is_active' => true,
            ],
            [
                'name' => 'Permasalahan Kepesertaan BPJS KIS / PBI-JK',
                'description' => 'Pengaduan terkait kendala penonaktifan kartu KIS PBI saat menjalani rawat inap atau rujukan darurat.',
                'is_active' => true,
            ],
            [
                'name' => 'Lansia dan Penyandang Disabilitas Terlantar Butuh Evakuasi',
                'description' => 'Laporan kondisi darurat lansia bedridden atau penyandang disabilitas berat tanpa pendamping keluarga.',
                'is_active' => true,
            ],
            [
                'name' => 'Dugaan Pungutan Liar atau Hambatan Pelayanan Sosial',
                'description' => 'Laporan penyalahgunaan wewenang, pemotongan dana bansos, atau hambatan dalam pengurusan layanan sosial.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            ComplaintCategory::updateOrCreate(
                ['name' => $cat['name']],
                $cat
            );
        }
    }
}
