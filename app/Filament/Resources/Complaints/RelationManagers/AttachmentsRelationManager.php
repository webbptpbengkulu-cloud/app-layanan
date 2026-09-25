<?php

namespace App\Filament\Resources\Complaints\RelationManagers;

use App\Enums\ComplaintAttachmentType;
use App\Models\ComplaintAttachment;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Lampiran & Bukti Foto Pengaduan';

    protected static ?string $modelLabel = 'Lampiran';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Tipe Lampiran')
                    ->options([
                        ComplaintAttachmentType::Photo->value => 'Foto / Gambar Lapangan',
                        ComplaintAttachmentType::Document->value => 'Dokumen Pendukung',
                    ])
                    ->default(ComplaintAttachmentType::Photo->value)
                    ->required(),
                FileUpload::make('file_path')
                    ->label('File Lampiran')
                    ->directory('complaint-attachments')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                    ->maxSize(5120)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_path')
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Pratinjau')
                    ->visibility(fn (ComplaintAttachment $record): bool => $record->type === ComplaintAttachmentType::Photo)
                    ->circular(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (ComplaintAttachmentType $state): string => $state->label()),
                TextColumn::make('file_path')
                    ->label('Lokasi File')
                    ->limit(40),
                TextColumn::make('created_at')
                    ->label('Diupload')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Lampiran Bukti'),
            ])
            ->recordActions([
                Action::make('bukaFile')
                    ->label('Buka')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn (ComplaintAttachment $record): string => Storage::disk('public')->url($record->file_path), shouldOpenInNewTab: true),
                DeleteAction::make(),
            ]);
    }
}
