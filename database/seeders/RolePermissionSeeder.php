<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 6 Roles
        $roles = [
            'administrator' => 'Administrator Sistem Dinsos',
            'petugas_dinsos' => 'Petugas Pelayanan / Peksos / Linjamsos',
            'pejabat_penandatangan' => 'Pejabat Penandatangan (Kadis / Kabid)',
            'pimpinan' => 'Pimpinan Dinas Sosial',
            'operator_wilayah' => 'Operator Puskesos Kecamatan / Desa',
            'masyarakat' => 'Pemohon / Pelapor Masyarakat',
        ];

        foreach ($roles as $roleName => $displayName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Assign roles to existing seeded users
        $roleAssignments = [
            'admin@blitarkab.go.id' => ['administrator'],
            'kadis@blitarkab.go.id' => ['pimpinan', 'pejabat_penandatangan'],
            'sekdin@blitarkab.go.id' => ['pimpinan'],
            'kabid.linjamsos@blitarkab.go.id' => ['pejabat_penandatangan'],
            'kabid.rehsos@blitarkab.go.id' => ['pejabat_penandatangan'],
            'kabid.dayasos@blitarkab.go.id' => ['pimpinan'],
            'petugas.linjamsos@blitarkab.go.id' => ['petugas_dinsos'],
            'petugas.rehsos@blitarkab.go.id' => ['petugas_dinsos'],
            'petugas.pengaduan@blitarkab.go.id' => ['petugas_dinsos'],
            'operator.kanigoro@blitarkab.go.id' => ['operator_wilayah'],
            'operator.satreyan@blitarkab.go.id' => ['operator_wilayah'],
            'operator.wlingi@blitarkab.go.id' => ['operator_wilayah'],
            'pemohon.dtsen@gmail.com' => ['masyarakat'],
            'pemohon.pbi@gmail.com' => ['masyarakat'],
            'pelapor.sosial@gmail.com' => ['masyarakat'],
        ];

        foreach ($roleAssignments as $email => $assignedRoles) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->syncRoles($assignedRoles);
            }
        }
    }
}
