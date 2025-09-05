<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Auth;

trait HasApprovalWorkflow
{
    public function submitForApproval(array $data = []): void
    {
        // If approved, stash changes instead of overwriting live content
        if ($this->status === 'approved') {
            $this->pending_payload = $data ?: $this->toArray();
        } else {
            if (!empty($data)) {
                $this->fill($data);
            }
        }

        $this->status = 'pending';
        $this->updated_by = Auth::id();
        $this->save();
    }

    public function approve(?string $note = null,?int $adminId = null): void
    {
        // Apply pending changes if any
        if ($this->pending_payload) {
            $this->fill($this->pending_payload);
            $this->pending_payload = null;
        }

        $this->status = 'approved';
        $this->note = $note;
        if (property_exists($this, 'is_active') || array_key_exists('is_active', $this->getAttributes())) {
            $this->is_active = 1;
        } else {
            $this->is_published = 1;
        }
        $this->published_at = $this->published_at ?? now();
        $this->updated_by = $adminId ?? Auth::id();
        $this->save();
    }

    public function reject(?string $reason = null, ?int $adminId = null): void
    {
        $this->status = 'rejected';
        $this->rejection_reason = $reason;
        $this->pending_payload = null;
        $this->updated_by = $adminId ?? Auth::id();
        $this->save();
    }

    public function isEditableByContributor(): bool
    {
        return in_array($this->status, ['draft', 'rejected'], true);
    }

    public function isDeletableByContributor(): bool
    {
        return in_array($this->status, ['draft', 'rejected'], true);
    }
}
