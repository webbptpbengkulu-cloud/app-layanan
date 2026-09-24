<?php

namespace App\Enums;

enum ApprovalDecision: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Persetujuan',
            self::Approved => 'Disetujui',
            self::Returned => 'Dikembalikan / Ditolak',
        };
    }
}
