<?php

namespace Database\Seeders;

use App\Models\ServiceRequirement;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'code' => 'DTSEN',
                'name' => 'Surat Keterangan DTSEN',
                'category' => 'Perlindungan dan Jaminan Sosial',
                'description' => 'Penerbitan surat keterangan status seseorang/keluarga dalam Data Tunggal Sosial Ekonomi Nasional (DTSEN) beserta peringkat desil untuk syarat SPMB Jalur Afirmasi, PIP, KIP Kuliah, bantuan sosial, dan pelayanan kesehatan.',
                'handler' => 'dtsen',
                'needs_assessment' => false,
                'sla_days' => 2,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'Kartu Tanda Penduduk (KTP) Pemohon',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK) Pemohon',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'KTP / Akta Kelahiran Orang yang Diterangkan',
                        'is_mandatory' => false,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'code' => 'PBI',
                'name' => 'Reaktivasi KIS / PBI-JK',
                'category' => 'Jaminan Kesehatan Sosial',
                'description' => 'Fasilitasi pengaktifan kembali kepesertaan JKN-KIS Penerima Bantuan Iuran Jaminan Kesehatan (PBI-JK) yang dinonaktifkan untuk masyarakat miskin dan rentan miskin.',
                'handler' => 'pbi',
                'needs_assessment' => false,
                'sla_days' => 7,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'Kartu Tanda Penduduk (KTP) Peserta / Pemohon',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK) Peserta',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Kartu BPJS Kesehatan / KIS Nonaktif',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Surat Keterangan / Rekam Medis dari Faskes / RS',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'code' => 'REHSOS_REQ',
                'name' => 'Permohonan Pelayanan Rehabilitasi Sosial',
                'category' => 'Rehabilitasi Sosial',
                'description' => 'Pengajuan permohonan penanganan dan pelayanan rehabilitasi sosial bagi Pemerlu Pelayanan Kesejahteraan Sosial (PPKS) seperti lansia terlantar, disabilitas, anak terlantar, dan ODGJ.',
                'handler' => 'generic',
                'needs_assessment' => true,
                'sla_days' => 5,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'KTP Pemohon / Pelapor',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK) Klien',
                        'is_mandatory' => false,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Foto Kondisi Klien & Lingkungan Tempat Tinggal',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'jpg,jpeg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Surat Pengantar / Keterangan Desa / Kelurahan',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'code' => 'REK_BANSOS',
                'name' => 'Rekomendasi Bantuan Sosial Terpadu',
                'category' => 'Pemberdayaan Sosial',
                'description' => 'Penerbitan surat rekomendasi untuk usulan bantuan sosial terpadu daerah, bantuan tidak terencana, atau bantuan pemakaman.',
                'handler' => 'generic',
                'needs_assessment' => false,
                'sla_days' => 3,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'KTP Pemohon',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK)',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Surat Keterangan Tidak Mampu (SKTM) dari Desa / Kelurahan',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Surat Permohonan Bantuan',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
        ];

        foreach ($services as $sData) {
            $requirements = $sData['requirements'];
            unset($sData['requirements']);

            $serviceType = ServiceType::updateOrCreate(
                ['code' => $sData['code']],
                $sData
            );

            foreach ($requirements as $reqData) {
                ServiceRequirement::updateOrCreate(
                    [
                        'service_type_id' => $serviceType->id,
                        'name' => $reqData['name'],
                    ],
                    $reqData
                );
            }
        }
    }
}
