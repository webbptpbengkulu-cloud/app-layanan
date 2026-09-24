<?php

namespace Tests\Unit;

use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\Assessment;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\Disposition;
use App\Models\District;
use App\Models\DownloadableForm;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\Faq;
use App\Models\InformationPage;
use App\Models\MonitoringRecord;
use App\Models\NumberSequence;
use App\Models\PageVisit;
use App\Models\PbiReactivation;
use App\Models\Referral;
use App\Models\ReferralInstitution;
use App\Models\RehabilitationCase;
use App\Models\SearchLog;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestDocument;
use App\Models\ServiceRequirement;
use App\Models\ServiceType;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_models_can_query_database(): void
    {
        $models = [
            WorkUnit::class,
            District::class,
            Village::class,
            User::class,
            ServiceType::class,
            ServiceRequirement::class,
            ServiceRequest::class,
            ServiceRequestDocument::class,
            DtsenPurpose::class,
            DtsenCertificate::class,
            PbiReactivation::class,
            Approval::class,
            ComplaintCategory::class,
            Complaint::class,
            ComplaintAttachment::class,
            ClientCategory::class,
            Client::class,
            RehabilitationCase::class,
            Assessment::class,
            ReferralInstitution::class,
            Referral::class,
            MonitoringRecord::class,
            InformationPage::class,
            DownloadableForm::class,
            Faq::class,
            PageVisit::class,
            SearchLog::class,
            StatusHistory::class,
            Disposition::class,
            NumberSequence::class,
            ActivityLog::class,
        ];

        foreach ($models as $modelClass) {
            $count = $modelClass::count();
            $this->assertIsInt($count, "Failed querying count on {$modelClass}");
        }
    }

    public function test_number_sequence_generates_code(): void
    {
        $prefix = 'TEST_'.uniqid();
        $code1 = NumberSequence::generateCode($prefix, 5, '202609');
        $this->assertSame($prefix.'-202609-00001', $code1);

        $code2 = NumberSequence::generateCode($prefix, 5, '202609');
        $this->assertSame($prefix.'-202609-00002', $code2);
    }
}
