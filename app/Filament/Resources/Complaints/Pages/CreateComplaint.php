<?php

namespace App\Filament\Resources\Complaints\Pages;

use App\Actions\ServiceRequest\GenerateTicketNumber;
use App\Enums\ComplaintStatus;
use App\Filament\Resources\Complaints\ComplaintResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateComplaint extends CreateRecord
{
    protected static string $resource = ComplaintResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['complaint_number'])) {
            $data['complaint_number'] = GenerateTicketNumber::execute('ADU');
        }

        $data['reported_at'] = Carbon::now();
        $data['reporter_id'] = auth()->id() ?? $data['reporter_id'] ?? null;
        $data['status'] = $data['status'] ?? ComplaintStatus::Received;

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->statusHistories()->create([
            'to_status' => $this->record->status->value,
            'notes' => 'Laporan pengaduan masyarakat berhasil diterima sistem.',
            'user_id' => auth()->id(),
            'created_at' => Carbon::now(),
        ]);
    }
}
