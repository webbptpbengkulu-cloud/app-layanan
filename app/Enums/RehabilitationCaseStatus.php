<?php

namespace App\Enums;

enum RehabilitationCaseStatus: string
{
    case Received = 'received';
    case Assessment = 'assessment';
    case ServicePlanning = 'service_planning';
    case InService = 'in_service';
    case Monitoring = 'monitoring';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Received => 'Diterima',
            self::Assessment => 'Assessment',
            self::ServicePlanning => 'Rencana Pelayanan',
            self::InService => 'Dalam Pelayanan',
            self::Monitoring => 'Monitoring',
            self::Closed => 'Ditutup / Selesai',
        };
    }
}
