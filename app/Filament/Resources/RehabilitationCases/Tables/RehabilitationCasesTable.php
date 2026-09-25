<?php

namespace App\Filament\Resources\RehabilitationCases\Tables;

use App\Actions\ServiceRequest\GenerateTicketNumber;
use App\Enums\ReferralStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\RehabilitationHandlingType;
use App\Models\Assessment;
use App\Models\MonitoringRecord;
use App\Models\Referral;
use App\Models\ReferralInstitution;
use App\Models\RehabilitationCase;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RehabilitationCasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('case_number')
                    ->label('Nomor Kasus')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('client.name')
                    ->label('Klien PPKS')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (RehabilitationCase $record): string => $record->client?->category?->name ?? '-'),

                TextColumn::make('status')
                    ->label('Status Kasus')
                    ->badge()
                    ->color(fn (RehabilitationCaseStatus $state): string => match ($state) {
                        RehabilitationCaseStatus::Received => 'warning',
                        RehabilitationCaseStatus::Assessment => 'purple',
                        RehabilitationCaseStatus::ServicePlanning => 'info',
                        RehabilitationCaseStatus::InService => 'primary',
                        RehabilitationCaseStatus::Monitoring => 'teal',
                        RehabilitationCaseStatus::Closed => 'success',
                    })
                    ->formatStateUsing(fn (RehabilitationCaseStatus $state): string => $state->label()),

                TextColumn::make('handling_type')
                    ->label('Rencana Penanganan')
                    ->badge()
                    ->color(fn (RehabilitationHandlingType $state): string => match ($state) {
                        RehabilitationHandlingType::Direct => 'info',
                        RehabilitationHandlingType::Referral => 'warning',
                        RehabilitationHandlingType::Both => 'success',
                    })
                    ->formatStateUsing(fn (RehabilitationHandlingType $state): string => $state->label()),

                TextColumn::make('officer.name')
                    ->label('Peksos / Petugas')
                    ->placeholder('Belum Ditugaskan'),

                TextColumn::make('assessments_count')
                    ->counts('assessments')
                    ->label('Assessment')
                    ->badge()
                    ->alignCenter(),

                TextColumn::make('referrals_count')
                    ->counts('referrals')
                    ->label('Rujukan')
                    ->badge()
                    ->alignCenter(),

                TextColumn::make('received_at')
                    ->label('Waktu Masuk')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('received_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Kasus')
                    ->options(collect(RehabilitationCaseStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),

                SelectFilter::make('handling_type')
                    ->label('Jenis Penanganan')
                    ->options(collect(RehabilitationHandlingType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),

                SelectFilter::make('officer_id')
                    ->label('Peksos')
                    ->relationship('officer', 'name'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->label('Detail Kasus'),

                    // 1. Action: Assessment
                    Action::make('catatAssessment')
                        ->label('Catat Assessment')
                        ->icon('heroicon-o-document-text')
                        ->color('purple')
                        ->form([
                            DatePicker::make('assessment_date')
                                ->label('Tanggal Assessment')
                                ->default(now())
                                ->required(),
                            Textarea::make('findings')
                                ->label('Temuan Masalah & Kondisi Fisik/Psikososial')
                                ->rows(3)
                                ->required(),
                            Textarea::make('needs')
                                ->label('Kebutuhan Dasar & Mendesak Klien')
                                ->rows(2)
                                ->required(),
                            Textarea::make('recommendations')
                                ->label('Rekomendasi Rencana Intervensi')
                                ->rows(2)
                                ->required(),
                        ])
                        ->action(function (RehabilitationCase $record, array $data): void {
                            Assessment::create([
                                'rehabilitation_case_id' => $record->id,
                                'officer_id' => auth()->id(),
                                'assessment_date' => $data['assessment_date'],
                                'findings' => $data['findings'],
                                'needs' => $data['needs'],
                                'recommendations' => $data['recommendations'],
                            ]);

                            $record->update(['status' => RehabilitationCaseStatus::Assessment]);
                            Notification::make()->title('Hasil assessment berhasil disimpan')->success()->send();
                        }),

                    // 2. Action: Rencana Pelayanan
                    Action::make('rencanaPelayanan')
                        ->label('Tetapkan Rencana Pelayanan')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->color('info')
                        ->form([
                            Select::make('handling_type')
                                ->label('Pilihan Penanganan')
                                ->options(collect(RehabilitationHandlingType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                ->default($record->handling_type ?? RehabilitationHandlingType::Direct)
                                ->required(),
                        ])
                        ->action(function (RehabilitationCase $record, array $data): void {
                            $record->update([
                                'handling_type' => $data['handling_type'],
                                'status' => RehabilitationCaseStatus::ServicePlanning,
                            ]);
                            Notification::make()->title('Rencana pelayanan berhasil diperbarui')->success()->send();
                        }),

                    // 3. Action: Buat Rujukan
                    Action::make('buatRujukan')
                        ->label('Buat Surat Rujukan')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->form([
                            Select::make('referral_institution_id')
                                ->label('Lembaga Tujuan Rujukan')
                                ->options(ReferralInstitution::where('is_active', true)->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            DatePicker::make('referral_date')
                                ->label('Tanggal Rujukan')
                                ->default(now())
                                ->required(),
                            Textarea::make('reason')
                                ->label('Alasan & Tujuan Rujukan')
                                ->rows(2)
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan Khusus / Pendampingan')
                                ->rows(2),
                        ])
                        ->action(function (RehabilitationCase $record, array $data): void {
                            Referral::create([
                                'rehabilitation_case_id' => $record->id,
                                'referral_institution_id' => $data['referral_institution_id'],
                                'referral_number' => GenerateTicketNumber::execute('RJK'),
                                'referral_date' => $data['referral_date'],
                                'reason' => $data['reason'],
                                'notes' => $data['notes'] ?? null,
                                'status' => ReferralStatus::Sent,
                                'officer_id' => auth()->id(),
                            ]);

                            $record->update([
                                'handling_type' => in_array($record->handling_type, [RehabilitationHandlingType::Direct, RehabilitationHandlingType::Both]) ? RehabilitationHandlingType::Both : RehabilitationHandlingType::Referral,
                                'status' => RehabilitationCaseStatus::InService,
                            ]);

                            Notification::make()->title('Rujukan ke lembaga berhasil dibuat')->success()->send();
                        }),

                    // 4. Action: Catat Monitoring
                    Action::make('catatMonitoring')
                        ->label('Catat Perkembangan (Monitoring)')
                        ->icon('heroicon-o-eye')
                        ->color('teal')
                        ->form([
                            DatePicker::make('monitoring_date')
                                ->label('Tanggal Monitoring')
                                ->default(now())
                                ->required(),
                            Textarea::make('progress_notes')
                                ->label('Catatan Perkembangan Pelayanan')
                                ->rows(3)
                                ->required(),
                            Textarea::make('client_condition')
                                ->label('Kondisi Terkini Klien')
                                ->rows(2),
                        ])
                        ->action(function (RehabilitationCase $record, array $data): void {
                            MonitoringRecord::create([
                                'rehabilitation_case_id' => $record->id,
                                'officer_id' => auth()->id(),
                                'monitoring_date' => $data['monitoring_date'],
                                'progress_notes' => $data['progress_notes'],
                                'client_condition' => $data['client_condition'] ?? null,
                            ]);

                            $record->update(['status' => RehabilitationCaseStatus::Monitoring]);
                            Notification::make()->title('Catatan monitoring berhasil dicatat')->success()->send();
                        }),

                    // 5. Action: Tutup Kasus
                    Action::make('tutupKasus')
                        ->label('Tutup Kasus (Selesai)')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('handling_result')
                                ->label('Hasil Akhir Penanganan Kasus & Terminasi')
                                ->placeholder('Contoh: Klien telah dirujuk dan diterima di Panti Sosial, atau klien telah mandiri bersama keluarga.')
                                ->rows(3)
                                ->required(),
                        ])
                        ->action(function (RehabilitationCase $record, array $data): void {
                            $record->update([
                                'handling_result' => $data['handling_result'],
                                'status' => RehabilitationCaseStatus::Closed,
                                'closed_at' => Carbon::now(),
                            ]);
                            Notification::make()->title('Kasus rehabilitasi sosial resmi ditutup / selesai')->success()->send();
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
