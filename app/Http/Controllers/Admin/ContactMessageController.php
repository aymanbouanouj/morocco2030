<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ContactMessages\UpdateContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(ContactMessage::class, 'contactMessage');
    }

    public function index(Request $request): View
    {
        $messages = ContactMessage::query()
            ->with(['user', 'language', 'assignedTo'])
            ->when($request->string('search')->toString(), function ($query, string $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('subject', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn ($query, string $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.contact-messages.index', [
            'messages' => $messages,
            'filters' => $request->only(['search', 'status']),
            'statuses' => $this->statuses(),
        ]);
    }

    public function show(ContactMessage $contactMessage): View
    {
        $contactMessage->load(['user', 'language', 'assignedTo']);

        return view('admin.contact-messages.show', [
            'contactMessage' => $contactMessage,
            'staffUsers' => User::query()->staff()->where('status', 'active')->orderBy('name')->get(['id', 'name', 'email']),
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(UpdateContactMessageRequest $request, ContactMessage $contactMessage): RedirectResponse
    {
        $original = $this->auditPayload($contactMessage);
        $validated = $request->validated();
        $meta = $contactMessage->meta ?? [];

        if (($validated['internal_note'] ?? null) !== null) {
            $meta['internal_note'] = $validated['internal_note'];
            $meta['internal_note_updated_at'] = now()->toDateTimeString();
        }

        $contactMessage->update([
            'status' => $validated['status'],
            'assigned_to' => $validated['assigned_to'] ?? null,
            'responded_at' => in_array($validated['status'], ['resolved', 'archived'], true)
                ? ($contactMessage->responded_at ?? now())
                : $contactMessage->responded_at,
            'meta' => $meta,
        ]);

        $this->recordAudit(
            $request,
            $contactMessage,
            'contact-messages.updated',
            $original,
            $this->auditPayload($contactMessage->fresh())
        );

        return redirect()->route('admin.contact-messages.show', $contactMessage)
            ->with('success', 'Contact message updated successfully.');
    }

    /**
     * @return array<int, string>
     */
    private function statuses(): array
    {
        return ['new', 'read', 'in_progress', 'resolved', 'archived'];
    }

    /**
     * @return array<string, mixed>
     */
    private function auditPayload(ContactMessage $contactMessage): array
    {
        return [
            'status' => $contactMessage->status,
            'assigned_to' => $contactMessage->assigned_to,
            'responded_at' => $contactMessage->responded_at?->toDateTimeString(),
            'internal_note' => $contactMessage->meta['internal_note'] ?? null,
        ];
    }
}
