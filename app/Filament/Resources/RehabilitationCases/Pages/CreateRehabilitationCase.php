<?php

namespace App\Filament\Resources\RehabilitationCases\Pages;

use App\Actions\ServiceRequest\GenerateTicketNumber;
use App\Enums\RehabilitationCaseStatus;
use App\Filament\Resources\RehabilitationCases\RehabilitationCaseResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateRehabilitationCase extends CreateRecord
{
    protected static string $resource = RehabilitationCaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['case_number'])) {
            $data['case_number'] = GenerateTicketNumber::execute('RHS');
        }

        $data['received_at'] = Carbon::now();
        $data['status'] = $data['status'] ?? RehabilitationCaseStatus::Received;

        return $data;
    }
}
