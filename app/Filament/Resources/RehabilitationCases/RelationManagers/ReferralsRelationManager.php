<?php

namespace App\Filament\Resources\RehabilitationCases\RelationManagers;

use App\Actions\ServiceRequest\GenerateTicketNumber;
use App\Enums\ReferralStatus;
use App\Models\ReferralInstitution;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReferralsRelationManager extends RelationManager
{
    protected static string $relationship = 'referrals';

    protected static ?string $title = 'Riwayat Rujukan ke Lembaga';

    protected static ?string $modelLabel = 'Rujukan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('referral_institution_id')
                    ->label('Lembaga Tujuan Rujukan')
                    ->options(ReferralInstitution::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                DatePicker::make('referral_date')
                    ->label('Tanggal Rujukan')
                    ->default(now())
                    ->required(),
                Select::make('status')
                    ->label('Status Rujukan')
                    ->options(collect(ReferralStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->default(ReferralStatus::Sent)
                    ->required(),
                Textarea::make('reason')
                    ->label('Alasan & Tujuan Rujukan')
                    ->rows(2)
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Catatan Perkembangan / Respon Lembaga')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('referral_number')
            ->columns([
                TextColumn::make('referral_number')
                    ->label('No. Rujukan')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('institution.name')
                    ->label('Lembaga Tujuan')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('referral_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ReferralStatus $state): string => match ($state) {
                        ReferralStatus::Draft => 'gray',
                        ReferralStatus::Sent => 'warning',
                        ReferralStatus::Accepted,
                        ReferralStatus::InService => 'info',
                        ReferralStatus::Completed => 'success',
                        ReferralStatus::Declined,
                        ReferralStatus::Cancelled => 'danger',
                    })
                    ->formatStateUsing(fn (ReferralStatus $state): string => $state->label()),
                TextColumn::make('notes')
                    ->label('Catatan Lembaga')
                    ->placeholder('-'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Rujukan')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['referral_number'] = GenerateTicketNumber::execute('RJK');
                        $data['officer_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('referral_date', 'desc');
    }
}
