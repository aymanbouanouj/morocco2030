<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Support\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class SecurityBaselineTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_audit_logger_redacts_sensitive_old_and_new_values(): void
    {
        $user = $this->makeStaffUser();
        $request = Request::create('/admin/security-audit-test', 'POST', server: [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'Feature Test',
        ]);
        $request->setUserResolver(fn () => $user);

        AuditLogger::log(
            $request,
            $user,
            'security.redaction.test',
            [
                'email' => 'admin@example.com',
                'password' => 'plain-secret',
                'nested' => [
                    'remember_token' => 'remember-me',
                ],
            ],
            [
                'name' => 'Admin User',
                'password_confirmation' => 'plain-secret',
                'api_key' => 'key-123',
                'metadata' => [
                    'secret' => 'hidden',
                    'safe' => 'visible',
                ],
            ]
        );

        $auditLog = AuditLog::query()->firstOrFail();

        $this->assertSame('admin@example.com', $auditLog->old_values['email']);
        $this->assertSame('[redacted]', $auditLog->old_values['password']);
        $this->assertSame('[redacted]', $auditLog->old_values['nested']['remember_token']);
        $this->assertSame('Admin User', $auditLog->new_values['name']);
        $this->assertSame('[redacted]', $auditLog->new_values['password_confirmation']);
        $this->assertSame('[redacted]', $auditLog->new_values['api_key']);
        $this->assertSame('[redacted]', $auditLog->new_values['metadata']['secret']);
        $this->assertSame('visible', $auditLog->new_values['metadata']['safe']);
    }
}
