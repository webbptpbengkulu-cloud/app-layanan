<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Terdaftar DTKS/DTSEN</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 20mm 20mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 2px 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 9pt;
            font-style: italic;
        }
        .title-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .title-section h4 {
            margin: 0;
            font-size: 13pt;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .title-section p {
            margin: 3px 0 0 0;
            font-size: 11pt;
        }
        .content {
            margin-bottom: 15px;
            text-align: justify;
        }
        .table-data {
            width: 100%;
            margin-left: 20px;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .table-data td {
            vertical-align: top;
            padding: 3px 4px;
            font-size: 11.5pt;
        }
        .table-data td.label {
            width: 200px;
        }
        .table-data td.separator {
            width: 15px;
            text-align: center;
        }
        .signature-section {
            width: 100%;
            margin-top: 30px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            vertical-align: top;
        }
        .signature-box {
            text-align: center;
            width: 250px;
            float: right;
        }
        .qr-box {
            text-align: center;
            width: 200px;
            float: left;
            border: 1px dashed #666;
            padding: 8px;
            font-size: 8pt;
        }
        .qr-box img {
            width: 90px;
            height: 90px;
        }
        .footer-note {
            margin-top: 40px;
            font-size: 8.5pt;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>PEMERINTAH KABUPATEN BLITAR</h3>
        <h2>DINAS SOSIAL</h2>
        <p>Jl. Raya Kanigoro No. 01, Kanigoro, Kabupaten Blitar, Jawa Timur 66171<br>Telepon: (0342) 801234 · Laman: dinsos.blitarkab.go.id · Pos-el: dinsos@blitarkab.go.id</p>
    </div>

    <div class="title-section">
        <h4>SURAT KETERANGAN TERDAFTAR DTKS / DTSEN</h4>
        <p>Nomor: {{ $certificate->certificate_number ?? '400.9.1/' . ($certificate->id ?? '---') . '/409.106/' . date('Y') }}</p>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini Kepala Dinas Sosial Kabupaten Blitar, menerangkan dengan sebenarnya bahwa:</p>
    </div>

    <table class="table-data">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="separator">:</td>
            <td><strong>{{ $certificate->subject_name }}</strong></td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="separator">:</td>
            <td>{{ $certificate->subject_nik }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Kartu Keluarga</td>
            <td class="separator">:</td>
            <td>{{ $request->family_card_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat / Domisili</td>
            <td class="separator">:</td>
            <td>{{ $request->address ?? '-' }}, Desa {{ $request->village?->name ?? '-' }}, Kec. {{ $request->village?->district?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Pemohon</td>
            <td class="separator">:</td>
            <td>{{ $request->applicant_name }} ({{ $certificate->relationship_to_applicant ?? 'Diri Sendiri' }})</td>
        </tr>
    </table>

    <div class="content">
        <p>Berdasarkan hasil pemadanan data pada Sistem Informasi Kesejahteraan Sosial Next Generation (SIKS-NG) / Data Terpadu Kesejahteraan Sosial (DTKS) Kementerian Sosial Republik Indonesia, yang bersangkutan tercatat:</p>
    </div>

    <table class="table-data">
        <tr>
            <td class="label">Status Kepesertaan</td>
            <td class="separator">:</td>
            <td><strong style="color: #0d6832;">TERDAFTAR DALAM DTKS / DTSEN</strong></td>
        </tr>
        <tr>
            <td class="label">Peringkat Desil</td>
            <td class="separator">:</td>
            <td><strong>Desil {{ $certificate->decile }}</strong></td>
        </tr>
        <tr>
            <td class="label">Tujuan Penggunaan</td>
            <td class="separator">:</td>
            <td>{{ $certificate->dtsenPurpose?->name ?? $certificate->purpose_description }}</td>
        </tr>
        <tr>
            <td class="label">Masa Berlaku Surat</td>
            <td class="separator">:</td>
            <td>Sampai dengan {{ \Carbon\Carbon::parse($certificate->valid_until)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <div class="content">
        <p>Demikian Surat Keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya sesuai ketentuan yang berlaku.</p>
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="width: 50%;">
                    @if(!empty($qrCodeSvgBase64))
                    <div class="qr-box">
                        <img src="data:image/svg+xml;base64,{{ $qrCodeSvgBase64 }}" alt="QR Verifikasi"><br>
                        <strong>PINDAI UNTUK VERIFIKASI</strong><br>
                        Kode: {{ $certificate->verification_code }}
                    </div>
                    @endif
                </td>
                <td style="width: 50%;">
                    <div class="signature-box">
                        Blitar, {{ \Carbon\Carbon::parse($certificate->issued_at ?? now())->translatedFormat('d F Y') }}<br>
                        <strong>KEPALA DINAS SOSIAL<br>KABUPATEN BLITAR</strong><br><br><br><br>
                        <u><strong>{{ $certificate->signer?->name ?? 'Drs. H. BAMBANG HERMANTO, M.Si' }}</strong></u><br>
                        Pembina Utama Muda<br>
                        NIP. {{ $certificate->signer?->nik ?? '197005101995031001' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-note" style="clear: both;">
        <p><em>Dokumen ini diterbitkan secara elektronik oleh Sistem SAPA SOSIAL Dinas Sosial Kabupaten Blitar dan dapat diverifikasi keasliannya melalui portal resmi dengan memindai kode QR atau memasukkan Kode Verifikasi di atas.</em></p>
    </div>

</body>
</html>
