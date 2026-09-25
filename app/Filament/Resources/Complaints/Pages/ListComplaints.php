<?php

namespace App\Filament\Resources\Complaints\Pages;

use App\Enums\ComplaintStatus;
use App\Filament\Resources\Complaints\ComplaintResource;
use App\Models\Complaint;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListComplaints extends ListRecords
{
    protected static string $resource = ComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Catat Pengaduan Baru'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(Complaint::count()),

            'baru' => Tab::make('Laporan Baru')
                ->badge(Complaint::where('status', ComplaintStatus::Received)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ComplaintStatus::Received)),

            'proses' => Tab::make('Dalam Penanganan')
                ->badge(Complaint::whereIn('status', [
                    ComplaintStatus::Verification,
                    ComplaintStatus::ClarificationRequested,
                    ComplaintStatus::Dispatched,
                    ComplaintStatus::InHandling,
                ])->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [
                    ComplaintStatus::Verification,
                    ComplaintStatus::ClarificationRequested,
                    ComplaintStatus::Dispatched,
                    ComplaintStatus::InHandling,
                ])),

            'selesai' => Tab::make('Selesai Ditangani')
                ->badge(Complaint::where('status', ComplaintStatus::Resolved)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ComplaintStatus::Resolved)),

            'duplikat_invalid' => Tab::make('Duplikat / Ditolak')
                ->badge(Complaint::whereIn('status', [
                    ComplaintStatus::Duplicate,
                    ComplaintStatus::Invalid,
                ])->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [
                    ComplaintStatus::Duplicate,
                    ComplaintStatus::Invalid,
                ])),
        ];
    }
}
