<?php

namespace App\Filament\Resources\RehabilitationCases\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MonitoringRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'monitoringRecords';

    protected static ?string $title = 'Catatan Monitoring & Perkembangan Klien';

    protected static ?string $modelLabel = 'Catatan Monitoring';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('monitoring_date')
                    ->label('Tanggal Monitoring')
                    ->default(now())
                    ->required(),
                Textarea::make('progress_notes')
                    ->label('Catatan Perkembangan')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('client_condition')
                    ->label('Kondisi Terkini Klien')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('progress_notes')
            ->columns([
                TextColumn::make('monitoring_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('progress_notes')
                    ->label('Perkembangan Klien')
                    ->wrap(),
                TextColumn::make('client_condition')
                    ->label('Kondisi Fisik / Mental')
                    ->placeholder('-'),
                TextColumn::make('officer.name')
                    ->label('Petugas Monitoring')
                    ->badge()
                    ->color('info'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Catatan Monitoring')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['officer_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('monitoring_date', 'desc');
    }
}
