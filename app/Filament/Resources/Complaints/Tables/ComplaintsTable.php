<?php

namespace App\Filament\Resources\Complaints\Tables;

use App\Actions\ServiceRequest\GenerateTicketNumber;
use App\Enums\ComplaintStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\RehabilitationHandlingType;
use App\Models\Client;
use App\Models\Complaint;
use App\Models\Disposition;
use App\Models\RehabilitationCase;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ComplaintsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('complaint_number')
                    ->label('Nomor Laporan')
                    ->badge()
                    ->color('danger')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reporter_name')
                    ->label('Pelapor')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Complaint $record): string => $record->reporter_phone ?? '-'),

                TextColumn::make('village.name')
                    ->label('Lokasi Kejadian')
                    ->formatStateUsing(fn ($state, Complaint $record): string => $record->village ? "{$record->village->name} ({$record->village->district?->name})" : '-')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ComplaintStatus $state): string => match ($state) {
                        ComplaintStatus::Received => 'warning',
                        ComplaintStatus::Verification,
                        ComplaintStatus::ClarificationRequested => 'purple',
                        ComplaintStatus::Dispatched,
                        ComplaintStatus::InHandling => 'info',
                        ComplaintStatus::Resolved => 'success',
                        ComplaintStatus::Duplicate,
                        ComplaintStatus::Invalid => 'danger',
                    })
                    ->formatStateUsing(fn (ComplaintStatus $state): string => $state->label()),

                TextColumn::make('officer.name')
                    ->label('Petugas')
                    ->placeholder('Belum Ditugaskan'),

                TextColumn::make('reported_at')
                    ->label('Waktu Lapor')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('reported_at', 'desc')
            ->filters([
                SelectFilter::make('complaint_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(ComplaintStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),

                SelectFilter::make('village_id')
                    ->label('Desa Kejadian')
                    ->relationship('village', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->label('Detail Pengaduan'),

                    // 1. Verifikasi Awal
                    Action::make('verifikasiAwal')
                        ->label('Verifikasi Awal / Lapangan')
                        ->icon('heroicon-o-magnifying-glass')
                        ->color('info')
                        ->visible(fn (Complaint $record): bool => in_array($record->status, [ComplaintStatus::Received, ComplaintStatus::ClarificationRequested]))
                        ->form([
                            Textarea::make('verification_result')
                                ->label('Hasil Verifikasi / Keterangan Lapangan')
                                ->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            $record->update([
                                'status' => ComplaintStatus::Verification,
                                'verification_result' => $data['verification_result'],
                            ]);

                            $record->statusHistories()->create([
                                'from_status' => ComplaintStatus::Received->value,
                                'to_status' => ComplaintStatus::Verification->value,
                                'notes' => 'Verifikasi: '.$data['verification_result'],
                                'user_id' => auth()->id(),
                                'created_at' => Carbon::now(),
                            ]);

                            Notification::make()->title('Pengaduan berhasil diverifikasi')->success()->send();
                        }),

                    // 2. Minta Klarifikasi Pelapor
                    Action::make('mintaKlarifikasi')
                        ->label('Minta Klarifikasi Pelapor')
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')
                        ->color('warning')
                        ->visible(fn (Complaint $record): bool => in_array($record->status, [ComplaintStatus::Received, ComplaintStatus::Verification]))
                        ->form([
                            Textarea::make('notes')->label('Poin Pertanyaan / Informasi Tambahan Yang Dibutuhkan')->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            $record->update(['status' => ComplaintStatus::ClarificationRequested]);

                            $record->statusHistories()->create([
                                'from_status' => $record->status->value,
                                'to_status' => ComplaintStatus::ClarificationRequested->value,
                                'notes' => 'Klarifikasi diminta: '.$data['notes'],
                                'user_id' => auth()->id(),
                                'created_at' => Carbon::now(),
                            ]);

                            Notification::make()->title('Permintaan klarifikasi dicatat')->warning()->send();
                        }),

                    // 3. Disposisi Petugas
                    Action::make('disposisi')
                        ->label('Disposisikan Penanganan')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('primary')
                        ->form([
                            Select::make('to_user_id')
                                ->label('Petugas / Peksos Ditugaskan')
                                ->options(User::where('is_active', true)->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            Textarea::make('notes')->label('Instruksi Tindak Lanjut')->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            Disposition::create([
                                'dispositionable_type' => Complaint::class,
                                'dispositionable_id' => $record->id,
                                'from_user_id' => auth()->id(),
                                'to_user_id' => $data['to_user_id'],
                                'notes' => $data['notes'],
                            ]);

                            $record->update([
                                'officer_id' => $data['to_user_id'],
                                'status' => ComplaintStatus::Dispatched,
                            ]);

                            $record->statusHistories()->create([
                                'from_status' => $record->status->value,
                                'to_status' => ComplaintStatus::Dispatched->value,
                                'notes' => 'Didisposisikan: '.$data['notes'],
                                'user_id' => auth()->id(),
                                'created_at' => Carbon::now(),
                            ]);

                            Notification::make()->title('Pengaduan berhasil didisposisikan')->success()->send();
                        }),

                    // 4. Eskalasi ke Kasus Rehabilitasi Sosial (Fase 5.2 PRD)
                    Action::make('eskalasiKeRehsos')
                        ->label('Eskalasi ke Rehabilitasi Sosial')
                        ->icon('heroicon-o-arrow-up-right')
                        ->color('purple')
                        ->form([
                            Select::make('client_id')
                                ->label('Pilih Klien PPKS (Atau buat klien baru terlebih dahulu)')
                                ->options(Client::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            Select::make('handling_type')
                                ->label('Rencana Penanganan')
                                ->options(collect(RehabilitationHandlingType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                ->default(RehabilitationHandlingType::Direct)
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan Pokok Masalah Kasus')
                                ->default(fn (Complaint $record): string => "Berdasarkan Pengaduan {$record->complaint_number}: {$record->description}"),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            $caseNumber = GenerateTicketNumber::execute('RHS');

                            RehabilitationCase::create([
                                'case_number' => $caseNumber,
                                'client_id' => $data['client_id'],
                                'complaint_id' => $record->id,
                                'officer_id' => auth()->id(),
                                'handling_type' => $data['handling_type'],
                                'status' => RehabilitationCaseStatus::Received,
                                'received_at' => Carbon::now(),
                            ]);

                            $record->update([
                                'status' => ComplaintStatus::InHandling,
                                'action_taken' => "Dieskalasi menjadi Kasus Rehabilitasi Sosial No. {$caseNumber}",
                            ]);

                            $record->statusHistories()->create([
                                'from_status' => $record->status->value,
                                'to_status' => ComplaintStatus::InHandling->value,
                                'notes' => "Dieskalasi ke Rehabilitasi Sosial: Kasus No. {$caseNumber}",
                                'user_id' => auth()->id(),
                                'created_at' => Carbon::now(),
                            ]);

                            Notification::make()->title("Kasus Rehabilitasi {$caseNumber} berhasil dibuka!")->success()->send();
                        }),

                    // 5. Tandai Duplikat
                    Action::make('tandaiDuplikat')
                        ->label('Tandai Sebagai Duplikat')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->form([
                            Select::make('duplicate_of_id')
                                ->label('Laporan Induk')
                                ->options(fn (?Complaint $record) => Complaint::query()
                                    ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                    ->pluck('complaint_number', 'id')
                                )
                                ->searchable()
                                ->required(),
                            Textarea::make('notes')->label('Catatan Kesamaan Laporan')->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            $record->update([
                                'status' => ComplaintStatus::Duplicate,
                                'duplicate_of_id' => $data['duplicate_of_id'],
                            ]);

                            $record->statusHistories()->create([
                                'from_status' => $record->status->value,
                                'to_status' => ComplaintStatus::Duplicate->value,
                                'notes' => 'Ditandai duplikat dari laporan: '.$data['duplicate_of_id'].' - '.$data['notes'],
                                'user_id' => auth()->id(),
                                'created_at' => Carbon::now(),
                            ]);

                            Notification::make()->title('Laporan ditandai sebagai duplikat')->warning()->send();
                        }),

                    // 6. Selesaikan Pengaduan
                    Action::make('selesaikanPengaduan')
                        ->label('Selesaikan Penanganan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('action_taken')
                                ->label('Laporan Hasil Tindak Lanjut / Solusi Masalah')
                                ->rows(3)
                                ->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            $record->update([
                                'action_taken' => $data['action_taken'],
                                'status' => ComplaintStatus::Resolved,
                                'resolved_at' => Carbon::now(),
                            ]);

                            $record->statusHistories()->create([
                                'from_status' => $record->status->value,
                                'to_status' => ComplaintStatus::Resolved->value,
                                'notes' => 'Selesai: '.$data['action_taken'],
                                'user_id' => auth()->id(),
                                'created_at' => Carbon::now(),
                            ]);

                            Notification::make()->title('Pengaduan masyarakat resmi selesai ditangani!')->success()->send();
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
