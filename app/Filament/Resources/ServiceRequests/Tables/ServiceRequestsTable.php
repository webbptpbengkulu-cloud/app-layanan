<?php

namespace App\Filament\Resources\ServiceRequests\Tables;

use App\Actions\Dtsen\CheckDecileEligibility;
use App\Actions\Dtsen\GenerateCertificatePdf;
use App\Actions\Dtsen\GenerateVerificationCode;
use App\Actions\ServiceRequest\TransitionStatus;
use App\Enums\ApprovalDecision;
use App\Enums\MinistryDecision;
use App\Enums\PbiReason;
use App\Enums\ServiceRequestStatus;
use App\Models\Approval;
use App\Models\Disposition;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\PbiReactivation;
use App\Models\ServiceRequest;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ServiceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')
                    ->label('Nomor Tiket')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('serviceType.name')
                    ->label('Jenis Layanan')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('applicant_name')
                    ->label('Nama Pemohon')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (ServiceRequest $record): string => "NIK: {$record->applicant_nik}"),

                TextColumn::make('village.name')
                    ->label('Wilayah')
                    ->formatStateUsing(fn ($state, ServiceRequest $record): string => $record->village ? "{$record->village->name}, {$record->village->district?->name}" : '-')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ServiceRequestStatus $state): string => match ($state) {
                        ServiceRequestStatus::Submitted => 'warning',
                        ServiceRequestStatus::DocumentCheck,
                        ServiceRequestStatus::DataVerification,
                        ServiceRequestStatus::EligibilityVerification,
                        ServiceRequestStatus::InProcess => 'info',
                        ServiceRequestStatus::AwaitingApproval => 'purple',
                        ServiceRequestStatus::Issued,
                        ServiceRequestStatus::Completed,
                        ServiceRequestStatus::Reactivated,
                        ServiceRequestStatus::MinistryApproved,
                        ServiceRequestStatus::RecommendationIssued => 'success',
                        ServiceRequestStatus::Rejected,
                        ServiceRequestStatus::MinistryRejected,
                        ServiceRequestStatus::RevisionRequested => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (ServiceRequestStatus $state): string => $state->label()),

                IconColumn::make('is_priority')
                    ->label('Darurat')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-minus')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('officer.name')
                    ->label('Petugas')
                    ->placeholder('Belum Ditugaskan')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Masuk')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('service_type_id')
                    ->label('Jenis Layanan')
                    ->relationship('serviceType', 'name'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(ServiceRequestStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),

                SelectFilter::make('village_id')
                    ->label('Desa')
                    ->relationship('village', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_priority')
                    ->label('Kasus Prioritas / Darurat'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->label('Detail / Edit Data'),

                    // 1. Periksa Berkas
                    Action::make('periksaBerkas')
                        ->label('Periksa Berkas')
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->color('info')
                        ->visible(fn (ServiceRequest $record): bool => in_array($record->status, [ServiceRequestStatus::Submitted, ServiceRequestStatus::RevisionRequested]))
                        ->form([
                            Textarea::make('notes')
                                ->label('Catatan Pemeriksaan Berkas')
                                ->placeholder('Berkas persyaratan telah diperiksa dan dinyatakan lengkap/sesuai.')
                                ->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            TransitionStatus::execute($record, ServiceRequestStatus::DocumentCheck, $data['notes']);
                            Notification::make()->title('Status diperbarui menjadi Pemeriksaan Berkas')->success()->send();
                        }),

                    // 2. Minta Perbaikan Berkas
                    Action::make('mintaPerbaikan')
                        ->label('Minta Perbaikan (Revisi)')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->visible(fn (ServiceRequest $record): bool => in_array($record->status, [ServiceRequestStatus::Submitted, ServiceRequestStatus::DocumentCheck]))
                        ->form([
                            Textarea::make('notes')
                                ->label('Rincian Kekurangan / Perbaikan Berkas')
                                ->required()
                                ->placeholder('Contoh: KTP buram, KK belum diperbarui, atau surat faskes belum bertanda tangan.'),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $record->rejection_reason = $data['notes'];
                            $record->save();
                            TransitionStatus::execute($record, ServiceRequestStatus::RevisionRequested, 'Diminta perbaikan: '.$data['notes']);
                            Notification::make()->title('Permintaan perbaikan berhasil dikirim ke pemohon')->warning()->send();
                        }),

                    // 3. Cek SIKS-NG & Validasi Desil (Khusus SK DTSEN)
                    Action::make('cekSiksNgDtsen')
                        ->label('Cek SIKS-NG (DTSEN)')
                        ->icon('heroicon-o-check-badge')
                        ->color('primary')
                        ->visible(fn (ServiceRequest $record): bool => $record->serviceType?->handler?->value === 'dtsen' && in_array($record->status, [ServiceRequestStatus::Submitted, ServiceRequestStatus::DocumentCheck, ServiceRequestStatus::DataVerification]))
                        ->mountUsing(function ($form, ServiceRequest $record): void {
                            $cert = $record->dtsenCertificate;
                            $form->fill([
                                'subject_name' => $cert?->subject_name ?? $record->applicant_name,
                                'subject_nik' => $cert?->subject_nik ?? $record->applicant_nik,
                                'dtsen_purpose_id' => $cert?->dtsen_purpose_id,
                                'is_registered' => $cert?->is_registered ?? true,
                                'decile' => $cert?->decile ?? 1,
                            ]);
                        })
                        ->form([
                            TextInput::make('subject_name')->label('Nama Subjek')->required(),
                            TextInput::make('subject_nik')->label('NIK Subjek')->required()->length(16),
                            Select::make('dtsen_purpose_id')
                                ->label('Tujuan Penggunaan')
                                ->options(DtsenPurpose::where('is_active', true)->pluck('name', 'id'))
                                ->required(),
                            Select::make('relationship_to_applicant')
                                ->label('Hubungan dengan Pemohon')
                                ->options([
                                    'Diri Sendiri' => 'Diri Sendiri',
                                    'Anak Kandung' => 'Anak Kandung',
                                    'Suami / Istri' => 'Suami / Istri',
                                    'Orang Tua' => 'Orang Tua',
                                    'Lainnya' => 'Lainnya',
                                ])
                                ->default('Diri Sendiri')
                                ->required(),
                            Toggle::make('is_registered')
                                ->label('Terdaftar di DTKS / DTSEN')
                                ->default(true)
                                ->required(),
                            TextInput::make('decile')
                                ->label('Peringkat Desil Hasil Cek SIKS-NG')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(10)
                                ->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $purpose = DtsenPurpose::findOrFail($data['dtsen_purpose_id']);
                            $eligibility = CheckDecileEligibility::execute((int) $data['decile'], $purpose);

                            $cert = DtsenCertificate::updateOrCreate(
                                ['service_request_id' => $record->id],
                                [
                                    'subject_name' => $data['subject_name'],
                                    'subject_nik' => $data['subject_nik'],
                                    'relationship_to_applicant' => $data['relationship_to_applicant'] ?? 'Diri Sendiri',
                                    'dtsen_purpose_id' => $data['dtsen_purpose_id'],
                                    'is_registered' => $data['is_registered'],
                                    'decile' => $data['decile'],
                                    'checked_at' => Carbon::now(),
                                    'checker_id' => auth()->id(),
                                    'valid_until' => Carbon::now()->addDays($purpose->validity_days ?? 30),
                                ]
                            );

                            if ($eligibility['eligible']) {
                                TransitionStatus::execute($record, ServiceRequestStatus::AwaitingApproval, $eligibility['message']);
                                Notification::make()->title('Memenuhi Syarat! Masuk antrean persetujuan/paraf.')->success()->send();
                            } else {
                                TransitionStatus::execute($record, ServiceRequestStatus::Rejected, $eligibility['message']);
                                Notification::make()->title('Tidak Memenuhi Syarat: '.$eligibility['message'])->danger()->send();
                            }
                        }),

                    // 4. Paraf Berjenjang (Kabid Linjamsos)
                    Action::make('parafKabid')
                        ->label('Paraf Kabid')
                        ->icon('heroicon-o-pencil-square')
                        ->color('purple')
                        ->visible(fn (ServiceRequest $record): bool => $record->status === ServiceRequestStatus::AwaitingApproval && $record->dtsenCertificate !== null)
                        ->form([
                            Textarea::make('notes')->label('Catatan Paraf / Verifikasi Akhir')->default('Berkas dan desil telah diverifikasi sesuai ketentuan, disetujui untuk penandatanganan Kepala Dinas.'),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            Approval::create([
                                'approvable_type' => DtsenCertificate::class,
                                'approvable_id' => $record->dtsenCertificate->id,
                                'step' => 1,
                                'approver_id' => auth()->id(),
                                'decision' => ApprovalDecision::Approved,
                                'notes' => $data['notes'],
                                'decided_at' => Carbon::now(),
                            ]);

                            TransitionStatus::execute($record, ServiceRequestStatus::AwaitingApproval, 'Telah diparaf Kabid: '.$data['notes']);
                            Notification::make()->title('Paraf berhasil disimpan. Menunggu tanda tangan Kadis.')->success()->send();
                        }),

                    // 5. Tanda Tangani SK DTSEN (Kadis) -> Generate PDF + QR Code
                    Action::make('tandatanganiDtsen')
                        ->label('Tanda Tangani SK (Terbitkan)')
                        ->icon('heroicon-o-shield-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Tanda Tangani & Terbitkan SK DTSEN')
                        ->modalDescription('Tindakan ini akan mengesahkan surat keterangan, menghasilkan kode verifikasi QR, serta menerbitkan dokumen PDF resmi.')
                        ->visible(fn (ServiceRequest $record): bool => $record->status === ServiceRequestStatus::AwaitingApproval && $record->dtsenCertificate !== null)
                        ->action(function (ServiceRequest $record): void {
                            $cert = $record->dtsenCertificate;
                            $code = GenerateVerificationCode::execute();
                            $certNumber = sprintf('400.9.1/%04d/409.106/%s', $cert->id, Carbon::now()->format('Y'));

                            $cert->certificate_number = $certNumber;
                            $cert->verification_code = $code;
                            $cert->signer_id = auth()->id();
                            $cert->issued_at = Carbon::now();
                            $cert->valid_until = Carbon::now()->addDays($cert->dtsenPurpose?->validity_days ?? 30);
                            $cert->save();

                            // Generate PDF
                            GenerateCertificatePdf::execute($cert);

                            // Catat approval step 2
                            Approval::create([
                                'approvable_type' => DtsenCertificate::class,
                                'approvable_id' => $cert->id,
                                'step' => 2,
                                'approver_id' => auth()->id(),
                                'decision' => ApprovalDecision::Approved,
                                'notes' => 'Surat Keterangan Terdaftar DTKS/DTSEN ditandatangani dan diterbitkan.',
                                'decided_at' => Carbon::now(),
                            ]);

                            TransitionStatus::execute($record, ServiceRequestStatus::Issued, 'Surat Keterangan terbit dengan nomor: '.$certNumber);
                            Notification::make()->title('SK DTSEN Berhasil Ditandatangani & Diterbitkan!')->success()->send();
                        }),

                    // 6. Unduh PDF SK DTSEN
                    Action::make('unduhPdfDtsen')
                        ->label('Unduh SK Terbit (PDF)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->visible(fn (ServiceRequest $record): bool => ! empty($record->dtsenCertificate?->file_path))
                        ->url(fn (ServiceRequest $record): string => Storage::disk('public')->url($record->dtsenCertificate->file_path), shouldOpenInNewTab: true),

                    // 7. Aksi Alur Reaktivasi PBI-JK (Fase 3)
                    Action::make('prosesPbiJk')
                        ->label('Proses Alur PBI-JK')
                        ->icon('heroicon-o-heart')
                        ->color('primary')
                        ->visible(fn (ServiceRequest $record): bool => $record->serviceType?->handler?->value === 'pbi')
                        ->form([
                            Select::make('tahap')
                                ->label('Tahapan Proses PBI-JK')
                                ->options([
                                    'verifikasi_kelayakan' => '1. Verifikasi Kelayakan (Cek DTKS & Faskes)',
                                    'terbitkan_rekomendasi' => '2. Terbitkan Surat Rekomendasi Dinsos',
                                    'usulkan_kemensos' => '3. Input & Usulkan ke SIKS-NG Kemensos',
                                    'keputusan_kemensos' => '4. Catat Keputusan Kemensos RI',
                                    'konfirmasi_aktif' => '5. Konfirmasi Kartu Aktif Kembali (Selesai)',
                                ])
                                ->required()
                                ->live(),
                            TextInput::make('decile')
                                ->label('Peringkat Desil')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(10)
                                ->visible(fn ($get) => in_array($get('tahap'), ['verifikasi_kelayakan', 'terbitkan_rekomendasi'])),
                            Select::make('ministry_decision')
                                ->label('Hasil Keputusan Kemensos')
                                ->options(collect(MinistryDecision::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                ->visible(fn ($get) => $get('tahap') === 'keputusan_kemensos'),
                            DatePicker::make('reactivated_date')
                                ->label('Tanggal Aktif Kembali')
                                ->visible(fn ($get) => $get('tahap') === 'konfirmasi_aktif'),
                            Textarea::make('notes')->label('Catatan Proses')->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $pbi = PbiReactivation::firstOrCreate(
                                ['service_request_id' => $record->id],
                                [
                                    'participant_name' => $record->applicant_name,
                                    'participant_nik' => $record->applicant_nik,
                                    'bpjs_card_number' => 'BPJS-'.$record->applicant_nik,
                                    'reason' => PbiReason::Emergency,
                                ]
                            );

                            switch ($data['tahap']) {
                                case 'verifikasi_kelayakan':
                                    if (isset($data['decile'])) {
                                        $pbi->decile = $data['decile'];
                                    }
                                    $pbi->eligibility_notes = $data['notes'];
                                    $pbi->save();
                                    TransitionStatus::execute($record, ServiceRequestStatus::EligibilityVerification, $data['notes']);
                                    break;

                                case 'terbitkan_rekomendasi':
                                    $pbi->recommendation_number = sprintf('440/%04d/409.106/%s', $record->id, Carbon::now()->format('Y'));
                                    $pbi->recommendation_issued_at = Carbon::now();
                                    $pbi->signer_id = auth()->id();
                                    $pbi->save();
                                    TransitionStatus::execute($record, ServiceRequestStatus::RecommendationIssued, 'Surat Rekomendasi No: '.$pbi->recommendation_number);
                                    break;

                                case 'usulkan_kemensos':
                                    $pbi->proposed_to_ministry_at = Carbon::now();
                                    $pbi->save();
                                    TransitionStatus::execute($record, ServiceRequestStatus::ProposedToMinistry, $data['notes']);
                                    break;

                                case 'keputusan_kemensos':
                                    $decision = MinistryDecision::from($data['ministry_decision']);
                                    $pbi->ministry_decision = $decision;
                                    $pbi->ministry_decided_at = Carbon::now();
                                    $pbi->save();
                                    $nextStatus = $decision === MinistryDecision::Approved ? ServiceRequestStatus::MinistryApproved : ServiceRequestStatus::MinistryRejected;
                                    TransitionStatus::execute($record, $nextStatus, $data['notes']);
                                    break;

                                case 'konfirmasi_aktif':
                                    $pbi->reactivated_date = $data['reactivated_date'] ?? Carbon::now();
                                    $pbi->save();
                                    TransitionStatus::execute($record, ServiceRequestStatus::Reactivated, 'BPJS Aktif Kembali: '.$data['notes']);
                                    break;
                            }

                            Notification::make()->title('Alur Reaktivasi PBI berhasil diperbarui!')->success()->send();
                        }),

                    // 8. Disposisi Antar Pegawai
                    Action::make('disposisi')
                        ->label('Kirim Disposisi')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('gray')
                        ->form([
                            Select::make('to_user_id')
                                ->label('Pegawai / Petugas Tujuan')
                                ->options(User::where('is_active', true)->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            Textarea::make('notes')->label('Instruksi Disposisi')->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            Disposition::create([
                                'dispositionable_type' => ServiceRequest::class,
                                'dispositionable_id' => $record->id,
                                'from_user_id' => auth()->id(),
                                'to_user_id' => $data['to_user_id'],
                                'notes' => $data['notes'],
                            ]);

                            $record->officer_id = $data['to_user_id'];
                            $record->save();

                            Notification::make()->title('Disposisi berhasil diteruskan')->success()->send();
                        }),

                    // 9. Tolak Pengajuan
                    Action::make('tolak')
                        ->label('Tolak Pengajuan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('reason')->label('Alasan Penolakan')->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $record->rejection_reason = $data['reason'];
                            $record->save();
                            TransitionStatus::execute($record, ServiceRequestStatus::Rejected, 'Ditolak: '.$data['reason']);
                            Notification::make()->title('Pengajuan telah ditolak')->danger()->send();
                        }),

                    // 10. Selesaikan
                    Action::make('selesaikan')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('service_result')->label('Hasil Akhir Pelayanan')->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $record->service_result = $data['service_result'];
                            $record->save();
                            TransitionStatus::execute($record, ServiceRequestStatus::Completed, 'Selesai: '.$data['service_result']);
                            Notification::make()->title('Pelayanan telah diselesaikan')->success()->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
