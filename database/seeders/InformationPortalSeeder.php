<?php

namespace Database\Seeders;

use App\Enums\InformationCategory;
use App\Enums\PublishStatus;
use App\Models\DownloadableForm;
use App\Models\Faq;
use App\Models\InformationPage;
use App\Models\PageVisit;
use App\Models\SearchLog;
use App\Models\ServiceType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InformationPortalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::where('email', 'petugas.pengaduan@blitarkab.go.id')->first()
            ?? User::first();

        $dtsenService = ServiceType::where('code', 'DTSEN')->first();
        $pbiService = ServiceType::where('code', 'PBI')->first();
        $rehsosService = ServiceType::where('code', 'REHSOS_REQ')->first();

        $pages = [
            [
                'title' => 'Surat Keterangan DTSEN (Data Tunggal Sosial Ekonomi Nasional)',
                'slug' => 'surat-keterangan-dtsen',
                'category' => InformationCategory::Program->value,
                'service_type_id' => $dtsenService?->id,
                'description' => 'Layanan resmi penerbitan Surat Keterangan Data Tunggal Sosial Ekonomi Nasional (DTSEN) yang memuat status keterdaftaran dan tingkat desil kesejahteraan keluarga pemohon di Kabupaten Blitar.',
                'requirements' => "1. Scan / Foto KTP asli pemohon yang masih berlaku\n2. Scan / Foto Kartu Keluarga (KK) asli pemohon\n3. Scan KTP / Akta Kelahiran anggota keluarga yang diterangkan (misal anak calon siswa / mahasiswa)\n4. Mengisi formulir tujuan permohonan (SPMB Afirmasi, KIP Kuliah, PIP, dll.)",
                'procedure' => "1. Akses menu Pengajuan Layanan di SAPA SOSIAL\n2. Pilih jenis layanan 'Surat Keterangan DTSEN' dan tentukan tujuan penggunaan\n3. Unggah berkas persyaratan dan simpan nomor tiket pengajuan\n4. Petugas memeriksa berkas dan memverifikasi data di SIKS-NG\n5. Draf surat diparaf oleh Kabid Linjamsos dan disetujui Kepala Dinas Sosial\n6. Pemohon mengunduh surat ber-QR code verifikasi secara mandiri melalui website",
                'service_hours' => 'Senin - Kamis: 08.00 - 15.00 WIB, Jumat: 08.00 - 14.30 WIB',
                'location' => 'Front Office Pelayanan Terpadu Dinas Sosial Kabupaten Blitar, Jl. Raya Kanigoro',
                'contact' => 'WhatsApp Layanan: 0812-3456-7890 | Email: dinsos@blitarkab.go.id',
                'publish_status' => PublishStatus::Published->value,
                'published_at' => Carbon::now()->subMonths(2),
                'manager_id' => $manager?->id,
                'forms' => [
                    [
                        'name' => 'Formulir Permohonan Surat Keterangan DTSEN',
                        'file_path' => 'forms/formulir_permohonan_dtsen.pdf',
                        'version' => '1.0',
                        'is_current' => true,
                    ],
                    [
                        'name' => 'Surat Pernyataan Tanggung Jawab Mutlak (SPTJM) Keabsahan Data',
                        'file_path' => 'forms/sptjm_keabsahan_data_dtsen.pdf',
                        'version' => '1.1',
                        'is_current' => true,
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'Berapa lama proses penerbitan Surat Keterangan DTSEN?',
                        'answer' => 'Proses verifikasi dan penerbitan SK DTSEN membutuhkan waktu maksimal 2 (dua) hari kerja sejak berkas dinyatakan lengkap dan valid.',
                        'sort_order' => 1,
                    ],
                    [
                        'question' => 'Bagaimana jika saya tidak terdaftar di DTSEN/SIKS-NG atau desil saya di atas batas?',
                        'answer' => 'Jika data belum terdaftar atau desil melebihi ketentuan syarat tujuan (misal SPMB mensyaratkan desil 1-5), pengajuan ditolak secara sistem dengan penjelasan resmi. Pemohon disarankan berkoordinasi dengan operator desa/Puskesos untuk mekanisme usulan pemutakhiran data.',
                        'sort_order' => 2,
                    ],
                    [
                        'question' => 'Bagaimana cara mengecek keaslian Surat Keterangan DTSEN yang telah terbit?',
                        'answer' => 'Setiap surat dilengkapi QR Code yang mengarah ke portal publik SAPA SOSIAL pada menu Cek Keaslian Surat, atau dengan memasukkan Kode Verifikasi unik surat.',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'title' => 'Fasilitasi Reaktivasi JKN-KIS / PBI-JK Dinonaktifkan',
                'slug' => 'reaktivasi-kis-pbi-jk',
                'category' => InformationCategory::Program->value,
                'service_type_id' => $pbiService?->id,
                'description' => 'Bantuan pengaktifan kembali kartu BPJS Kesehatan Penerima Bantuan Iuran Jaminan Kesehatan (PBI-JK) APBN/APBD yang dinonaktifkan untuk warga tidak mampu yang membutuhkan penanganan medis segera.',
                'requirements' => "1. KTP dan Kartu Keluarga (KK) Kabupaten Blitar\n2. Kartu KIS / BPJS Kesehatan yang dinonaktifkan\n3. Surat Keterangan Rawat Inap / Rawat Jalan / Rekam Medis dari RS atau Fasilitas Kesehatan\n4. Surat Keterangan Tidak Mampu dari Desa / Kelurahan setempat",
                'procedure' => "1. Daftarkan tiket reaktivasi melalui portal SAPA SOSIAL atau melalui petugas Puskesos di desa/kecamatan\n2. Unggah berkas medis dan identitas lengkap (tandai Prioritas jika darurat medis)\n3. Petugas Dinsos memverifikasi desil dan kelayakan di SIKS-NG\n4. Dinsos menerbitkan surat rekomendasi reaktivasi yang ditandatangani Kepala Dinas\n5. Petugas mengusulkan ke Kementerian Sosial melalui aplikasi SIKS-NG pusat\n6. Petugas memantau status hingga kepesertaan terkonfirmasi aktif di BPJS Kesehatan",
                'service_hours' => 'Senin - Kamis: 08.00 - 15.00 WIB, Jumat: 08.00 - 14.30 WIB (Layanan Darurat Medis 24 Jam via Puskesos)',
                'location' => 'Bidang Perlindungan dan Jaminan Sosial Dinas Sosial Kabupaten Blitar',
                'contact' => 'Hotline PBI Dinsos: 0812-3456-7891',
                'publish_status' => PublishStatus::Published->value,
                'published_at' => Carbon::now()->subMonths(2),
                'manager_id' => $manager?->id,
                'forms' => [
                    [
                        'name' => 'Formulir Pengusulan Reaktivasi KIS PBI-JK',
                        'file_path' => 'forms/formulir_usulan_reaktivasi_pbi.pdf',
                        'version' => '2.0',
                        'is_current' => true,
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'Siapa saja yang berhak mengajukan reaktivasi PBI-JK?',
                        'answer' => 'Warga miskin/rentan miskin terdaftar di DTSEN dengan kondisi sakit kronis, katastropik, gawat darurat medis, atau bayi baru lahir dari ibu peserta PBI aktif.',
                        'sort_order' => 1,
                    ],
                    [
                        'question' => 'Mengapa kartu KIS PBI saya dinonaktifkan tiba-tiba?',
                        'answer' => 'Penonaktifan dapat disebabkan oleh pemutakhiran data Kementerian Sosial pusat (SK Kemensos bulanan) akibat perubahan desil, NIK tidak padan Dukcapil, atau kuota alokasi.',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'title' => 'Pelayanan dan Pendampingan Rehabilitasi Sosial PPKS',
                'slug' => 'pelayanan-rehabilitasi-sosial',
                'category' => InformationCategory::Rehabilitation->value,
                'service_type_id' => $rehsosService?->id,
                'description' => 'Layanan perlindungan, pemulihan, dan rujukan bagi Pemerlu Pelayanan Kesejahteraan Sosial (PPKS) meliputi lansia terlantar, penyandang disabilitas berat, anak terlantar, korban kekerasan, dan ODGJ terlantar di wilayah Kabupaten Blitar.',
                'requirements' => "1. Laporan warga / pengantar desa / rekomendasi aparat keamanan\n2. Dokumen identitas PPKS (KTP/KK jika ada; jika tidak ada, petugas mendampingi pencatatan)\n3. Dokumentasi visual kondisi PPKS",
                'procedure' => "1. Laporan diterima tim piket penanganan darurat sosial Dinsos\n2. Pekerja Sosial (Peksos) melakukan assessment kebutuhan dan situasi lapangan\n3. Penetapan rencana intervensi: bantuan langsung dan/atau rujukan ke lembaga rujukan mitra\n4. Monitoring perkembangan pemulihan hingga kasus dinyatakan selesai/ditutup",
                'service_hours' => 'Pelayanan Kantor: Hari Kerja. Unit Respon Cepat Rehsos: Siaga 24 Jam',
                'location' => 'Kantor Dinas Sosial Kabupaten Blitar - Ruang Assessment Rehabilitasi Sosial',
                'contact' => 'Call Center Rehsos Blitar: (0342) 808999 / WA Siaga: 0811-3322-110',
                'publish_status' => PublishStatus::Published->value,
                'published_at' => Carbon::now()->subMonths(1),
                'manager_id' => $manager?->id,
                'forms' => [
                    [
                        'name' => 'Instrumen Assessment Awal PPKS Terlantar',
                        'file_path' => 'forms/instrumen_assessment_ppks.pdf',
                        'version' => '1.0',
                        'is_current' => true,
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'Apakah penanganan ODGJ atau lansia terlantar dipungut biaya?',
                        'answer' => 'Sama sekali tidak dipungut biaya (GRATIS). Seluruh operasional penanganan kedaruratan sosial dibiayai oleh APBD Kabupaten Blitar dan mitra panti Provinsi.',
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'title' => 'Panduan Pengaduan Permasalahan Sosial dan Bansos',
                'slug' => 'panduan-pengaduan-sosial',
                'category' => InformationCategory::Complaint->value,
                'service_type_id' => null,
                'description' => 'Mekanisme pelaporan dan pengaduan sosial secara online, terarah, dan transparan bagi seluruh warga Kabupaten Blitar.',
                'requirements' => "1. Nama dan nomor telepon/WhatsApp pelapor yang aktif\n2. Uraian jelas peristiwa / permasalahan sosial\n3. Lokasi detail (minimal nama desa/kelurahan dan kecamatan di Kabupaten Blitar)\n4. Bukti foto pendukung (sangat disarankan)",
                'procedure' => "1. Masuk ke menu 'Pengaduan Sosial' di halaman utama SAPA SOSIAL\n2. Isi formulir laporan dan cantumkan titik lokasi\n3. Dapatkan nomor tiket pengaduan unik (mis. ADU-202610-00001)\n4. Petugas verifikasi memeriksa dalam 1x24 jam dan mendisposisikan ke bidang terkait\n5. Pelapor dapat memantau catatan perkembangan langsung lewat tiket",
                'service_hours' => 'Sistem Penerimaan Laporan Online: 24 Jam Nonstop',
                'location' => 'Sub Bagian Umum & Kepegawaian / Tim Pengaduan Masyarakat Dinas Sosial',
                'contact' => 'Email: aduan.dinsos@blitarkab.go.id | WA Aduan: 0812-9876-5432',
                'publish_status' => PublishStatus::Published->value,
                'published_at' => Carbon::now()->subMonths(1),
                'manager_id' => $manager?->id,
                'forms' => [],
                'faqs' => [
                    [
                        'question' => 'Apakah identitas pelapor pengaduan dijamin kerahasiaannya?',
                        'answer' => 'Ya, identitas pelapor dilindungi dan hanya dapat dilihat oleh petugas administrator penanganan pengaduan Dinsos Kabupaten Blitar.',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ];

        foreach ($pages as $pData) {
            $forms = $pData['forms'];
            $faqs = $pData['faqs'];
            unset($pData['forms'], $pData['faqs']);

            $page = InformationPage::updateOrCreate(
                ['slug' => $pData['slug']],
                $pData
            );

            foreach ($forms as $formData) {
                DownloadableForm::updateOrCreate(
                    [
                        'information_page_id' => $page->id,
                        'name' => $formData['name'],
                    ],
                    $formData
                );
            }

            foreach ($faqs as $faqData) {
                Faq::updateOrCreate(
                    [
                        'information_page_id' => $page->id,
                        'question' => $faqData['question'],
                    ],
                    $faqData
                );
            }

            // Seed Page Visits for statistics
            for ($i = 10; $i >= 0; $i--) {
                PageVisit::updateOrCreate(
                    [
                        'information_page_id' => $page->id,
                        'visit_date' => Carbon::today()->subDays($i),
                    ],
                    [
                        'visit_count' => rand(15, 80),
                    ]
                );
            }
        }

        // General FAQs not linked to a specific page
        $generalFaqs = [
            [
                'question' => 'Apa itu aplikasi SAPA SOSIAL Kabupaten Blitar?',
                'answer' => 'SAPA SOSIAL (Satu Pintu Layanan Sosial) adalah sistem terpadu Dinas Sosial Kabupaten Blitar untuk memudahkan masyarakat mengajukan layanan, menyampaikan pengaduan sosial, serta mengecek status berkas secara online dan transparan menggunakan nomor tiket.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah saya harus datang ke kantor Dinas Sosial setelah mengajukan tiket online?',
                'answer' => 'Untuk layanan Surat Keterangan DTSEN tidak perlu datang, dokumen resmi ber-QR Code dapat diunduh langsung. Untuk layanan reaktivasi KIS atau rujukan rehabilitasi, Anda akan dihubungi oleh petugas apabila memerlukan berkas fisik atau wawancara lanjutan.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara memeriksa status pengajuan atau pengaduan saya?',
                'answer' => 'Cukup buka halaman Beranda SAPA SOSIAL, masukkan Nomor Tiket (misal: DTSEN-202609-00001) beserta 4 digit terakhir NIK atau nomor WhatsApp yang Anda daftarkan.',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($generalFaqs as $gFaq) {
            Faq::updateOrCreate(
                ['question' => $gFaq['question']],
                array_merge($gFaq, ['information_page_id' => null])
            );
        }

        // Seed some search logs
        $sampleSearches = [
            ['keyword' => 'surat dtsen spmb', 'count' => 45],
            ['keyword' => 'reaktivasi kis mati', 'count' => 38],
            ['keyword' => 'cek desil blitar', 'count' => 29],
            ['keyword' => 'bantuan lansia', 'count' => 22],
            ['keyword' => 'lapor odgj', 'count' => 17],
            ['keyword' => 'kip kuliah dtsen', 'count' => 14],
        ];

        foreach ($sampleSearches as $search) {
            SearchLog::create([
                'keyword' => $search['keyword'],
                'result_count' => $search['count'],
                'searched_at' => Carbon::now()->subHours(rand(1, 48)),
            ]);
        }
    }
}
