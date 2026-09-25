<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Enums\ServiceRequestStatus;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\ServiceRequest;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListServiceRequests extends ListRecords
{
    protected static string $resource = ServiceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Pengajuan Baru'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(ServiceRequest::count()),

            'baru' => Tab::make('Permohonan Baru')
                ->badge(ServiceRequest::where('status', ServiceRequestStatus::Submitted)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ServiceRequestStatus::Submitted)),

            'proses' => Tab::make('Dalam Proses')
                ->badge(ServiceRequest::whereIn('status', [
                    ServiceRequestStatus::DocumentCheck,
                    ServiceRequestStatus::DataVerification,
                    ServiceRequestStatus::EligibilityVerification,
                    ServiceRequestStatus::Verification,
                    ServiceRequestStatus::Assessment,
                    ServiceRequestStatus::AwaitingApproval,
                    ServiceRequestStatus::ProposedToMinistry,
                    ServiceRequestStatus::InProcess,
                ])->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [
                    ServiceRequestStatus::DocumentCheck,
                    ServiceRequestStatus::DataVerification,
                    ServiceRequestStatus::EligibilityVerification,
                    ServiceRequestStatus::Verification,
                    ServiceRequestStatus::Assessment,
                    ServiceRequestStatus::AwaitingApproval,
                    ServiceRequestStatus::ProposedToMinistry,
                    ServiceRequestStatus::InProcess,
                ])),

            'selesai' => Tab::make('Selesai / Terbit')
                ->badge(ServiceRequest::whereIn('status', [
                    ServiceRequestStatus::Issued,
                    ServiceRequestStatus::Completed,
                    ServiceRequestStatus::Reactivated,
                    ServiceRequestStatus::RecommendationIssued,
                ])->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [
                    ServiceRequestStatus::Issued,
                    ServiceRequestStatus::Completed,
                    ServiceRequestStatus::Reactivated,
                    ServiceRequestStatus::RecommendationIssued,
                ])),

            'ditolak' => Tab::make('Ditolak / Revisi')
                ->badge(ServiceRequest::whereIn('status', [
                    ServiceRequestStatus::Rejected,
                    ServiceRequestStatus::MinistryRejected,
                    ServiceRequestStatus::RevisionRequested,
                ])->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [
                    ServiceRequestStatus::Rejected,
                    ServiceRequestStatus::MinistryRejected,
                    ServiceRequestStatus::RevisionRequested,
                ])),
        ];
    }
}
