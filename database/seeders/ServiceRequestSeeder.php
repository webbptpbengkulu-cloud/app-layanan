<?php

namespace Database\Seeders;

use App\Enums\ApprovalDecision;
use App\Enums\DocumentVerificationStatus;
use App\Enums\MinistryDecision;
use App\Enums\PbiReason;
use App\Enums\ServiceRequestStatus;
use App\Models\Approval;
use App\Models\Disposition;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\PbiReactivation;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestDocument;
use App\Models\ServiceType;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ServiceRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dtsenType = ServiceType::where('code', 'DTSEN')->with('requirements')->first();
        $pbiType = ServiceType::where('code', 'PBI')->with('requirements')->first();
        $rehsosReqType = ServiceType::where('code', 'REHSOS_REQ')->with('requirements')->first();
        $bansosType = ServiceType::where('code', 'REK_BANSOS')->with('requirements')->first();

        $purposeSpmb = DtsenPurpose::where('code', 'spmb')->first();
        $purposePip = DtsenPurpose::where('code', 'pip')->first();
        $purposeKip = DtsenPurpose::where('code', 'kip_kuliah')->first();
        $purposeBansos = DtsenPurpose::where('code', 'bansos')->first();

        $linjamsosUnit = WorkUnit::where('name', 'like', '%Perlindungan dan Jaminan Sosial%')->first();
        $rehsosUnit = WorkUnit::where('name', 'like', '%Rehabilitasi Sosial%')->first();
        $dayasosUnit = WorkUnit::where('name', 'like', '%Pemberdayaan Sosial%')->first();

        $petugasLinjamsos = User::where('email', 'petugas.linjamsos@blitarkab.go.id')->first();
        $petugasRehsos = User::where('email', 'petugas.rehsos@blitarkab.go.id')->first();
        $kabidLinjamsos = User::where('email', 'kabid.linjamsos@blitarkab.go.id')->first();
        $kadis = User::where('email', 'kadis@blitarkab.go.id')->first();

        $pemohon1 = User::where('email', 'pemohon.dtsen@gmail.com')->first();
        $pemohon2 = User::where('email', 'pemohon.pbi@gmail.com')->first();
        $operatorKanigoro = User::where('email', 'operator.kanigoro@blitarkab.go.id')->first();

        $satreyan = Village::where('name', 'Satreyan')->first();
        $beru = Village::where('name', 'Beru')->first();
        $kalipang = Village::where('name', 'Kalipang')->first();

        // -------------------------------------------------------------
        // CASE 1: DTSEN - Diajukan (Baru Masuk)
        // -------------------------------------------------------------
        $req1 = ServiceRequest::updateOrCreate(
            ['request_number' => 'DTSEN-202609-00001'],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => $pemohon1?->id,
                'applicant_name' => 'Siti Aminah',
                'applicant_nik' => '3505035504850002',
                'family_card_number' => '3505031201080005',
                'address' => 'RT 02 RW 03, Dusun Subontoro, Desa Satreyan',
                'village_id' => $satreyan->id,
                'phone' => '085712345001',
                'submitted_at' => Carbon::now()->subHours(5),
                'officer_id' => null,
                'work_unit_id' => $linjamsosUnit?->id,
                'status' => ServiceRequestStatus::Submitted,
                'is_priority' => false,
            ]
        );

        $this->seedDocuments($req1, $dtsenType, DocumentVerificationStatus::Pending);
        $this->logStatus($req1, null, ServiceRequestStatus::Submitted->value, 'Pengajuan baru didaftarkan secara mandiri oleh pemohon.', $pemohon1?->id);

        DtsenCertificate::updateOrCreate(
            ['service_request_id' => $req1->id],
            [
                'dtsen_purpose_id' => $purposeSpmb->id,
                'purpose_description' => 'Persyaratan pendaftaran SPMB Jalur Afirmasi SMAN 1 Talun',
                'subject_name' => 'Ahmad Fajar Pratama',
                'subject_nik' => '3505031505080001',
                'relationship_to_applicant' => 'Anak Kandung',
                'is_registered' => false,
            ]
        );

        // -------------------------------------------------------------
        // CASE 2: DTSEN - Pemeriksaan / Verifikasi Data (SIKS-NG)
        // -------------------------------------------------------------
        $req2 = ServiceRequest::updateOrCreate(
            ['request_number' => 'DTSEN-202609-00002'],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => $operatorKanigoro?->id,
                'applicant_name' => 'Suparman',
                'applicant_nik' => '3505031808790003',
                'family_card_number' => '3505032504060012',
                'address' => 'Jl. Kenanga No. 14, RT 01 RW 01, Kanigoro',
                'village_id' => $satreyan->id,
                'phone' => '085798765002',
                'submitted_at' => Carbon::now()->subDays(1),
                'officer_id' => $petugasLinjamsos?->id,
                'work_unit_id' => $linjamsosUnit?->id,
                'status' => ServiceRequestStatus::DataVerification,
                'is_priority' => false,
                'officer_notes' => 'Dokumen KTP dan KK lengkap. Sedang pengecekan ID BDT/DTSEN pada database SIKS-NG.',
            ]
        );

        $this->seedDocuments($req2, $dtsenType, DocumentVerificationStatus::Valid);
        $this->logStatus($req2, null, ServiceRequestStatus::Submitted->value, 'Pengajuan didaftarkan oleh Puskesos Kanigoro.', $operatorKanigoro?->id);
        $this->logStatus($req2, ServiceRequestStatus::Submitted->value, ServiceRequestStatus::DataVerification->value, 'Berkas dinyatakan lengkap, lanjut verifikasi SIKS-NG.', $petugasLinjamsos?->id);

        DtsenCertificate::updateOrCreate(
            ['service_request_id' => $req2->id],
            [
                'dtsen_purpose_id' => $purposePip->id,
                'purpose_description' => 'Kelengkapan usulan Program Indonesia Pintar (PIP) SMPN 1 Kanigoro',
                'subject_name' => 'Fitria Ramadhani',
                'subject_nik' => '3505035201100002',
                'relationship_to_applicant' => 'Anak Kandung',
                'is_registered' => true,
                'decile' => 3,
                'checked_at' => Carbon::now()->subHours(2),
                'checker_id' => $petugasLinjamsos?->id,
            ]
        );

        // -------------------------------------------------------------
        // CASE 3: DTSEN - Menunggu Tanda Tangan / Persetujuan (Awaiting Approval)
        // -------------------------------------------------------------
        $req3 = ServiceRequest::updateOrCreate(
            ['request_number' => 'DTSEN-202609-00003'],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => $pemohon1?->id,
                'applicant_name' => 'Wahyudi',
                'applicant_nik' => '3505101206840004',
                'family_card_number' => '3505102008050019',
                'address' => 'Lingkungan Beru RT 03 RW 02, Wlingi',
                'village_id' => $beru->id,
                'phone' => '081333444003',
                'submitted_at' => Carbon::now()->subDays(2),
                'officer_id' => $petugasLinjamsos?->id,
                'work_unit_id' => $linjamsosUnit?->id,
                'status' => ServiceRequestStatus::AwaitingApproval,
                'is_priority' => false,
                'verification_result' => 'Data terdaftar di DTSEN dengan Desil 2 (Keluarga Miskin). Memenuhi kriteria KIP Kuliah.',
                'officer_notes' => 'Draf surat telah dibuat otomatis dari template, menunggu persetujuan berjenjang.',
            ]
        );

        $this->seedDocuments($req3, $dtsenType, DocumentVerificationStatus::Valid);
        $this->logStatus($req3, null, ServiceRequestStatus::Submitted->value, 'Tiket dibuat.', $pemohon1?->id);
        $this->logStatus($req3, ServiceRequestStatus::Submitted->value, ServiceRequestStatus::DataVerification->value, 'Verifikasi berkas & cek SIKS-NG.', $petugasLinjamsos?->id);
        $this->logStatus($req3, ServiceRequestStatus::DataVerification->value, ServiceRequestStatus::AwaitingApproval->value, 'Draf SK DTSEN siap untuk ditandatangani pejabat.', $petugasLinjamsos?->id);

        $cert3 = DtsenCertificate::updateOrCreate(
            ['service_request_id' => $req3->id],
            [
                'dtsen_purpose_id' => $purposeKip->id,
                'purpose_description' => 'Pendaftaran KIP Kuliah di Universitas Brawijaya',
                'subject_name' => 'Muhammad Bagas Wahyudi',
                'subject_nik' => '3505101004060001',
                'relationship_to_applicant' => 'Anak Kandung',
                'is_registered' => true,
                'decile' => 2,
                'checked_at' => Carbon::now()->subDays(1),
                'checker_id' => $petugasLinjamsos?->id,
                'certificate_number' => null,
            ]
        );

        // Approval Step 1 (Kabid Linjamsos - Disetujui/Paraf)
        Approval::updateOrCreate(
            [
                'approvable_type' => DtsenCertificate::class,
                'approvable_id' => $cert3->id,
                'step' => 1,
            ],
            [
                'approver_id' => $kabidLinjamsos->id,
                'decision' => ApprovalDecision::Approved->value,
                'notes' => 'Telah diperiksa, berkas dan hasil SIKS-NG desil 2 sesuai ketentuan. Siap TTE Kepala Dinas.',
                'decided_at' => Carbon::now()->subHours(6),
            ]
        );

        // Approval Step 2 (Kadis - Pending)
        Approval::updateOrCreate(
            [
                'approvable_type' => DtsenCertificate::class,
                'approvable_id' => $cert3->id,
                'step' => 2,
            ],
            [
                'approver_id' => $kadis->id,
                'decision' => ApprovalDecision::Pending->value,
                'notes' => null,
                'decided_at' => null,
            ]
        );

        // -------------------------------------------------------------
        // CASE 4: DTSEN - Selesai & Terbit (Completed / Issued)
        // -------------------------------------------------------------
        $req4 = ServiceRequest::updateOrCreate(
            ['request_number' => 'DTSEN-202609-00004'],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => $pemohon1?->id,
                'applicant_name' => 'Maryanto',
                'applicant_nik' => '3505051103750005',
                'family_card_number' => '3505051907030022',
                'address' => 'RT 04 RW 01, Kelurahan Kalipang, Sutojayan',
                'village_id' => $kalipang->id,
                'phone' => '081223344004',
                'submitted_at' => Carbon::now()->subDays(4),
                'officer_id' => $petugasLinjamsos?->id,
                'work_unit_id' => $linjamsosUnit?->id,
                'status' => ServiceRequestStatus::Completed,
                'is_priority' => false,
                'verification_result' => 'Terdaftar pada DTSEN dengan Desil 1. Memenuhi syarat SPMB Jalur Afirmasi.',
                'service_result' => 'Surat Keterangan DTSEN Nomor 400.9/042/409.106/2026 telah terbit dan dapat diunduh.',
                'completed_at' => Carbon::now()->subDays(2),
            ]
        );

        $this->seedDocuments($req4, $dtsenType, DocumentVerificationStatus::Valid);
        $this->logStatus($req4, null, ServiceRequestStatus::Submitted->value, 'Pengajuan masuk.', $pemohon1?->id);
        $this->logStatus($req4, ServiceRequestStatus::Submitted->value, ServiceRequestStatus::DataVerification->value, 'Verifikasi SIKS-NG valid.', $petugasLinjamsos?->id);
        $this->logStatus($req4, ServiceRequestStatus::DataVerification->value, ServiceRequestStatus::AwaitingApproval->value, 'Menunggu persetujuan berjenjang.', $petugasLinjamsos?->id);
        $this->logStatus($req4, ServiceRequestStatus::AwaitingApproval->value, ServiceRequestStatus::Completed->value, 'SK DTSEN resmi diterbitkan dengan QR verifikasi.', $kadis?->id);

        $cert4 = DtsenCertificate::updateOrCreate(
            ['service_request_id' => $req4->id],
            [
                'dtsen_purpose_id' => $purposeSpmb->id,
                'purpose_description' => 'SPMB Jalur Afirmasi SMAN 1 Sutojayan',
                'subject_name' => 'Dwi Cahyo Maryanto',
                'subject_nik' => '3505050508080003',
                'relationship_to_applicant' => 'Anak Kandung',
                'is_registered' => true,
                'decile' => 1,
                'checked_at' => Carbon::now()->subDays(3),
                'checker_id' => $petugasLinjamsos?->id,
                'certificate_number' => '400.9/042/409.106/2026',
                'issued_at' => Carbon::now()->subDays(2),
                'valid_until' => Carbon::today()->addDays(30),
                'signer_id' => $kadis?->id,
                'file_path' => 'certificates/dtsen_400_9_042_409_106_2026.pdf',
                'verification_code' => 'DTSEN-202609-V88912',
            ]
        );

        Approval::updateOrCreate(
            ['approvable_type' => DtsenCertificate::class, 'approvable_id' => $cert4->id, 'step' => 1],
            ['approver_id' => $kabidLinjamsos->id, 'decision' => ApprovalDecision::Approved->value, 'notes' => 'Paraf disetujui.', 'decided_at' => Carbon::now()->subDays(3)]
        );
        Approval::updateOrCreate(
            ['approvable_type' => DtsenCertificate::class, 'approvable_id' => $cert4->id, 'step' => 2],
            ['approver_id' => $kadis->id, 'decision' => ApprovalDecision::Approved->value, 'notes' => 'Ditandatangani secara digital.', 'decided_at' => Carbon::now()->subDays(2)]
        );

        // -------------------------------------------------------------
        // CASE 5: DTSEN - Ditolak (Rejected - Desil Di Luar Ketentuan)
        // -------------------------------------------------------------
        $req5 = ServiceRequest::updateOrCreate(
            ['request_number' => 'DTSEN-202609-00005'],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => $pemohon1?->id,
                'applicant_name' => 'Hartono',
                'applicant_nik' => '3505031402800006',
                'family_card_number' => '3505032009020015',
                'address' => 'RT 01 RW 04, Desa Gaprang, Kanigoro',
                'village_id' => $satreyan->id,
                'phone' => '085811223005',
                'submitted_at' => Carbon::now()->subDays(3),
                'officer_id' => $petugasLinjamsos?->id,
                'work_unit_id' => $linjamsosUnit?->id,
                'status' => ServiceRequestStatus::Rejected,
                'is_priority' => false,
                'verification_result' => 'Hasil pengecekan SIKS-NG: NIK terdaftar pada peringkat Desil 7.',
                'rejection_reason' => 'Permohonan ditolak karena peringkat desil (Desil 7) melebihi batas maksimal ketentuan SPMB Afirmasi (Maksimal Desil 5). Pemohon dapat berkonsultasi ke Desa terkait pemutakhiran data sosial.',
                'completed_at' => Carbon::now()->subDays(2),
            ]
        );

        $this->seedDocuments($req5, $dtsenType, DocumentVerificationStatus::Valid);
        $this->logStatus($req5, null, ServiceRequestStatus::Submitted->value, 'Pengajuan masuk.', $pemohon1?->id);
        $this->logStatus($req5, ServiceRequestStatus::Submitted->value, ServiceRequestStatus::Rejected->value, 'Pengajuan ditolak karena desil melebihi batas regulasi.', $petugasLinjamsos?->id);

        DtsenCertificate::updateOrCreate(
            ['service_request_id' => $req5->id],
            [
                'dtsen_purpose_id' => $purposeSpmb->id,
                'purpose_description' => 'SPMB Afirmasi',
                'subject_name' => 'Rangga Hartono',
                'subject_nik' => '3505031405080002',
                'relationship_to_applicant' => 'Anak Kandung',
                'is_registered' => true,
                'decile' => 7,
                'checked_at' => Carbon::now()->subDays(2),
                'checker_id' => $petugasLinjamsos?->id,
            ]
        );

        // -------------------------------------------------------------
        // CASE 6: PBI-JK - Darurat Medis (Prioritas & Baru Masuk)
        // -------------------------------------------------------------
        $req6 = ServiceRequest::updateOrCreate(
            ['request_number' => 'PBI-202609-00001'],
            [
                'service_type_id' => $pbiType->id,
                'submitter_id' => $pemohon2?->id,
                'applicant_name' => 'Budi Santoso',
                'applicant_nik' => '3505101407880003',
                'family_card_number' => '3505101201080009',
                'address' => 'RT 02 RW 01, Kelurahan Beru, Wlingi',
                'village_id' => $beru->id,
                'phone' => '085712345002',
                'submitted_at' => Carbon::now()->subHours(3),
                'officer_id' => $petugasLinjamsos?->id,
                'work_unit_id' => $linjamsosUnit?->id,
                'status' => ServiceRequestStatus::Submitted,
                'is_priority' => true, // DARURAT MEDIS
                'officer_notes' => 'Pasien sedang di IGD RSUD Ngudi Waluyo Wlingi, penanganan segera dibutuhkan.',
            ]
        );

        $this->seedDocuments($req6, $pbiType, DocumentVerificationStatus::Pending);
        $this->logStatus($req6, null, ServiceRequestStatus::Submitted->value, 'Pengajuan darurat medis didaftarkan keluarga pasien.', $pemohon2?->id);

        PbiReactivation::updateOrCreate(
            ['service_request_id' => $req6->id],
            [
                'participant_name' => 'Budi Santoso',
                'participant_nik' => '3505101407880003',
                'bpjs_card_number' => '0001889922114',
                'deactivated_date' => Carbon::today()->subMonths(2),
                'reason' => PbiReason::Emergency,
                'health_facility_name' => 'RSUD Ngudi Waluyo Wlingi',
                'health_letter_number' => '445/892/RSUD/IX/2026',
                'decile' => null,
            ]
        );

        // -------------------------------------------------------------
        // CASE 7: PBI-JK - Verifikasi Kelayakan (Eligibility Verification)
        // -------------------------------------------------------------
        $req7 = ServiceRequest::updateOrCreate(
            ['request_number' => 'PBI-202609-00002'],
            [
                'service_type_id' => $pbiType->id,
                'submitter_id' => $operatorKanigoro?->id,
                'applicant_name' => 'Samsuri',
                'applicant_nik' => '3505031105650007',
                'family_card_number' => '3505032004070014',
                'address' => 'RT 03 RW 02, Desa Satreyan, Kanigoro',
                'village_id' => $satreyan->id,
                'phone' => '081234999007',
                'submitted_at' => Carbon::now()->subDays(1),
                'officer_id' => $petugasLinjamsos?->id,
                'work_unit_id' => $linjamsosUnit?->id,
                'status' => ServiceRequestStatus::EligibilityVerification,
                'is_priority' => false,
                'officer_notes' => 'Pasien cuci darah rutin (Gagal Ginjal Kronik). Sedang verifikasi kelayakan desil.',
            ]
        );

        $this->seedDocuments($req7, $pbiType, DocumentVerificationStatus::Valid);
        $this->logStatus($req7, null, ServiceRequestStatus::Submitted->value, 'Diajukan via Puskesos.', $operatorKanigoro?->id);
        $this->logStatus($req7, ServiceRequestStatus::Submitted->value, ServiceRequestStatus::EligibilityVerification->value, 'Sedang verifikasi kelayakan di SIKS-NG.', $petugasLinjamsos?->id);

        PbiReactivation::updateOrCreate(
            ['service_request_id' => $req7->id],
            [
                'participant_name' => 'Samsuri',
                'participant_nik' => '3505031105650007',
                'bpjs_card_number' => '0001556677881',
                'deactivated_date' => Carbon::today()->subMonths(3),
                'reason' => PbiReason::Chronic,
                'health_facility_name' => 'RSUD Ngudi Waluyo Wlingi',
                'health_letter_number' => '445/702/Hemodialisa/2026',
                'decile' => 2,
                'eligibility_notes' => 'Peserta terdaftar desil 2, riwayat gagal ginjal tahap 5, layak diaktifkan kembali.',
            ]
        );

        // -------------------------------------------------------------
        // CASE 8: PBI-JK - Menunggu Persetujuan Rekomendasi (Awaiting Approval)
        // -------------------------------------------------------------
        $req8 = ServiceRequest::updateOrCreate(
            ['request_number' => 'PBI-202609-00003'],
            [
                'service_type_id' => $pbiType->id,
                'submitter_id' => $pemohon2?->id,
                'applicant_name' => 'Kusnan',
                'applicant_nik' => '3505051908710008',
                'family_card_number' => '3505051201080033',
                'address' => 'RT 01 RW 03, Kelurahan Kalipang, Sutojayan',
                'village_id' => $kalipang->id,
                'phone' => '081335566008',
                'submitted_at' => Carbon::now()->subDays(3),
                'officer_id' => $petugasLinjamsos?->id,
                'work_unit_id' => $linjamsosUnit?->id,
                'status' => ServiceRequestStatus::AwaitingApproval,
                'is_priority' => false,
                'verification_result' => 'Verifikasi kelayakan memenuhi syarat (Desil 1). Draf Surat Rekomendasi siap.',
            ]
        );

        $this->seedDocuments($req8, $pbiType, DocumentVerificationStatus::Valid);
        $this->logStatus($req8, null, ServiceRequestStatus::Submitted->value, 'Pengajuan masuk.', $pemohon2?->id);
        $this->logStatus($req8, ServiceRequestStatus::Submitted->value, ServiceRequestStatus::EligibilityVerification->value, 'Cek kelayakan selesai.', $petugasLinjamsos?->id);
        $this->logStatus($req8, ServiceRequestStatus::EligibilityVerification->value, ServiceRequestStatus::AwaitingApproval->value, 'Menunggu persetujuan rekomendasi oleh Kepala Dinas.', $petugasLinjamsos?->id);

        $pbi8 = PbiReactivation::updateOrCreate(
            ['service_request_id' => $req8->id],
            [
                'participant_name' => 'Kusnan',
                'participant_nik' => '3505051908710008',
                'bpjs_card_number' => '0001334499002',
                'deactivated_date' => Carbon::today()->subMonths(1),
                'reason' => PbiReason::Catastrophic,
                'health_facility_name' => 'RSUD Srengat',
                'health_letter_number' => '445/112/Poli-Jantung/2026',
                'decile' => 1,
                'eligibility_notes' => 'Pasien penyakit jantung membutuhkan kontrol dan tindakan berkelanjutan.',
            ]
        );

        Approval::updateOrCreate(
            ['approvable_type' => PbiReactivation::class, 'approvable_id' => $pbi8->id, 'step' => 1],
            ['approver_id' => $kabidLinjamsos->id, 'decision' => ApprovalDecision::Approved->value, 'notes' => 'Paraf rekomendasi reaktivasi.', 'decided_at' => Carbon::now()->subHours(12)]
        );
        Approval::updateOrCreate(
            ['approvable_type' => PbiReactivation::class, 'approvable_id' => $pbi8->id, 'step' => 2],
            ['approver_id' => $kadis->id, 'decision' => ApprovalDecision::Pending->value, 'notes' => null, 'decided_at' => null]
        );

        // -------------------------------------------------------------
        // CASE 9: PBI-JK - Diusulkan ke Kemensos (Proposed to Ministry)
        // -------------------------------------------------------------
        $req9 = ServiceRequest::updateOrCreate(
            ['request_number' => 'PBI-202609-00004'],
            [
                'service_type_id' => $pbiType->id,
                'submitter_id' => $pemohon2?->id,
                'applicant_name' => 'Yuliatin',
                'applicant_nik' => '3505105007900009',
                'family_card_number' => '3505102506120018',
                'address' => 'RT 04 RW 02, Wlingi',
                'village_id' => $beru->id,
                'phone' => '085233112009',
                'submitted_at' => Carbon::now()->subDays(6),
                'officer_id' => $petugasLinjamsos?->id,
                'work_unit_id' => $linjamsosUnit?->id,
                'status' => ServiceRequestStatus::ProposedToMinistry,
                'is_priority' => false,
                'verification_result' => 'Surat Rekomendasi Kepala Dinas Nomor 440/118/409.106/2026 terbit. Usulan telah diinput pada SIKS-NG pusat.',
            ]
        );

        $this->seedDocuments($req9, $pbiType, DocumentVerificationStatus::Valid);
        $this->logStatus($req9, null, ServiceRequestStatus::Submitted->value, 'Pengajuan masuk.', $pemohon2?->id);
        $this->logStatus($req9, ServiceRequestStatus::Submitted->value, ServiceRequestStatus::AwaitingApproval->value, 'Rekomendasi disiapkan.', $petugasLinjamsos?->id);
        $this->logStatus($req9, ServiceRequestStatus::AwaitingApproval->value, ServiceRequestStatus::RecommendationIssued->value, 'Rekomendasi resmi terbit.', $kadis?->id);
        $this->logStatus($req9, ServiceRequestStatus::RecommendationIssued->value, ServiceRequestStatus::ProposedToMinistry->value, 'Diinput pada aplikasi SIKS-NG modul reaktivasi PBI Kemensos RI.', $petugasLinjamsos?->id);

        PbiReactivation::updateOrCreate(
            ['service_request_id' => $req9->id],
            [
                'participant_name' => 'Yuliatin',
                'participant_nik' => '3505105007900009',
                'bpjs_card_number' => '0001992288334',
                'deactivated_date' => Carbon::today()->subMonths(4),
                'reason' => PbiReason::Newborn,
                'health_facility_name' => 'Puskesmas Wlingi',
                'health_letter_number' => '440/098/PKM-WLI/2026',
                'decile' => 2,
                'recommendation_number' => '440/118/409.106/2026',
                'recommendation_issued_at' => Carbon::now()->subDays(3),
                'signer_id' => $kadis?->id,
                'proposed_to_ministry_at' => Carbon::now()->subDays(2),
                'ministry_decision' => MinistryDecision::Pending,
            ]
        );

        // -------------------------------------------------------------
        // CASE 10: PBI-JK - Selesai & Aktif Kembali (Completed / Reactivated)
        // -------------------------------------------------------------
        $req10 = ServiceRequest::updateOrCreate(
            ['request_number' => 'PBI-202609-00005'],
            [
                'service_type_id' => $pbiType->id,
                'submitter_id' => $pemohon2?->id,
                'applicant_name' => 'Tugiman',
                'applicant_nik' => '3505030804680010',
                'family_card_number' => '3505031908050021',
                'address' => 'RT 02 RW 02, Desa Satreyan, Kanigoro',
                'village_id' => $satreyan->id,
                'phone' => '081234567010',
                'submitted_at' => Carbon::now()->subDays(15),
                'officer_id' => $petugasLinjamsos?->id,
                'work_unit_id' => $linjamsosUnit?->id,
                'status' => ServiceRequestStatus::Completed,
                'is_priority' => false,
                'verification_result' => 'Rekomendasi terbit dan disetujui Kemensos.',
                'service_result' => 'Kepesertaan PBI-JK telah aktif kembali di sistem BPJS Kesehatan per tanggal 20 September 2026.',
                'completed_at' => Carbon::now()->subDays(4),
            ]
        );

        $this->seedDocuments($req10, $pbiType, DocumentVerificationStatus::Valid);
        $this->logStatus($req10, null, ServiceRequestStatus::Submitted->value, 'Pengajuan dibuat.', $pemohon2?->id);
        $this->logStatus($req10, ServiceRequestStatus::Submitted->value, ServiceRequestStatus::ProposedToMinistry->value, 'Diusulkan ke Kemensos RI.', $petugasLinjamsos?->id);
        $this->logStatus($req10, ServiceRequestStatus::ProposedToMinistry->value, ServiceRequestStatus::MinistryApproved->value, 'SK Kemensos menetapkan reaktivasi kepesertaan.', $petugasLinjamsos?->id);
        $this->logStatus($req10, ServiceRequestStatus::MinistryApproved->value, ServiceRequestStatus::Completed->value, 'Status BPJS terkonfirmasi aktif.', $petugasLinjamsos?->id);

        PbiReactivation::updateOrCreate(
            ['service_request_id' => $req10->id],
            [
                'participant_name' => 'Tugiman',
                'participant_nik' => '3505030804680010',
                'bpjs_card_number' => '0001223388776',
                'deactivated_date' => Carbon::today()->subMonths(5),
                'reason' => PbiReason::Chronic,
                'health_facility_name' => 'RSUD Ngudi Waluyo Wlingi',
                'health_letter_number' => '445/512/Poli-Dalam/2026',
                'decile' => 1,
                'recommendation_number' => '440/095/409.106/2026',
                'recommendation_issued_at' => Carbon::now()->subDays(12),
                'signer_id' => $kadis?->id,
                'proposed_to_ministry_at' => Carbon::now()->subDays(10),
                'ministry_decision' => MinistryDecision::Approved,
                'ministry_decided_at' => Carbon::now()->subDays(5),
                'reactivated_date' => Carbon::today()->subDays(4),
            ]
        );

        // -------------------------------------------------------------
        // CASE 11: Layanan Lain - Permohonan Rehsos (REHSOS_REQ)
        // -------------------------------------------------------------
        $req11 = ServiceRequest::updateOrCreate(
            ['request_number' => 'RHS-REQ-202609-00001'],
            [
                'service_type_id' => $rehsosReqType->id,
                'submitter_id' => $operatorKanigoro?->id,
                'applicant_name' => 'Bagus Wicaksono (Puskesos)',
                'applicant_nik' => '3505032108930001',
                'family_card_number' => '3505032108930001',
                'address' => 'Desa Satreyan, Kanigoro',
                'village_id' => $satreyan->id,
                'phone' => '081234567010',
                'submitted_at' => Carbon::now()->subDays(2),
                'officer_id' => $petugasRehsos?->id,
                'work_unit_id' => $rehsosUnit?->id,
                'status' => ServiceRequestStatus::InProcess,
                'is_priority' => true,
                'verification_result' => 'Laporan valid, ditemukan lansia terlantar atas nama Mbah Slamet (72 th) tanpa keluarga.',
                'assessment_notes' => 'Kondisi fisik lemah, tempat tinggal tidak layak dan sebatang kara. Butuh rujukan panti werdha.',
            ]
        );

        $this->seedDocuments($req11, $rehsosReqType, DocumentVerificationStatus::Valid);
        $this->logStatus($req11, null, ServiceRequestStatus::Submitted->value, 'Permohonan layanan rehsos didaftarkan Puskesos.', $operatorKanigoro?->id);
        $this->logStatus($req11, ServiceRequestStatus::Submitted->value, ServiceRequestStatus::InProcess->value, 'Didisposisi ke Bidang Rehsos untuk assessment.', $petugasRehsos?->id);

        // Disposisi dari Sekdin ke Bidang Rehsos
        Disposition::create([
            'dispositionable_type' => ServiceRequest::class,
            'dispositionable_id' => $req11->id,
            'from_user_id' => $kadis->id,
            'to_work_unit_id' => $rehsosUnit->id,
            'to_user_id' => $petugasRehsos?->id,
            'instructions' => 'Segera terjunkan pekerja sosial untuk penjangkauan dan assessment lansia terlantar.',
            'disposed_at' => Carbon::now()->subDays(2),
        ]);

        // -------------------------------------------------------------
        // CASE 12: Layanan Lain - Rekomendasi Bansos (REK_BANSOS)
        // -------------------------------------------------------------
        $req12 = ServiceRequest::updateOrCreate(
            ['request_number' => 'BANSOS-202609-00001'],
            [
                'service_type_id' => $bansosType->id,
                'submitter_id' => $pemohon1?->id,
                'applicant_name' => 'Wartini',
                'applicant_nik' => '3505036109720011',
                'family_card_number' => '3505031102040019',
                'address' => 'RT 03 RW 01, Desa Satreyan, Kanigoro',
                'village_id' => $satreyan->id,
                'phone' => '081299887011',
                'submitted_at' => Carbon::now()->subDays(5),
                'officer_id' => User::where('email', 'petugas.pengaduan@blitarkab.go.id')->first()?->id,
                'work_unit_id' => $dayasosUnit?->id,
                'status' => ServiceRequestStatus::Completed,
                'is_priority' => false,
                'verification_result' => 'Surat Keterangan Tidak Mampu Desa valid dan terverifikasi di DTKS.',
                'service_result' => 'Surat Rekomendasi Bantuan Sosial Tidak Terencana Nomor 460/204/409.106/2026 telah terbit.',
                'completed_at' => Carbon::now()->subDays(3),
            ]
        );

        $this->seedDocuments($req12, $bansosType, DocumentVerificationStatus::Valid);
        $this->logStatus($req12, null, ServiceRequestStatus::Submitted->value, 'Pengajuan masuk.', $pemohon1?->id);
        $this->logStatus($req12, ServiceRequestStatus::Submitted->value, ServiceRequestStatus::Completed->value, 'Rekomendasi bansos diterbitkan.', $petugasLinjamsos?->id);
    }

    private function seedDocuments(ServiceRequest $request, ServiceType $type, DocumentVerificationStatus $status): void
    {
        foreach ($type->requirements as $req) {
            $ext = str_contains($req->allowed_mimes, 'pdf') ? 'pdf' : 'jpg';
            $safeSlug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $req->name));

            ServiceRequestDocument::updateOrCreate(
                [
                    'service_request_id' => $request->id,
                    'service_requirement_id' => $req->id,
                ],
                [
                    'file_path' => "documents/requests/{$request->request_number}/{$safeSlug}.{$ext}",
                    'original_name' => "{$safeSlug}_lampiran.{$ext}",
                    'verification_status' => $status->value,
                    'notes' => $status === DocumentVerificationStatus::Valid ? 'Dokumen jelas dan terbaca sesuai data kependudukan.' : null,
                ]
            );
        }
    }

    private function logStatus(ServiceRequest $request, ?string $from, string $to, string $notes, ?int $userId): void
    {
        StatusHistory::create([
            'statusable_type' => ServiceRequest::class,
            'statusable_id' => $request->id,
            'from_status' => $from,
            'to_status' => $to,
            'notes' => $notes,
            'user_id' => $userId,
            'created_at' => Carbon::now()->subMinutes(rand(10, 180)),
        ]);
    }
}
