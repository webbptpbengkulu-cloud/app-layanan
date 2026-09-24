<?php

namespace App\Enums;

enum ComplaintStatus: string
{
    case Received = 'received';
    case Verification = 'verification';
    case ClarificationRequested = 'clarification_requested';
    case Dispatched = 'dispatched';
    case InHandling = 'in_handling';
    case Resolved = 'resolved';
    case Duplicate = 'duplicate';
    case Invalid = 'invalid';

    public function label(): string
    {
        return match ($this) {
            self::Received => 'Diterima',
            self::Verification => 'Verifikasi Awal',
            self::ClarificationRequested => 'Permintaan Klarifikasi',
            self::Dispatched => 'Didisposisikan',
            self::InHandling => 'Dalam Penanganan',
            self::Resolved => 'Selesai Ditangani',
            self::Duplicate => 'Laporan Duplikat',
            self::Invalid => 'Tidak Valid / Fiktif',
        };
    }
}
