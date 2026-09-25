<?php

namespace App\Actions\Dtsen;

use App\Models\DtsenCertificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateCertificatePdf
{
    /**
     * Generate official PDF certificate with embedded QR verification code.
     */
    public static function execute(DtsenCertificate $certificate): string
    {
        $request = $certificate->serviceRequest;

        // Generate verification URL
        $verificationUrl = url('/verifikasi/'.$certificate->verification_code);

        // Generate QR code SVG as base64
        $qrCodeSvg = QrCode::format('svg')
            ->size(140)
            ->errorCorrection('H')
            ->generate($verificationUrl);

        $qrCodeSvgBase64 = base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.dtsen_certificate', [
            'certificate' => $certificate,
            'request' => $request,
            'qrCodeSvgBase64' => $qrCodeSvgBase64,
            'verificationUrl' => $verificationUrl,
        ])->setPaper('a4', 'portrait');

        $fileName = 'certificates/'.$certificate->verification_code.'.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        $certificate->file_path = $fileName;
        $certificate->save();

        return $fileName;
    }
}
