<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $sekretariat = WorkUnit::where('name', 'Sekretariat Dinas Sosial')->first();
        $linjamsos = WorkUnit::where('name', 'like', '%Perlindungan dan Jaminan Sosial%')->first();
        $rehsos = WorkUnit::where('name', 'like', '%Rehabilitasi Sosial%')->first();
        $dayasos = WorkUnit::where('name', 'like', '%Pemberdayaan Sosial%')->first();

        $kanigoro = District::where('name', 'Kanigoro')->first();
        $wlingi = District::where('name', 'Wlingi')->first();
        $sutojayan = District::where('name', 'Sutojayan')->first();

        $satreyan = Village::where('name', 'Satreyan')->first();
        $beru = Village::where('name', 'Beru')->first();
        $kalipang = Village::where('name', 'Kalipang')->first();

        $users = [
            // 1. Administrator
            [
                'name' => "Ahmad Mu'amar Muzakki",
                'email' => 'admin@blitarkab.go.id',
                'password' => $password,
                'phone' => '081234567001',
                'nik' => '3505031204920001',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            // 2. Pimpinan / Kepala Dinas (Juga Pejabat Penandatangan Akhir)
            [
                'name' => 'Drs. H. Bambang Hermanto, M.Si',
                'email' => 'kadis@blitarkab.go.id',
                'password' => $password,
                'phone' => '081234567002',
                'nik' => '3505031005700001',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            // 3. Sekretaris Dinas (Pimpinan)
            [
                'name' => 'Dra. Endang Purwaningsih, MM',
                'email' => 'sekdin@blitarkab.go.id',
                'password' => $password,
                'phone' => '081234567003',
                'nik' => '3505031508720001',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            // 4. Pejabat Penandatangan - Kabid Linjamsos (Paraf Tahap 1)
            [
                'name' => 'Dr. Hendro Siswanto, S.Sos, M.Si',
                'email' => 'kabid.linjamsos@blitarkab.go.id',
                'password' => $password,
                'phone' => '081234567004',
                'nik' => '3505032001750001',
                'work_unit_id' => $linjamsos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            // 5. Pejabat Penandatangan - Kabid Rehsos
            [
                'name' => 'Nurul Hidayati, S.ST, M.PS.Sp',
                'email' => 'kabid.rehsos@blitarkab.go.id',
                'password' => $password,
                'phone' => '081234567005',
                'nik' => '3505032509780001',
                'work_unit_id' => $rehsos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            // 6. Kabid Dayasos PFM
            [
                'name' => 'Supriyadi, S.Sos',
                'email' => 'kabid.dayasos@blitarkab.go.id',
                'password' => $password,
                'phone' => '081234567006',
                'nik' => '3505031403760001',
                'work_unit_id' => $dayasos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            // 7. Petugas Pelayanan Linjamsos (Verifikator DTSEN & PBI-JK)
            [
                'name' => 'Rina Wijayanti, S.Tr.Sos',
                'email' => 'petugas.linjamsos@blitarkab.go.id',
                'password' => $password,
                'phone' => '081234567007',
                'nik' => '3505034506890001',
                'work_unit_id' => $linjamsos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            // 8. Petugas Rehsos (Pekerja Sosial)
            [
                'name' => 'Dimas Prasetyo, S.Sos (Peksos)',
                'email' => 'petugas.rehsos@blitarkab.go.id',
                'password' => $password,
                'phone' => '081234567008',
                'nik' => '3505031102910001',
                'work_unit_id' => $rehsos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            // 9. Petugas Pengaduan & Informasi
            [
                'name' => 'Fajar Nugroho, A.Md',
                'email' => 'petugas.pengaduan@blitarkab.go.id',
                'password' => $password,
                'phone' => '081234567009',
                'nik' => '3505031807950001',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
            ],
            // 10. Operator Kecamatan Kanigoro
            [
                'name' => 'Bagus Wicaksono (Puskesos Kanigoro)',
                'email' => 'operator.kanigoro@blitarkab.go.id',
                'password' => $password,
                'phone' => '081234567010',
                'nik' => '3505032108930001',
                'work_unit_id' => null,
                'district_id' => $kanigoro?->id,
                'village_id' => null,
                'is_active' => true,
            ],
            // 11. Operator Desa Satreyan (Kanigoro)
            [
                'name' => 'Anisa Rahmawati (Operator Satreyan)',
                'email' => 'operator.satreyan@blitarkab.go.id',
                'password' => $password,
                'phone' => '081234567011',
                'nik' => '3505036209940001',
                'work_unit_id' => null,
                'district_id' => $kanigoro?->id,
                'village_id' => $satreyan?->id,
                'is_active' => true,
            ],
            // 12. Operator Kecamatan Wlingi
            [
                'name' => 'Tri Wulandari (Puskesos Wlingi)',
                'email' => 'operator.wlingi@blitarkab.go.id',
                'password' => $password,
                'phone' => '081234567012',
                'nik' => '3505105210920001',
                'work_unit_id' => null,
                'district_id' => $wlingi?->id,
                'village_id' => $beru?->id,
                'is_active' => true,
            ],
            // 13. Masyarakat Pemohon 1
            [
                'name' => 'Siti Aminah',
                'email' => 'pemohon.dtsen@gmail.com',
                'password' => $password,
                'phone' => '085712345001',
                'nik' => '3505035504850002',
                'work_unit_id' => null,
                'district_id' => $kanigoro?->id,
                'village_id' => $satreyan?->id,
                'is_active' => true,
            ],
            // 14. Masyarakat Pemohon 2
            [
                'name' => 'Budi Santoso',
                'email' => 'pemohon.pbi@gmail.com',
                'password' => $password,
                'phone' => '085712345002',
                'nik' => '3505101407880003',
                'work_unit_id' => null,
                'district_id' => $wlingi?->id,
                'village_id' => $beru?->id,
                'is_active' => true,
            ],
            // 15. Masyarakat Pelapor Pengaduan
            [
                'name' => 'Rahmat Hidayat',
                'email' => 'pelapor.sosial@gmail.com',
                'password' => $password,
                'phone' => '085712345003',
                'nik' => '3505051909800004',
                'work_unit_id' => null,
                'district_id' => $sutojayan?->id,
                'village_id' => $kalipang?->id,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
