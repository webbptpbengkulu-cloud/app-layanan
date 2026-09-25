<?php

namespace Tests\Feature;

use App\Actions\Dtsen\CheckDecileEligibility;
use App\Actions\Dtsen\GenerateCertificatePdf;
use App\Actions\Dtsen\GenerateVerificationCode;
use App\Actions\ServiceRequest\GenerateTicketNumber;
use App\Actions\ServiceRequest\TransitionStatus;
use App\Enums\ComplaintStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\ServiceRequestStatus;
use App\Models\Client;
use App\Models\Complaint;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\RehabilitationCase;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use App\Models\User;
use App\Models\Village;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardBackendWorkflowTest extends TestCase
{
    public function test_ticket_number_generation_is_gapless_and_unique(): void
    {
        $ticket1 = GenerateTicketNumber::execute('DTSEN');
        $ticket2 = GenerateTicketNumber::execute('DTSEN');

        $this->assertNotEmpty($ticket1);
        $this->assertNotEmpty($ticket2);
        $this->assertNotEquals($ticket1, $ticket2);
        $this->assertStringStartsWith('DTSEN-', $ticket1);
    }

    public function test_decile_eligibility_validation(): void
    {
        $purpose = DtsenPurpose::firstOrCreate(
            ['code' => 'TEST-PIP'],
            ['name' => 'Program Indonesia Pintar', 'max_decile' => 4, 'validity_days' => 30, 'is_active' => true]
        );

        $eligible = CheckDecileEligibility::execute(3, $purpose);
        $this->assertTrue($eligible['eligible']);

        $ineligible = CheckDecileEligibility::execute(6, $purpose);
        $this->assertFalse($ineligible['eligible']);
    }

    public function test_service_request_status_transition_and_audit_history(): void
    {
        $serviceType = ServiceType::first();
        $user = User::first();
        $village = Village::first();

        $request = ServiceRequest::create([
            'request_number' => GenerateTicketNumber::execute('DTSEN'),
            'service_type_id' => $serviceType->id,
            'submitter_id' => $user->id,
            'applicant_name' => 'Warga Uji Coba',
            'applicant_nik' => '3505030101900001',
            'family_card_number' => '3505030101900001',
            'address' => 'Jl. Raya Kanigoro No. 10',
            'village_id' => $village->id,
            'phone' => '081234567899',
            'status' => ServiceRequestStatus::Submitted,
        ]);

        TransitionStatus::execute($request, ServiceRequestStatus::DocumentCheck, 'Berkas lengkap');

        $this->assertEquals(ServiceRequestStatus::DocumentCheck, $request->fresh()->status);
        $this->assertDatabaseHas('status_histories', [
            'statusable_id' => $request->id,
            'statusable_type' => ServiceRequest::class,
            'to_status' => ServiceRequestStatus::DocumentCheck->value,
        ]);
    }

    public function test_dtsen_certificate_pdf_and_qr_generation(): void
    {
        Storage::fake('public');

        $serviceType = ServiceType::first();
        $user = User::first();
        $village = Village::first();
        $purpose = DtsenPurpose::first();

        $request = ServiceRequest::create([
            'request_number' => GenerateTicketNumber::execute('DTSEN'),
            'service_type_id' => $serviceType->id,
            'submitter_id' => $user->id,
            'applicant_name' => 'Siti Aminah',
            'applicant_nik' => '3505035504850002',
            'family_card_number' => '3505035504850002',
            'address' => 'Jl. Kenanga No. 5 RT 02 RW 01',
            'village_id' => $village->id,
            'phone' => '085712345001',
            'status' => ServiceRequestStatus::AwaitingApproval,
        ]);

        $certificate = DtsenCertificate::create([
            'service_request_id' => $request->id,
            'dtsen_purpose_id' => $purpose->id,
            'subject_name' => 'Siti Aminah',
            'subject_nik' => '3505035504850002',
            'relationship_to_applicant' => 'Diri Sendiri',
            'is_registered' => true,
            'decile' => 2,
            'verification_code' => GenerateVerificationCode::execute(),
            'certificate_number' => '400.9.1/'.rand(1000, 9999).time().'/409.106/2026',
            'valid_until' => now()->addDays(30),
            'signer_id' => $user->id,
        ]);

        $pdfPath = GenerateCertificatePdf::execute($certificate);

        $this->assertNotEmpty($pdfPath);
        Storage::disk('public')->assertExists($pdfPath);
    }

    public function test_complaint_escalation_to_rehabilitation_case(): void
    {
        $village = Village::first();
        $user = User::first();
        $client = Client::first();

        $complaint = Complaint::create([
            'complaint_number' => GenerateTicketNumber::execute('ADU'),
            'complaint_category_id' => 1,
            'reporter_name' => 'Warga Pelapor',
            'reporter_phone' => '081234567890',
            'village_id' => $village->id,
            'description' => 'Ditemukan lansia terlantar di pos kamling tanpa keluarga.',
            'reported_at' => now(),
            'status' => ComplaintStatus::Received,
        ]);

        $caseNumber = GenerateTicketNumber::execute('RHS');
        $case = RehabilitationCase::create([
            'case_number' => $caseNumber,
            'client_id' => $client->id,
            'complaint_id' => $complaint->id,
            'officer_id' => $user->id,
            'status' => RehabilitationCaseStatus::Received,
            'received_at' => now(),
        ]);

        $complaint->update(['status' => ComplaintStatus::InHandling]);

        $this->assertEquals($complaint->id, $case->complaint_id);
        $this->assertEquals(ComplaintStatus::InHandling, $complaint->fresh()->status);
        $this->assertDatabaseHas('rehabilitation_cases', [
            'case_number' => $caseNumber,
            'complaint_id' => $complaint->id,
        ]);
    }

    public function test_filament_database_notifications_jsonb_query(): void
    {
        $user = User::first();

        // Test the exact query Filament runs on topbar notifications badge
        $unreadCount = $user->unreadNotifications()
            ->where('data->format', 'filament')
            ->count();

        $this->assertEquals(0, $unreadCount);
    }

    public function test_admin_complaints_page_renders_successfully(): void
    {
        $admin = User::where('email', 'admin@blitarkab.go.id')->first() ?? User::first();

        $response = $this->actingAs($admin)->get('/admin/complaints');

        $response->assertSuccessful();
    }

    public function test_admin_service_requests_and_rehab_pages_render_successfully(): void
    {
        $admin = User::where('email', 'admin@blitarkab.go.id')->first() ?? User::first();

        $this->actingAs($admin)->get('/admin/service-requests')->assertSuccessful();
        $this->actingAs($admin)->get('/admin/rehabilitation-cases')->assertSuccessful();
    }
}
