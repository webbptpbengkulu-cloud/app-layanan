<?php

namespace App\Filament\Resources\ServiceRequests\RelationManagers;

use App\Enums\DocumentVerificationStatus;
use App\Models\ServiceRequestDocument;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Dokumen & Berkas Persyaratan';

    protected static ?string $modelLabel = 'Dokumen Persyaratan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_requirement_id')
                    ->label('Jenis Persyaratan')
                    ->relationship('serviceRequirement', 'name')
                    ->required()
                    ->preload(),
                FileUpload::make('file_path')
                    ->label('Berkas Dokumen')
                    ->directory('service-documents')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->maxSize(5120)
                    ->required(),
                TextInput::make('original_name')
                    ->label('Nama Asli File')
                    ->maxLength(255),
                Select::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options(collect(DocumentVerificationStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->default(DocumentVerificationStatus::Pending),
                Textarea::make('notes')
                    ->label('Catatan Petugas')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('original_name')
            ->columns([
                TextColumn::make('serviceRequirement.name')
                    ->label('Persyaratan')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('original_name')
                    ->label('Nama Berkas')
                    ->searchable()
                    ->description(fn (ServiceRequestDocument $record): string => $record->file_path),
                TextColumn::make('verification_status')
                    ->label('Status Validasi')
                    ->badge()
                    ->color(fn (DocumentVerificationStatus $state): string => match ($state) {
                        DocumentVerificationStatus::Valid => 'success',
                        DocumentVerificationStatus::RevisionNeeded => 'danger',
                        DocumentVerificationStatus::Pending => 'warning',
                    })
                    ->formatStateUsing(fn (DocumentVerificationStatus $state): string => $state->label()),
                TextColumn::make('notes')
                    ->label('Catatan Petugas')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Diupload')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()->label('Unggah Dokumen Tambahan'),
            ])
            ->recordActions([
                Action::make('lihatDokumen')
                    ->label('Buka File')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->visible(fn (ServiceRequestDocument $record): bool => ! empty($record->file_path))
                    ->url(fn (ServiceRequestDocument $record): string => Storage::disk('public')->url($record->file_path), shouldOpenInNewTab: true),

                Action::make('verifikasi')
                    ->label('Validasi Berkas')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->form([
                        Select::make('verification_status')
                            ->label('Status Verifikasi')
                            ->options(collect(DocumentVerificationStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->required(),
                        Textarea::make('notes')
                            ->label('Catatan (Alasan bila perlu revisi)'),
                    ])
                    ->action(function (ServiceRequestDocument $record, array $data): void {
                        $record->update([
                            'verification_status' => $data['verification_status'],
                            'notes' => $data['notes'],
                        ]);
                        Notification::make()->title('Status dokumen diperbarui')->success()->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
