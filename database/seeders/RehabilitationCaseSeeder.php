<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Enums\ReferralStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\RehabilitationHandlingType;
use App\Models\Assessment;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\Complaint;
use App\Models\MonitoringRecord;
use App\Models\Referral;
use App\Models\ReferralInstitution;
use App\Models\RehabilitationCase;
use App\Models\ServiceRequest;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RehabilitationCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catLut = ClientCategory::where('name', 'like', '%Lanjut Usia Terlantar%')->first();
        $catOdgj = ClientCategory::where('name', 'like', '%Gangguan Jiwa%')->first();
        $catAnak = ClientCategory::where('name', 'like', '%Anak Terlantar%')->first();
        $catDisabilitas = ClientCategory::where('name', 'like', '%Disabilitas Terlantar%')->first();

        $satreyan = Village::where('name', 'Satreyan')->first();
        $beru = Village::where('name', 'Beru')->first();
        $kalipang = Village::where('name', 'Kalipang')->first();

        $petugasRehsos = User::where('email', 'petugas.rehsos@blitarkab.go.id')->first();

        $pstwBlitar = ReferralInstitution::where('name', 'like', '%Tresna Werdha%')->first();
        $rsjMenur = ReferralInstitution::where('name', 'like', '%Menur%')->first();
        $rsudWlingi = ReferralInstitution::where('name', 'like', '%Ngudi Waluyo%')->first();

        $sourceComplaint = Complaint::where('complaint_number', 'ADU-202609-00001')->first();
        $sourceRequest = ServiceRequest::where('request_number', 'RHS-REQ-202609-00001')->first();

        // -------------------------------------------------------------
        // KASUS 1: ODGJ Terlantar dari Pengaduan Masyarakat (ADU-202609-00001)
        // Klien: Mr. X (Identitas Belum Lengkap / NIK null) -> Dirujuk ke RS Jiwa
        // -------------------------------------------------------------
        $clientOdgj = Client::updateOrCreate(
            ['name' => 'Joko / Mr. X (ODGJ Terlantar Pasar Beru)'],
            [
                'client_category_id' => $catOdgj->id,
                'nik' => null,
                'birth_date' => Carbon::parse('1990-01-01'),
                'gender' => Gender::Male->value,
                'address' => 'Ditemukan di Kompleks Pasar Beru Wlingi',
                'village_id' => $beru->id,
                'phone' => null,
            ]
        );

        $case1 = RehabilitationCase::updateOrCreate(
            ['case_number' => 'RHS-202609-00001'],
            [
                'client_id' => $clientOdgj->id,
                'service_request_id' => null,
                'complaint_id' => $sourceComplaint?->id,
                'officer_id' => $petugasRehsos?->id,
                'handling_type' => RehabilitationHandlingType::Referral,
                'status' => RehabilitationCaseStatus::InService,
                'received_at' => Carbon::now()->subDays(2),
            ]
        );

        $this->logStatus($case1, null, RehabilitationCaseStatus::Received->value, 'Kasus dibuka dari laporan pengaduan masyarakat.', $petugasRehsos?->id);
        $this->logStatus($case1, RehabilitationCaseStatus::Received->value, RehabilitationCaseStatus::Assessment->value, 'Assessment awal di RSUD Ngudi Waluyo Wlingi.', $petugasRehsos?->id);
        $this->logStatus($case1, RehabilitationCaseStatus::Assessment->value, RehabilitationCaseStatus::InService->value, 'Rujukan penanganan ke Rumah Sakit Jiwa Menur Surabaya.', $petugasRehsos?->id);

        $assessment1 = Assessment::updateOrCreate(
            ['rehabilitation_case_id' => $case1->id],
            [
                'officer_id' => $petugasRehsos->id,
                'assessment_date' => Carbon::today()->subDays(2),
                'result' => 'Klien mengalami disorientasi waktu dan tempat, afek tumpul, halusinasi auditorik aktif. Tidak kooperatif saat ditanya nama lengkap keluarga.',
                'service_needs' => 'Perawatan stabilisasi kejiwaan intensif dan terapi medikamentosa psikiatri.',
                'recommendation' => 'Rujukan segera ke RS Jiwa Menur Provinsi Jawa Timur dengan pendampingan Pekerja Sosial Dinsos.',
                'needs_referral' => true,
            ]
        );

        $referral1 = Referral::updateOrCreate(
            ['referral_number' => 'RJK-202609-00001'],
            [
                'rehabilitation_case_id' => $case1->id,
                'assessment_id' => $assessment1->id,
                'referral_institution_id' => $rsjMenur->id,
                'officer_id' => $petugasRehsos->id,
                'referral_date' => Carbon::today()->subDay(),
                'status' => ReferralStatus::InService,
                'service_result' => 'Klien telah diterima di ruang observasi RS Jiwa Menur Surabaya, sedang menjalani terapi penstabilan.',
            ]
        );

        StatusHistory::create([
            'statusable_type' => Referral::class,
            'statusable_id' => $referral1->id,
            'from_status' => ReferralStatus::Draft->value,
            'to_status' => ReferralStatus::InService->value,
            'notes' => 'Surat rujukan dan serah terima klien telah diterima pihak RSJ Menur.',
            'user_id' => $petugasRehsos->id,
            'created_at' => Carbon::now()->subDay(),
        ]);

        MonitoringRecord::updateOrCreate(
            [
                'rehabilitation_case_id' => $case1->id,
                'referral_id' => $referral1->id,
                'monitoring_date' => Carbon::today(),
            ],
            [
                'officer_id' => $petugasRehsos->id,
                'progress' => 'Kondisi klien mulai tenang, tidak lagi agresif setelah mendapatkan pengobatan. Tim Dinsos terus berkoordinasi dengan tim medis untuk upaya penelusuran keluarga (tracing).',
                'result_notes' => 'Tracing keluarga akan dilanjutkan bersama Dinas Kependudukan dan Catatan Sipil melalui biometrik sidik jari.',
            ]
        );

        // -------------------------------------------------------------
        // KASUS 2: Lansia Terlantar dari Pengajuan Layanan (RHS-REQ-202609-00001)
        // Klien: Mbah Slamet (72 th) -> Rujukan Panti Werdha PSTW Blitar
        // -------------------------------------------------------------
        $clientLut = Client::updateOrCreate(
            ['name' => 'Mbah Slamet'],
            [
                'client_category_id' => $catLut->id,
                'nik' => '3505030201520004',
                'birth_date' => Carbon::parse('1952-01-02'),
                'gender' => Gender::Male->value,
                'address' => 'RT 01 RW 02, Desa Satreyan, Kanigoro',
                'village_id' => $satreyan->id,
                'phone' => null,
            ]
        );

        $case2 = RehabilitationCase::updateOrCreate(
            ['case_number' => 'RHS-202609-00002'],
            [
                'client_id' => $clientLut->id,
                'service_request_id' => $sourceRequest?->id,
                'complaint_id' => null,
                'officer_id' => $petugasRehsos?->id,
                'handling_type' => RehabilitationHandlingType::Both,
                'status' => RehabilitationCaseStatus::Monitoring,
                'received_at' => Carbon::now()->subDays(3),
            ]
        );

        $this->logStatus($case2, null, RehabilitationCaseStatus::Received->value, 'Kasus masuk dari usulan Puskesos Kanigoro.', $petugasRehsos?->id);
        $this->logStatus($case2, RehabilitationCaseStatus::Received->value, RehabilitationCaseStatus::ServicePlanning->value, 'Rencana rujukan panti disetujui.', $petugasRehsos?->id);
        $this->logStatus($case2, RehabilitationCaseStatus::ServicePlanning->value, RehabilitationCaseStatus::InService->value, 'Klien resmi ditampung di PSTW Blitar.', $petugasRehsos?->id);
        $this->logStatus($case2, RehabilitationCaseStatus::InService->value, RehabilitationCaseStatus::Monitoring->value, 'Pemantauan adaptasi klien di panti.', $petugasRehsos?->id);

        $assessment2 = Assessment::updateOrCreate(
            ['rehabilitation_case_id' => $case2->id],
            [
                'officer_id' => $petugasRehsos->id,
                'assessment_date' => Carbon::today()->subDays(3),
                'result' => 'Lansia berusia 72 tahun sebatang kara, tidak memiliki sanak saudara, tempat tinggal gubuk tidak layak huni dan atap bocor parah. Mengalami keluhan asam urat dan penurunan nafsu makan.',
                'service_needs' => 'Pemenuhan kebutuhan dasar makan minum, tempat tinggal aman, dan perawatan medis geriatri.',
                'recommendation' => 'Penerbitan surat rujukan dan terminasi penampungan tetap di UPT PSTW Blitar (Panti Werdha).',
                'needs_referral' => true,
            ]
        );

        $referral2 = Referral::updateOrCreate(
            ['referral_number' => 'RJK-202609-00002'],
            [
                'rehabilitation_case_id' => $case2->id,
                'assessment_id' => $assessment2->id,
                'referral_institution_id' => $pstwBlitar->id,
                'officer_id' => $petugasRehsos->id,
                'referral_date' => Carbon::today()->subDays(2),
                'status' => ReferralStatus::InService,
                'service_result' => 'Klien telah resmi tercatat sebagai penerima manfaat di UPT PSTW Blitar di Wisma Mawar.',
            ]
        );

        MonitoringRecord::updateOrCreate(
            [
                'rehabilitation_case_id' => $case2->id,
                'referral_id' => $referral2->id,
                'monitoring_date' => Carbon::today()->subDay(),
            ],
            [
                'officer_id' => $petugasRehsos->id,
                'progress' => 'Klien beradaptasi dengan baik, kondisi fisik membaik dengan pola makan teratur 3 kali sehari dan telah diperiksa dokter klinik panti.',
                'result_notes' => 'Klien merasa senang dan nyaman tinggal bersama sesama lansia.',
            ]
        );

        // -------------------------------------------------------------
        // KASUS 3: Anak Terlantar / Rentan - Ditutup Selesai (Closed)
        // Penanganan Langsung Dinsos (Direct): Reunifikasi Keluarga
        // -------------------------------------------------------------
        $clientAnak = Client::updateOrCreate(
            ['name' => 'Ananda Rizky (9 th)'],
            [
                'client_category_id' => $catAnak->id,
                'nik' => '3505051804150001',
                'birth_date' => Carbon::parse('2015-04-18'),
                'gender' => Gender::Male->value,
                'address' => 'RT 02 RW 01, Kelurahan Kalipang, Sutojayan',
                'village_id' => $kalipang->id,
                'phone' => null,
            ]
        );

        $case3 = RehabilitationCase::updateOrCreate(
            ['case_number' => 'RHS-202609-00003'],
            [
                'client_id' => $clientAnak->id,
                'service_request_id' => null,
                'complaint_id' => null,
                'officer_id' => $petugasRehsos?->id,
                'handling_type' => RehabilitationHandlingType::Direct,
                'status' => RehabilitationCaseStatus::Closed,
                'handling_result' => 'Anak berhasil direunifikasi dan diserahkan kembali kepada keluarga besar (kakek & nenek) dengan disaksikan perangkat Desa Kalipang. Bantuan perlengkapan sekolah dan nutrisi telah diserahkan.',
                'received_at' => Carbon::now()->subDays(10),
                'closed_at' => Carbon::now()->subDays(2),
            ]
        );

        $this->logStatus($case3, null, RehabilitationCaseStatus::Received->value, 'Anak terlantar dilaporkan warga tersesat di stasiun.', $petugasRehsos?->id);
        $this->logStatus($case3, RehabilitationCaseStatus::Received->value, RehabilitationCaseStatus::Assessment->value, 'Assessment kondisi psikologis anak.', $petugasRehsos?->id);
        $this->logStatus($case3, RehabilitationCaseStatus::Assessment->value, RehabilitationCaseStatus::Closed->value, 'Reunifikasi sukses dan kasus ditutup.', $petugasRehsos?->id);

        Assessment::updateOrCreate(
            ['rehabilitation_case_id' => $case3->id],
            [
                'officer_id' => $petugasRehsos->id,
                'assessment_date' => Carbon::today()->subDays(9),
                'result' => 'Anak terpisah dari orang tua yang bekerja di luar daerah, tinggal bersama paman yang kemudian pindah. Mengalami rasa cemas dan belum bersekolah secara teratur.',
                'service_needs' => 'Pendampingan psikososial, penelusuran keluarga besar (kakek/nenek), serta dukungan administrasi pendidikan.',
                'recommendation' => 'Reunifikasi dengan kakek di Sutojayan dan fasilitasi bantuan peralatan sekolah.',
                'needs_referral' => false,
            ]
        );

        MonitoringRecord::updateOrCreate(
            [
                'rehabilitation_case_id' => $case3->id,
                'referral_id' => null,
                'monitoring_date' => Carbon::today()->subDays(3),
            ],
            [
                'officer_id' => $petugasRehsos->id,
                'progress' => 'Anak telah kembali tinggal bersama kakek dan bersekolah di SDN Kalipang 1.',
                'result_notes' => 'Keluarga menerima pendampingan lanjutan dari pendamping PKH setempat.',
            ]
        );
    }

    private function logStatus(RehabilitationCase $case, ?string $from, string $to, string $notes, ?int $userId): void
    {
        StatusHistory::create([
            'statusable_type' => RehabilitationCase::class,
            'statusable_id' => $case->id,
            'from_status' => $from,
            'to_status' => $to,
            'notes' => $notes,
            'user_id' => $userId,
            'created_at' => Carbon::now()->subMinutes(rand(10, 180)),
        ]);
    }
}
