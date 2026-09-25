<?php

namespace App\Actions\Dtsen;

use App\Models\DtsenCertificate;
use Illuminate\Support\Str;

class GenerateVerificationCode
{
    /**
     * Generate unique verification code for SK DTSEN QR and portal lookup.
     */
    public static function execute(): string
    {
        do {
            $code = 'DTSEN-'.strtoupper(Str::random(10));
        } while (DtsenCertificate::where('verification_code', $code)->exists());

        return $code;
    }
}
