<?php

namespace Database\Seeders;

use App\Enums\ComplaintAttachmentType;
use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\Disposition;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catPpks = ComplaintCategory::where('name', 'like', '%Pemerlu Pelayanan%')->first();
        $catBansos = ComplaintCategory::where('name', 'like', '%Ketidaktepatan Sasaran%')->first();
        $catOdgj = ComplaintCategory::where('name', 'like', '%Gangguan Ketertiban Sosial & ODGJ%')->first();
        $catKis = ComplaintCategory::where('name', 'like', '%Permasalahan Kepesertaan BPJS%')->first();

        $satreyan = Village::where('name', 'Satreyan')->first();
        $beru = Village::where('name', 'Beru')->first();
        $kalipang = Village::where('name', 'Kalipang')->first();

        $reporter = User::where('email', 'pelapor.sosial@gmail.com')->first();
        $petugasPengaduan = User::where('email', 'petugas.pengaduan@blitarkab.go.id')->first();
        $petugasRehsos = User::where('email', 'petugas.rehsos@blitarkab.go.id')->first();
        $sekretariatUnit = WorkUnit::where('name', 'Sekretariat Dinas Sosial')->first();
        $rehsosUnit = WorkUnit::where('name', 'like', '%Rehabilitasi Sosial%')->first();

        // -------------------------------------------------------------
        // COMPLAINT 1: Temuan ODGJ Terlantar (Dalam Penanganan / In Handling)
        // -------------------------------------------------------------
        $c1 = Complaint::updateOrCreate(
            ['complaint_number' => 'ADU-202609-00001'],
            [
                'complaint_category_id' => $catOdgj->id,
                'reporter_id' => $reporter?->id,
                'reporter_name' => 'Rahmat Hidayat',
                'reporter_phone' => '085712345003',
                'location_detail' => 'Depan Ruko Pasar Beru, Jl. Gajah Mada No. 12',
                'village_id' => $beru->id,
                'description' => 'Ada orang dengan gangguan jiwa (ODGJ) laki-laki usia sekitar 35 tahun tanpa busana lengkap berkeliaran di area pertokoan dan meresahkan pedagang. Mohon segera dievakuasi.',
                'reported_at' => Carbon::now()->subDays(2),
                'officer_id' => $petugasRehsos?->id,
                'status' => ComplaintStatus::InHandling,
                'verification_result' => 'Laporan telah dikonfirmasi Babinsa dan Trantib Kecamatan Wlingi. Tim Rehsos Dinsos bersama Satpol PP diterjunkan ke lokasi.',
                'action_taken' => 'Tim piket Rehsos telah mengevakuasi klien ODGJ ke IGD RSUD Ngudi Waluyo Wlingi untuk penstabilan medis dan penelusuran identitas.',
            ]
        );

        $this->seedAttachment($c1, 'attachments/complaints/odgj_pasar_beru.jpg', ComplaintAttachmentType::Photo);
        $this->logStatus($c1, null, ComplaintStatus::Received->value, 'Pengaduan diterima sistem.', $reporter?->id);
        $this->logStatus($c1, ComplaintStatus::Received->value, ComplaintStatus::Dispatched->value, 'Didisposisi ke Bidang Rehsos.', $petugasPengaduan?->id);
        $this->logStatus($c1, ComplaintStatus::Dispatched->value, ComplaintStatus::InHandling->value, 'Tim pekerja sosial meluncur ke lokasi.', $petugasRehsos?->id);

        Disposition::create([
            'dispositionable_type' => Complaint::class,
            'dispositionable_id' => $c1->id,
            'from_user_id' => $petugasPengaduan->id,
            'to_work_unit_id' => $rehsosUnit->id,
            'to_user_id' => $petugasRehsos?->id,
            'instructions' => 'Segera koordinasi dengan TKSK dan aparat setempat untuk evakuasi humanis.',
            'disposed_at' => Carbon::now()->subDays(2),
        ]);

        // -------------------------------------------------------------
        // COMPLAINT 2: Pengaduan Duplikat dari Kasus 1 (Duplicate)
        // -------------------------------------------------------------
        $c2 = Complaint::updateOrCreate(
            ['complaint_number' => 'ADU-202609-00002'],
            [
                'complaint_category_id' => $catOdgj->id,
                'reporter_id' => null,
                'reporter_name' => 'Wahyudi (Warga Pasar)',
                'reporter_phone' => '081233887766',
                'location_detail' => 'Kawasan Pasar Beru Wlingi',
                'village_id' => $beru->id,
                'description' => 'Mohon bantuan dinas sosial, ada ODGJ terlantar di pasar Beru Wlingi.',
                'reported_at' => Carbon::now()->subDays(2)->addHours(2),
                'officer_id' => $petugasPengaduan?->id,
                'status' => ComplaintStatus::Duplicate,
                'verification_result' => 'Laporan serupa mengenai subjek dan lokasi yang sama sudah dalam penanganan pada tiket ADU-202609-00001.',
                'duplicate_of_id' => $c1->id,
            ]
        );

        $this->logStatus($c2, null, ComplaintStatus::Received->value, 'Laporan masuk.', null);
        $this->logStatus($c2, ComplaintStatus::Received->value, ComplaintStatus::Duplicate->value, 'Ditetapkan sebagai laporan duplikat.', $petugasPengaduan?->id);

        // -------------------------------------------------------------
        // COMPLAINT 3: Bansos Tidak Tepat Sasaran (Verification Awal)
        // -------------------------------------------------------------
        $c3 = Complaint::updateOrCreate(
            ['complaint_number' => 'ADU-202609-00003'],
            [
                'complaint_category_id' => $catBansos->id,
                'reporter_id' => $reporter?->id,
                'reporter_name' => 'Rahmat Hidayat',
                'reporter_phone' => '085712345003',
                'location_detail' => 'Dusun Krajan RT 02 RW 01, Satreyan',
                'village_id' => $satreyan->id,
                'description' => 'Ada warga pemilik kendaraan roda 4 dan rumah tingkat masih menerima bantuan PKH, sedangkan tetangga sebelah yang lansia janda miskin tidak mendapatkan bansos apapun.',
                'reported_at' => Carbon::now()->subDay(),
                'officer_id' => $petugasPengaduan?->id,
                'status' => ComplaintStatus::Verification,
                'verification_result' => 'Sedang dilakukan kroscek data kepesertaan DTKS bersama Pendamping PKH Kecamatan Kanigoro.',
            ]
        );

        $this->seedAttachment($c3, 'attachments/complaints/foto_rumah_pkh.jpg', ComplaintAttachmentType::Photo);
        $this->logStatus($c3, null, ComplaintStatus::Received->value, 'Laporan diterima.', $reporter?->id);
        $this->logStatus($c3, ComplaintStatus::Received->value, ComplaintStatus::Verification->value, 'Pemeriksaan awal bukti laporan.', $petugasPengaduan?->id);

        // -------------------------------------------------------------
        // COMPLAINT 4: Permasalahan BPJS PBI Nonaktif (Selesai Ditangani / Resolved)
        // -------------------------------------------------------------
        $c4 = Complaint::updateOrCreate(
            ['complaint_number' => 'ADU-202609-00004'],
            [
                'complaint_category_id' => $catKis->id,
                'reporter_id' => null,
                'reporter_name' => 'Sri Rahayu',
                'reporter_phone' => '087855443322',
                'location_detail' => 'RT 03 RW 02, Kalipang Sutojayan',
                'village_id' => $kalipang->id,
                'description' => 'Kartu KIS anak saya tiba-tiba tidak aktif saat mau dipakai rujukan operasi amandel di RSUD. Kami keluarga tidak mampu dan bingung biaya.',
                'reported_at' => Carbon::now()->subDays(5),
                'officer_id' => $petugasPengaduan?->id,
                'status' => ComplaintStatus::Resolved,
                'verification_result' => 'Verifikasi desil memenuhi syarat reaktivasi PBI-JK.',
                'action_taken' => 'Pelapor telah dibimbing dan dibuatkan tiket Reaktivasi KIS PBI Prioritas (Tiket: PBI-202609-00003). Surat rekomendasi telah diterbitkan dan telah berkoordinasi dengan loket BPJS RSUD.',
                'resolved_at' => Carbon::now()->subDays(3),
            ]
        );

        $this->seedAttachment($c4, 'attachments/complaints/kartu_kis_nonaktif.pdf', ComplaintAttachmentType::Document);
        $this->logStatus($c4, null, ComplaintStatus::Received->value, 'Laporan masuk.', null);
        $this->logStatus($c4, ComplaintStatus::Received->value, ComplaintStatus::InHandling->value, 'Diproses reaktivasi.', $petugasPengaduan?->id);
        $this->logStatus($c4, ComplaintStatus::InHandling->value, ComplaintStatus::Resolved->value, 'Rekomendasi terbit dan solusi diberikan.', $petugasPengaduan?->id);
    }

    private function seedAttachment(Complaint $complaint, string $path, ComplaintAttachmentType $type): void
    {
        ComplaintAttachment::updateOrCreate(
            [
                'complaint_id' => $complaint->id,
                'file_path' => $path,
            ],
            [
                'type' => $type->value,
            ]
        );
    }

    private function logStatus(Complaint $complaint, ?string $from, string $to, string $notes, ?int $userId): void
    {
        StatusHistory::create([
            'statusable_type' => Complaint::class,
            'statusable_id' => $complaint->id,
            'from_status' => $from,
            'to_status' => $to,
            'notes' => $notes,
            'user_id' => $userId,
            'created_at' => Carbon::now()->subMinutes(rand(15, 240)),
        ]);
    }
}
