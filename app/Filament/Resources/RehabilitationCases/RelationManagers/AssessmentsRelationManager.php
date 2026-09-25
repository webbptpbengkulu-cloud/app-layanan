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

class AssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessments';

    protected static ?string $title = 'Riwayat Assessment Klien';

    protected static ?string $modelLabel = 'Assessment';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('assessment_date')
                    ->label('Tanggal Assessment')
                    ->default(now())
                    ->required(),
                Textarea::make('findings')
                    ->label('Temuan Masalah & Kondisi Klien')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('needs')
                    ->label('Kebutuhan Dasar')
                    ->rows(2)
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('recommendations')
                    ->label('Rekomendasi Rencana Penanganan')
                    ->rows(2)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('findings')
            ->columns([
                TextColumn::make('assessment_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('findings')
                    ->label('Temuan Masalah')
                    ->limit(40)
                    ->wrap(),
                TextColumn::make('needs')
                    ->label('Kebutuhan')
                    ->limit(30),
                TextColumn::make('recommendations')
                    ->label('Rekomendasi')
                    ->limit(40),
                TextColumn::make('officer.name')
                    ->label('Peksos')
                    ->badge()
                    ->color('info'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Assessment')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['officer_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('assessment_date', 'desc');
    }
}
