# 🚀 Rencana Aksi — Dashboard SAPA SOSIAL (Filament v5)

> **Proyek:** SAPA SOSIAL — Satu Pintu Layanan Sosial Kabupaten Blitar
> **Stack:** Laravel 13 · Filament v5.8 · Livewire v4 · PostgreSQL · Tailwind CSS v4
> **Tanggal:** 25 September 2026

---

## 📊 Status Proyek Saat Ini

| Komponen | Status | Keterangan |
|----------|--------|------------|
| Migrasi database | ✅ Selesai | 34 migration file, seluruh tabel PRD |
| Model Eloquent | ✅ Selesai | 31 model dengan relasi lengkap |
| Enum PHP | ✅ Selesai | 14 enum untuk status & tipe |
| Factory & Seeder | ⚠️ Minimal | Hanya `UserFactory`, belum ada seeder |
| spatie/laravel-permission | ❌ Belum | Belum ada di `composer.json` |
| spatie/laravel-activitylog | ❌ Belum | Tabel `activity_log` sudah ada tapi paket belum terpasang |
| Filament Resources | ❌ Belum | Direktori `app/Filament` kosong |
| Filament Widgets | ❌ Belum | Belum ada widget dashboard |
| Policy & Authorization | ❌ Belum | Belum ada Policy class |
| Portal Publik | ❌ Belum | Livewire components belum dibuat |
| PDF Generation | ❌ Belum | Paket belum dipilih/dipasang |
| QR Code | ❌ Belum | Paket belum dipilih/dipasang |

---

## 🏗️ Fase Pembangunan

### Fase 0 — Fondasi & Dependensi *(Prasyarat)*

> [!IMPORTANT]
> Fase ini harus selesai sebelum mulai membangun Filament resources.

#### 0.1 — Instal Paket Pendukung
```bash
composer require spatie/laravel-permission
composer require spatie/laravel-activitylog
composer require barryvdh/laravel-dompdf
composer require simplesoftwareio/simple-qrcode
```

#### 0.2 — Setup Roles & Permissions
Jalankan migrasi spatie, lalu buat seeder untuk 6 role:

| Role | Slug | Keterangan |
|------|------|------------|
| Administrator | `administrator` | Full akses, kelola master data & pengguna |
| Petugas Dinsos | `petugas_dinsos` | Verifikasi, disposisi, proses pengajuan/pengaduan/rehsos |
| Pejabat Penandatangan | `pejabat_penandatangan` | Paraf & tanda tangan surat |
| Pimpinan | `pimpinan` | Dashboard & laporan (read-only) |
| Operator Kecamatan/Desa | `operator_wilayah` | CRUD di wilayahnya saja |
| Masyarakat | `masyarakat` | Portal publik, data sendiri |

#### 0.3 — Tambahkan Trait Spatie ke Model User
- Tambah `HasRoles` trait ke `User` model
- Tambah `LogsActivity` ke model-model transaksi utama

#### 0.4 — Buat Factory & Seeder Lengkap
Buat factory untuk setiap model, lalu seeder master data:

| Seeder | Data |
|--------|------|
| `RolePermissionSeeder` | 6 role + permissions per modul |
| `WorkUnitSeeder` | Unit kerja Dinsos |
| `DistrictVillageSeeder` | Kecamatan & desa Kab. Blitar |
| `ServiceTypeSeeder` | 4 jenis layanan (DTSEN, PBI, REHSOS, umum) + persyaratan |
| `DtsenPurposeSeeder` | Tujuan penggunaan SK DTSEN (SPMB, PIP, KIP, dll.) |
| `ClientCategorySeeder` | Kategori klien rehabilitasi |
| `ComplaintCategorySeeder` | Kategori pengaduan |
| `ReferralInstitutionSeeder` | Lembaga rujukan |
| `DemoUserSeeder` | User demo per role |

**Deliverable:** `php artisan db:seed` berjalan tanpa error, semua master data terisi.

---

### Fase 1 — Panel Admin & Master Data *(Minggu 1–2)*

> Modul administrasi dasar yang menjadi fondasi seluruh layanan.

#### 1.1 — Konfigurasi Panel Admin
- [ ] Update `AdminPanelProvider`:
  - Navigation groups: **Layanan**, **Rehabilitasi Sosial**, **Pengaduan**, **Informasi Publik**, **Master Data**, **Pengguna & Akses**, **Laporan**
  - Branding: Logo, nama "SAPA SOSIAL", warna tema Dinsos
  - `->sidebarCollapsibleOnDesktop()`
  - `->globalSearchKeyBindings(['command+k', 'ctrl+k'])`
  - `->databaseNotifications()`

#### 1.2 — Resource Master Data

| Resource | Model | Group | Fitur Utama |
|----------|-------|-------|-------------|
| `WorkUnitResource` | WorkUnit | Master Data | CRUD, toggle aktif |
| `DistrictResource` | District | Master Data | CRUD, relasi ke villages |
| `VillageResource` | Village | Master Data | CRUD, filter per district |
| `ServiceTypeResource` | ServiceType | Master Data | CRUD, relasi requirements, toggle aktif |
| `ServiceRequirementResource` | — | *Inline di ServiceType* | Repeater di form ServiceType |
| `DtsenPurposeResource` | DtsenPurpose | Master Data | CRUD, batas desil, masa berlaku |
| `ClientCategoryResource` | ClientCategory | Master Data | CRUD sederhana |
| `ComplaintCategoryResource` | ComplaintCategory | Master Data | CRUD, toggle aktif |
| `ReferralInstitutionResource` | ReferralInstitution | Master Data | CRUD, tipe, toggle aktif |

#### 1.3 — Resource Pengguna & Akses

| Resource | Model | Fitur Utama |
|----------|-------|-------------|
| `UserResource` | User | CRUD, assign role, filter per unit/wilayah, toggle aktif |

**Deliverable:** Seluruh master data dapat dikelola via panel admin.

---

### Fase 2 — Layanan 1: SK DTSEN *(Minggu 2–3)* ⭐

> Layanan prioritas pertama — alur paling lengkap dengan persetujuan berjenjang.

#### 2.1 — ServiceRequestResource (Shared)
Resource utama untuk pengajuan layanan (dipakai bersama Layanan 1, 2, 4):

- **Table columns:** Nomor tiket, jenis layanan, nama pemohon, NIK, desa, status (badge warna), tanggal, petugas, prioritas
- **Filters:** Status, jenis layanan, kecamatan, desa, periode, petugas
- **Tabs:** Semua | Baru | Dalam Proses | Selesai | Ditolak *(filter berdasarkan `service_types.handler`)*
- **Global search:** Nomor tiket, nama pemohon, NIK

#### 2.2 — DtsenCertificateResource
Resource khusus detail SK DTSEN, **atau** sebagai Relation Manager di ServiceRequestResource:

| Halaman/Action | Fungsi |
|----------------|--------|
| **Create** | Form pengajuan: data pemohon + orang diterangkan + tujuan + upload KTP/KK |
| **View** | Infolist detail pengajuan + status timeline + dokumen |
| **Edit** | Edit data (hanya petugas, sebelum issued) |
| **Action: Periksa Berkas** | Verifikasi dokumen per file (valid/revisi) → status `document_check` |
| **Action: Minta Perbaikan** | Status → `revision_requested`, kirim catatan |
| **Action: Cek SIKS-NG** | Form: terdaftar/tidak, desil, tanggal cek → `data_verification` |
| **Action: Buat Draf Surat** | Validasi desil ≤ batas, generate draf → `awaiting_approval` |
| **Action: Paraf (Kabid)** | Step 1 approval → catat di `approvals` |
| **Action: Tandatangani (Kadis)** | Step 2 approval → generate PDF + QR → `issued` |
| **Action: Tolak** | Catat alasan → `rejected` |
| **Action: Selesaikan** | Status → `completed` |

#### 2.3 — Business Logic
- `App\Actions\Dtsen\CheckDecileEligibility` — validasi desil vs batas tujuan
- `App\Actions\Dtsen\GenerateCertificatePdf` — generate PDF dari template + QR code
- `App\Actions\Dtsen\GenerateVerificationCode` — buat kode verifikasi unik
- `App\Actions\ServiceRequest\GenerateTicketNumber` — pakai `NumberSequence` model
- `App\Actions\ServiceRequest\TransitionStatus` — catat status_histories otomatis
- Duplikasi check: warning jika pemohon+tujuan sama & surat masih berlaku

#### 2.4 — Relation Managers
- `DocumentsRelationManager` — kelola dokumen persyaratan
- `StatusHistoriesRelationManager` — timeline riwayat status (read-only)
- `ApprovalsRelationManager` — riwayat paraf/persetujuan
- `DispositionsRelationManager` — riwayat disposisi

**Deliverable:** Alur lengkap SK DTSEN dari pengajuan → verifikasi → persetujuan berjenjang → terbit → selesai, termasuk generate PDF + QR.

---

### Fase 3 — Layanan 2: Reaktivasi PBI-JK *(Minggu 3–4)* ⭐

#### 3.1 — PbiReactivationResource
Menggunakan `ServiceRequestResource` yang sama, dengan form/view tambahan:

| Action | Fungsi |
|--------|--------|
| **Pengajuan** | Data peserta + BPJS + alasan + upload dokumen |
| **Verifikasi Kelayakan** | Cek desil, catat eligibility → `eligibility_verification` |
| **Buat Rekomendasi** | Generate surat rekomendasi → approval → `recommendation_issued` |
| **Input SIKS-NG** | Catat tanggal pengusulan → `proposed_to_ministry` |
| **Keputusan Kemensos** | approved/rejected → `ministry_approved` / `ministry_rejected` |
| **Konfirmasi Aktif** | Catat tanggal aktif kembali → `reactivated` → `completed` |

#### 3.2 — Business Logic
- Alasan darurat medis → otomatis `is_priority = true`, tampil paling atas
- Wajib lampirkan surat faskes untuk alasan medis
- Deteksi pengajuan tertahan > N hari di `proposed_to_ministry` → badge peringatan
- Approval flow serupa SK DTSEN (paraf → tanda tangan)

**Deliverable:** Alur lengkap reaktivasi PBI dari pengajuan → rekomendasi → usulan Kemensos → aktif kembali.

---

### Fase 4 — Layanan 3: Rehabilitasi Sosial *(Minggu 4–5)* ⭐

#### 4.1 — ClientResource
- CRUD klien rehabilitasi
- Field sensitif: NIK, alamat → hanya petugas yang ditugaskan
- Filter: kategori, desa, kecamatan

#### 4.2 — RehabilitationCaseResource

| Halaman/Action | Fungsi |
|----------------|--------|
| **Create** | Buat kasus baru, pilih/buat klien, hubungkan sumber (pengajuan/pengaduan) |
| **View** | Infolist + timeline penanganan lengkap |
| **Action: Assessment** | Form assessment → catat kebutuhan + rekomendasi |
| **Action: Rencana Pelayanan** | Tentukan jenis penanganan (langsung/rujukan/keduanya) |
| **Action: Buat Rujukan** | Form rujukan → pilih lembaga tujuan → `referrals` |
| **Action: Update Rujukan** | Ubah status rujukan (diterima/dalam pelayanan/selesai) |
| **Action: Monitoring** | Catat perkembangan pelayanan |
| **Action: Tutup Kasus** | Validasi: hasil penanganan harus terisi → `closed` |

#### 4.3 — Relation Managers
- `AssessmentsRelationManager` — riwayat assessment
- `ReferralsRelationManager` — daftar & status rujukan
- `MonitoringRecordsRelationManager` — catatan monitoring

**Deliverable:** Alur lengkap kasus rehabilitasi dari penerimaan → assessment → pelayanan/rujukan → monitoring → tutup.

---

### Fase 5 — Layanan 4 & 5: Pengajuan Umum & Pengaduan *(Minggu 5–6)*

#### 5.1 — Pengajuan Layanan Umum (Layanan 4)
Menggunakan `ServiceRequestResource` yang sudah ada, dengan:
- Form dinamis sesuai `service_type` (persyaratan tampil sesuai jenis)
- Opsional assessment step
- Link ke Layanan 3 jika perlu rehabilitasi

#### 5.2 — ComplaintResource (Layanan 5)

| Fitur | Keterangan |
|-------|------------|
| **Table** | Nomor laporan, kategori, lokasi, status badge, tanggal, petugas |
| **Create** | Kategori + lokasi (desa/kec) + deskripsi + upload lampiran |
| **Actions** | Verifikasi → Minta Klarifikasi → Disposisi → Tangani → Selesaikan |
| **Duplikasi** | Tandai duplikat, gabungkan ke laporan induk |
| **Eskalasi** | Buat kasus rehabilitasi dari pengaduan |

#### 5.3 — Relation Managers
- `ComplaintAttachmentsRelationManager`
- `StatusHistoriesRelationManager`
- `DispositionsRelationManager`

**Deliverable:** Pengajuan umum dinamis + pengaduan lengkap dengan disposisi & eskalasi ke rehsos.

---

### Fase 6 — Dashboard & Widget *(Minggu 6–7)*

> [!IMPORTANT]
> Bagian ini menjawab kebutuhan Bagian 3 PRD. Semua widget harus mendukung filter periode, jenis layanan, status, kecamatan, dan desa.

#### 6.1 — Widget Stats Overview

| Widget | Tipe | Data |
|--------|------|------|
| `DtsenIssuedChart` | Chart (bar) | SK DTSEN terbit per tujuan & desil |
| `DtsenAwaitingSignature` | Stats | Jumlah draf menunggu paraf/tanda tangan |
| `PbiByStageChart` | Chart (bar) | Reaktivasi per tahap status |
| `PbiEmergencyWidget` | Table | Pengajuan darurat medis belum selesai |
| `PbiStalledWidget` | Stats | Tertahan di `proposed_to_ministry` > N hari |
| `ActiveRehabCasesChart` | Chart (doughnut) | Kasus aktif per status |
| `ReferralsByInstitution` | Chart (bar) | Rujukan per lembaga tujuan |
| `IncomingRequestsWidget` | Stats | Pengajuan & pengaduan baru hari ini / minggu ini |
| `ProcessVsCompletedChart` | Chart (pie) | Dalam proses vs selesai |
| `RegionalDistribution` | Chart (bar) | Layanan & pengaduan per kecamatan |
| `PopularInfoWidget` | Table *(opsional)* | Konten paling sering diakses |

#### 6.2 — Filter Dashboard Global
- Custom Filament Dashboard page dengan filter form:
  - Periode (date range)
  - Jenis Layanan (select)
  - Status (select)
  - Kecamatan (select, cascading)
  - Desa/Kelurahan (select, cascading dari kecamatan)
- Operator Kecamatan/Desa → filter otomatis sesuai wilayah

#### 6.3 — Scope Data per Role

| Role | Data yang Tampil |
|------|------------------|
| Administrator | Semua data |
| Petugas Dinsos | Semua data layanan (sesuai unit kerjanya) |
| Pejabat Penandatangan | Antrean persetujuan + ringkasan |
| Pimpinan | Seluruh dashboard (read-only) |
| Operator Wilayah | Hanya data wilayahnya |

**Deliverable:** Dashboard interaktif dengan 10+ widget, filter global, dan scope per role.

---

### Fase 7 — Laporan & Ekspor *(Minggu 7–8)*

#### 7.1 — Halaman Laporan di Panel Admin

| Laporan | Fitur |
|---------|-------|
| Rekap SK DTSEN | Filter periode, tujuan, desil, wilayah → tabel + ekspor Excel/PDF |
| Rekap Reaktivasi PBI-JK | Filter periode, alasan, status, wilayah → tabel + ekspor |
| Laporan Rehabilitasi Sosial | Filter periode, kategori, lembaga, status → tabel + ekspor |
| Laporan Pelayanan (semua jenis) | Filter jenis, status, periode, wilayah → tabel + ekspor |
| Laporan Pengaduan | Filter kategori, status, wilayah, periode → tabel + ekspor |

#### 7.2 — Implementasi Teknis
- Gunakan Filament Table Export Action (bawaan Filament v5) untuk Excel
- PDF: gunakan `barryvdh/laravel-dompdf` dengan template Blade
- Laporan besar → dispatch ke Queue, kirim notifikasi setelah selesai
- Format laporan: header Dinsos, tabel ringkasan, tabel detail

**Deliverable:** 5 jenis laporan berkala, dapat ditampilkan di layar, dicetak, dan diekspor Excel/PDF.

---

### Fase 8 — Layanan 6: Informasi Publik & Portal *(Minggu 8–9)*

#### 8.1 — Resource CMS (Panel Admin)

| Resource | Fitur |
|----------|-------|
| `InformationPageResource` | CRUD konten, rich editor, status draft/published/archived |
| `DownloadableFormResource` | Upload formulir berversi, toggle versi aktif |
| `FaqResource` | CRUD FAQ, sortable, toggle aktif |

#### 8.2 — Portal Publik (Livewire v4 Full-Page Components)

| Halaman | Route | Komponen |
|---------|-------|----------|
| Beranda | `/` | Hero + daftar layanan + pencarian |
| Daftar Layanan | `/layanan` | Grid kartu layanan |
| Detail Layanan | `/layanan/{slug}` | Info lengkap + persyaratan + alur + formulir unduhan |
| FAQ | `/faq` | Accordion FAQ, filter per layanan |
| Pengajuan Layanan | `/pengajuan/{service_type}` | Form multi-step Livewire |
| Pengaduan | `/pengaduan` | Form pengaduan |
| Cek Status | `/cek-status` | Input nomor tiket + 4 digit NIK/HP → timeline status |
| Verifikasi SK | `/verifikasi/{code}` | Cek keaslian SK DTSEN via kode/QR |
| Akun Saya | `/akun` | Riwayat pengajuan & pengaduan (login masyarakat) |

#### 8.3 — Desain Portal
- Responsive (mobile-first), Tailwind CSS v4
- Branding Dinsos Kabupaten Blitar
- Tanpa login untuk informasi, cek status, dan verifikasi
- Login opsional untuk akun masyarakat

**Deliverable:** Portal publik lengkap + CMS di panel admin.

---

### Fase 9 — Authorization & Policy *(Paralel, mulai Fase 1)*

> [!WARNING]
> Policy harus diterapkan sejak awal, bukan ditambahkan di akhir. Setiap resource harus memiliki Policy.

#### 9.1 — Policies

| Policy | Model | Aturan Utama |
|--------|-------|--------------|
| `ServiceRequestPolicy` | ServiceRequest | Operator hanya wilayahnya, masyarakat hanya miliknya |
| `DtsenCertificatePolicy` | DtsenCertificate | Approval hanya pejabat, issue hanya petugas |
| `PbiReactivationPolicy` | PbiReactivation | Serupa DTSEN |
| `RehabilitationCasePolicy` | RehabilitationCase | Data sensitif, hanya petugas ditugaskan + admin |
| `ClientPolicy` | Client | Data sensitif |
| `ComplaintPolicy` | Complaint | Operator hanya wilayahnya |
| `InformationPagePolicy` | InformationPage | Hanya pengelola & admin |
| `UserPolicy` | User | Hanya admin |
| `MasterDataPolicy` | *Shared* | Hanya admin |

#### 9.2 — Scope Global
- Operator Kecamatan/Desa → scope otomatis `village_id`/`district_id`
- Masyarakat → scope `submitter_id = auth()->id()`
- Pimpinan → read-only, semua `canCreate/canEdit/canDelete = false`

---

### Fase 10 — Queue, Notifikasi & Finishing *(Minggu 9–10)*

#### 10.1 — Queue Jobs
- `GenerateDtsenPdfJob` — generate PDF SK DTSEN
- `GeneratePbiRecommendationPdfJob` — generate PDF rekomendasi
- `ExportReportJob` — ekspor laporan besar
- `FlagStalledTicketsJob` — tandai tiket tertahan (scheduled command)

#### 10.2 — Filament Notifications
- Notifikasi database saat:
  - Pengajuan baru masuk (→ petugas)
  - Status berubah (→ pemohon / petugas terkait)
  - Dokumen perlu revisi (→ pemohon)
  - Menunggu persetujuan (→ pejabat penandatangan)
  - Tiket tertahan (→ petugas)

#### 10.3 — Audit Log
- `LogsActivity` trait pada model transaksi
- Activity log viewer di panel admin (khusus Administrator)

#### 10.4 — Testing
- Feature test per resource (CRUD + actions + policies)
- Unit test untuk business logic (desil check, nomor tiket, status transition)
- Test factories untuk semua model

**Deliverable:** Background jobs, notifikasi real-time, audit trail, dan test coverage.

---

## 📐 Arsitektur Direktori Target

```
app/
├── Actions/                      # Business logic actions
│   ├── Dtsen/
│   │   ├── CheckDecileEligibility.php
│   │   ├── GenerateCertificatePdf.php
│   │   └── GenerateVerificationCode.php
│   ├── Pbi/
│   │   └── GenerateRecommendationPdf.php
│   ├── Rehsos/
│   │   └── CreateCaseFromSource.php
│   └── ServiceRequest/
│       ├── GenerateTicketNumber.php
│       └── TransitionStatus.php
├── Enums/                        # ✅ Sudah ada (14 enum)
├── Filament/
│   ├── Pages/
│   │   └── Dashboard.php         # Custom dashboard dengan filter
│   ├── Resources/
│   │   ├── ServiceRequestResource/
│   │   │   ├── ServiceRequestResource.php
│   │   │   ├── Pages/
│   │   │   │   ├── ListServiceRequests.php
│   │   │   │   ├── CreateServiceRequest.php
│   │   │   │   ├── EditServiceRequest.php
│   │   │   │   └── ViewServiceRequest.php
│   │   │   └── RelationManagers/
│   │   │       ├── DocumentsRelationManager.php
│   │   │       ├── StatusHistoriesRelationManager.php
│   │   │       └── ApprovalsRelationManager.php
│   │   ├── ComplaintResource/
│   │   ├── RehabilitationCaseResource/
│   │   ├── ClientResource/
│   │   ├── InformationPageResource/
│   │   ├── UserResource/
│   │   └── ... (master data resources)
│   └── Widgets/
│       ├── DtsenIssuedChart.php
│       ├── DtsenAwaitingSignature.php
│       ├── PbiByStageChart.php
│       ├── PbiEmergencyWidget.php
│       ├── ActiveRehabCasesChart.php
│       ├── IncomingRequestsWidget.php
│       ├── ProcessVsCompletedChart.php
│       └── RegionalDistributionChart.php
├── Http/
│   └── Livewire/                 # Portal publik
│       ├── PublicHomePage.php
│       ├── ServiceListPage.php
│       ├── ServiceDetailPage.php
│       ├── ServiceApplicationForm.php
│       ├── ComplaintForm.php
│       ├── TrackStatusPage.php
│       ├── VerifyCertificatePage.php
│       └── AccountDashboard.php
├── Jobs/
│   ├── GenerateDtsenPdfJob.php
│   ├── GeneratePbiRecommendationPdfJob.php
│   ├── ExportReportJob.php
│   └── FlagStalledTicketsJob.php
├── Models/                       # ✅ Sudah ada (31 model)
├── Policies/
│   ├── ServiceRequestPolicy.php
│   ├── DtsenCertificatePolicy.php
│   ├── PbiReactivationPolicy.php
│   ├── RehabilitationCasePolicy.php
│   ├── ClientPolicy.php
│   ├── ComplaintPolicy.php
│   ├── InformationPagePolicy.php
│   └── UserPolicy.php
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php # ✅ Sudah ada (perlu update)
```

---

## ⏱️ Estimasi Timeline

| Fase | Durasi | Keterangan |
|------|--------|------------|
| **Fase 0** — Fondasi | 2–3 hari | Paket, roles, seeders |
| **Fase 1** — Master Data | 3–4 hari | 9 resource sederhana |
| **Fase 2** — SK DTSEN | 5–7 hari | Alur paling kompleks |
| **Fase 3** — Reaktivasi PBI | 4–5 hari | Mirip DTSEN, lebih banyak tahap |
| **Fase 4** — Rehabilitasi | 5–6 hari | Assessment, rujukan, monitoring |
| **Fase 5** — Pengajuan Umum & Pengaduan | 4–5 hari | Reuse & pengaduan baru |
| **Fase 6** — Dashboard | 4–5 hari | 10+ widget, filter global |
| **Fase 7** — Laporan | 3–4 hari | 5 jenis laporan + ekspor |
| **Fase 8** — Portal Publik | 5–7 hari | 8+ halaman Livewire |
| **Fase 9** — Authorization | *Paralel* | Policy seiring resource |
| **Fase 10** — Finishing | 4–5 hari | Queue, notifikasi, testing |
| **Total** | **~7–10 minggu** | |

---

## 🔑 Keputusan Teknis yang Perlu Dikonfirmasi

1. **Database PostgreSQL**: Apakah PostgreSQL sudah dikonfigurasi di `.env`? Saat ini ada `database.sqlite` — migrasi perlu dijalankan ulang ke PostgreSQL.
2. **Tanda tangan elektronik (TTE/BSrE)**: Apakah cukup QR + persetujuan di sistem, atau perlu integrasi TTE tersertifikasi?
3. **Notifikasi WA/SMS**: Apakah masuk scope? Jika ya, perlu pilih provider (Fonnte, Zenziva, dll.).
4. **Login masyarakat**: Self-registration atau dibuat operator?
5. **Format nomor surat dinas**: Perlu contoh format yang dipakai Dinsos Kab. Blitar.
6. **Branding**: Logo dan warna resmi Dinsos Kab. Blitar?

---

## ▶️ Langkah Selanjutnya

Setelah rencana ini disetujui, kita mulai dari **Fase 0** dengan urutan:
1. Install `spatie/laravel-permission` & `spatie/laravel-activitylog`
2. Publish & jalankan migrasi spatie
3. Tambah `HasRoles` ke User model
4. Buat `RolePermissionSeeder`
5. Buat seeders master data
6. Mulai Fase 1 — resource master data pertama

> [!TIP]
> Disarankan menggunakan perintah `/goal` untuk fase-fase besar (seperti Fase 2–4) agar agent bekerja menyeluruh tanpa berhenti di tengah jalan.
