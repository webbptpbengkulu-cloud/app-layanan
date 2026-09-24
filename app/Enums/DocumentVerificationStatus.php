<?php

namespace App\Enums;

enum DocumentVerificationStatus: string
{
    case Pending = 'pending';
    case Valid = 'valid';
    case RevisionNeeded = 'revision_needed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Verifikasi',
            self::Valid => 'Sesuai / Valid',
            self::RevisionNeeded => 'Perlu Perbaikan',
        };
    }
}
