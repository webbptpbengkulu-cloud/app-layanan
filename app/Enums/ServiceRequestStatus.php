<?php

namespace App\Enums;

enum ServiceRequestStatus: string
{
    case Submitted = 'submitted';
    case DocumentCheck = 'document_check';
    case RevisionRequested = 'revision_requested';
    case DataVerification = 'data_verification';
    case EligibilityVerification = 'eligibility_verification';
    case Verification = 'verification';
    case Assessment = 'assessment';
    case AwaitingApproval = 'awaiting_approval';
    case RecommendationIssued = 'recommendation_issued';
    case ProposedToMinistry = 'proposed_to_ministry';
    case MinistryApproved = 'ministry_approved';
    case MinistryRejected = 'ministry_rejected';
    case Reactivated = 'reactivated';
    case Issued = 'issued';
    case InProcess = 'in_process';
    case Completed = 'completed';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Diajukan',
            self::DocumentCheck => 'Pemeriksaan Berkas',
            self::RevisionRequested => 'Perlu Perbaikan',
            self::DataVerification => 'Verifikasi Data',
            self::EligibilityVerification => 'Verifikasi Kelayakan',
            self::Verification => 'Verifikasi',
            self::Assessment => 'Assessment',
            self::AwaitingApproval => 'Menunggu Tanda Tangan / Persetujuan',
            self::RecommendationIssued => 'Rekomendasi Terbit',
            self::ProposedToMinistry => 'Diusulkan ke Kemensos',
            self::MinistryApproved => 'Disetujui Kemensos',
            self::MinistryRejected => 'Ditolak Kemensos',
            self::Reactivated => 'Aktif Kembali',
            self::Issued => 'Surat Terbit',
            self::InProcess => 'Sedang Diproses',
            self::Completed => 'Selesai',
            self::Rejected => 'Ditolak',
        };
    }
}
