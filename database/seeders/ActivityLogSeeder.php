<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\DtsenCertificate;
use App\Models\ServiceRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@blitarkab.go.id')->first();
        $kadis = User::where('email', 'kadis@blitarkab.go.id')->first();
        $petugas = User::where('email', 'petugas.linjamsos@blitarkab.go.id')->first();

        $completedRequest = ServiceRequest::where('request_number', 'DTSEN-202609-00004')->first();
        $completedCert = DtsenCertificate::where('certificate_number', '400.9/042/409.106/2026')->first();

        $logs = [
            [
                'log_name' => 'auth',
                'description' => 'Pengguna login ke dalam sistem SAPA SOSIAL',
                'subject_type' => User::class,
                'subject_id' => $admin?->id ?? 1,
                'event' => 'login',
                'causer_type' => User::class,
                'causer_id' => $admin?->id ?? 1,
                'properties' => ['ip' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'],
                'batch_uuid' => (string) Str::uuid(),
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'log_name' => 'service_request',
                'description' => 'Verifikasi berkas dan pengecekan SIKS-NG pengajuan DTSEN-202609-00004',
                'subject_type' => ServiceRequest::class,
                'subject_id' => $completedRequest?->id ?? 1,
                'event' => 'verified',
                'causer_type' => User::class,
                'causer_id' => $petugas?->id ?? 1,
                'properties' => ['decile' => 1, 'is_registered' => true],
                'batch_uuid' => (string) Str::uuid(),
                'created_at' => Carbon::now()->subDays(2)->subHours(5),
            ],
            [
                'log_name' => 'certificate_issuance',
                'description' => 'Persetujuan dan penerbitan Surat Keterangan DTSEN Nomor 400.9/042/409.106/2026',
                'subject_type' => DtsenCertificate::class,
                'subject_id' => $completedCert?->id ?? 1,
                'event' => 'approved',
                'causer_type' => User::class,
                'causer_id' => $kadis?->id ?? 1,
                'properties' => ['certificate_number' => '400.9/042/409.106/2026', 'verification_code' => 'DTSEN-202609-V88912'],
                'batch_uuid' => (string) Str::uuid(),
                'created_at' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create($log);
        }
    }
}
