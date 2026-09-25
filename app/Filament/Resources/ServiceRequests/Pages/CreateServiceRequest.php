<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Actions\ServiceRequest\GenerateTicketNumber;
use App\Enums\ServiceHandler;
use App\Enums\ServiceRequestStatus;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\ServiceType;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceRequest extends CreateRecord
{
    protected static string $resource = ServiceRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['request_number'])) {
            $serviceType = ! empty($data['service_type_id']) ? ServiceType::find($data['service_type_id']) : null;
            $prefix = match ($serviceType?->handler) {
                ServiceHandler::Dtsen => 'DTSEN',
                ServiceHandler::Pbi => 'PBI',
                default => 'SRV',
            };
            $data['request_number'] = GenerateTicketNumber::execute($prefix);
        }

        $data['submitted_at'] = Carbon::now();
        $data['submitter_id'] = auth()->id() ?? $data['submitter_id'] ?? null;
        $data['status'] = $data['status'] ?? ServiceRequestStatus::Submitted;

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->statusHistories()->create([
            'to_status' => $this->record->status->value,
            'notes' => 'Permohonan baru berhasil didaftarkan ke sistem SAPA SOSIAL.',
            'user_id' => auth()->id(),
            'created_at' => Carbon::now(),
        ]);
    }
}
