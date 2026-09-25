<?php

namespace App\Actions\ServiceRequest;

use App\Enums\ServiceRequestStatus;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransitionStatus
{
    /**
     * Transition the status of a service request and log history.
     */
    public static function execute(
        ServiceRequest $serviceRequest,
        ServiceRequestStatus|string $newStatus,
        ?string $notes = null,
        ?int $userId = null
    ): ServiceRequest {
        $toStatus = is_string($newStatus) ? ServiceRequestStatus::from($newStatus) : $newStatus;
        $fromStatus = $serviceRequest->status;

        return DB::transaction(function () use ($serviceRequest, $fromStatus, $toStatus, $notes, $userId) {
            $serviceRequest->status = $toStatus;

            if (in_array($toStatus, [ServiceRequestStatus::Completed, ServiceRequestStatus::Issued], true)) {
                $serviceRequest->completed_at = Carbon::now();
            }

            $serviceRequest->save();

            $serviceRequest->statusHistories()->create([
                'from_status' => $fromStatus?->value,
                'to_status' => $toStatus->value,
                'notes' => $notes,
                'user_id' => $userId ?? auth()->id(),
                'created_at' => Carbon::now(),
            ]);

            return $serviceRequest;
        });
    }
}
