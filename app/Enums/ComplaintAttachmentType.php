<?php

namespace App\Enums;

enum ComplaintAttachmentType: string
{
    case Photo = 'photo';
    case Document = 'document';

    public function label(): string
    {
        return match ($this) {
            self::Photo => 'Foto / Gambar',
            self::Document => 'Dokumen Pendukung',
        };
    }
}
