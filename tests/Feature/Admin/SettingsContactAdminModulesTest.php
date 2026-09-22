<?php

namespace Tests\Feature\Admin;

use App\Models\ContactMessage;
use App\Models\Language;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class SettingsContactAdminModulesTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_settings_admin_routes_are_protected_and_permission_controlled(): void
    {
        $setting = $this->makeSetting();

        $this->get(route('admin.settings.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($this->makePublicUser())
            ->get(route('admin.settings.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser())
            ->get(route('admin.settings.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser(['settings.manage']))
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee($setting->setting_key);
    }

    public function test_authorized_staff_can_update_safe_settings(): void
    {
        $user = $this->makeStaffUser(['settings.manage']);
        $setting = $this->makeSetting([
            'value' => 'Old public tagline',
            'description' => 'Old description',
        ]);

        $this->actingAs($user)
            ->put(route('admin.settings.update', $setting), [
                'value' => 'New public tagline',
                'is_public' => '1',
                'autoload' => '0',
                'description' => 'Updated public description.',
            ])
            ->assertRedirect(route('admin.settings.index'));

        $setting->refresh();

        $this->assertSame('New public tagline', $setting->value);
        $this->assertTrue($setting->is_public);
        $this->assertFalse($setting->autoload);
        $this->assertSame('Updated public description.', $setting->description);
    }

    public function test_sensitive_settings_cannot_be_edited_from_admin_ui(): void
    {
        $user = $this->makeStaffUser(['settings.manage']);
        $setting = $this->makeSetting([
            'setting_key' => 'db_password',
            'value' => 'plain-secret',
            'is_public' => false,
        ]);

        $this->actingAs($user)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('[protected]')
            ->assertDontSee('plain-secret');

        $this->actingAs($user)
            ->put(route('admin.settings.update', $setting), [
                'value' => 'changed-secret',
                'is_public' => '0',
                'autoload' => '0',
                'description' => 'Should remain blocked.',
            ])
            ->assertForbidden();

        $this->assertSame('plain-secret', $setting->fresh()->value);
    }

    public function test_contact_message_admin_routes_are_protected_and_permission_controlled(): void
    {
        $message = $this->makeContactMessage();

        $this->get(route('admin.contact-messages.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($this->makePublicUser())
            ->get(route('admin.contact-messages.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser())
            ->get(route('admin.contact-messages.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser(['contact-messages.manage']))
            ->get(route('admin.contact-messages.index'))
            ->assertOk()
            ->assertSee($message->subject);
    }

    public function test_authorized_staff_can_review_and_update_contact_message_status(): void
    {
        $handler = $this->makeStaffUser(['contact-messages.manage'], [
            'name' => 'Support Handler',
            'email' => 'support-handler@example.com',
        ]);
        $message = $this->makeContactMessage();

        $this->actingAs($handler)
            ->get(route('admin.contact-messages.show', $message))
            ->assertOk()
            ->assertSee($message->email)
            ->assertSee($message->message);

        $this->actingAs($handler)
            ->put(route('admin.contact-messages.update', $message), [
                'status' => 'resolved',
                'assigned_to' => $handler->id,
                'internal_note' => 'Handled during admin QA.',
            ])
            ->assertRedirect(route('admin.contact-messages.show', $message));

        $message->refresh();

        $this->assertSame('resolved', $message->status);
        $this->assertSame($handler->id, $message->assigned_to);
        $this->assertSame('Handled during admin QA.', $message->meta['internal_note']);
        $this->assertNotNull($message->responded_at);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeSetting(array $attributes = []): Setting
    {
        return Setting::query()->create([
            'group_name' => $attributes['group_name'] ?? 'platform',
            'setting_key' => $attributes['setting_key'] ?? 'site_tagline',
            'value' => $attributes['value'] ?? 'Original value',
            'type' => $attributes['type'] ?? 'string',
            'is_public' => $attributes['is_public'] ?? true,
            'autoload' => $attributes['autoload'] ?? true,
            'description' => $attributes['description'] ?? 'Public setting for admin tests.',
        ]);
    }

    private function makeContactMessage(): ContactMessage
    {
        $language = Language::query()->create([
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en',
            'locale' => 'en-MA',
            'direction' => 'ltr',
            'is_active' => true,
            'is_default' => true,
            'sort_order' => 1,
        ]);

        return ContactMessage::query()->create([
            'language_id' => $language->id,
            'subject' => 'Accessibility question',
            'name' => 'Public Visitor',
            'email' => 'visitor@example.com',
            'phone' => '+212600000000',
            'message' => 'I need information about accessible stadium access.',
            'source' => 'contact_form',
            'status' => 'new',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
