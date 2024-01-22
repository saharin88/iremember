<?php

namespace App\Traits;

use Livewire\Attributes\On;

trait AuditEditRecordTrait
{
    protected function afterSave(): void
    {
        $this->dispatch('updateAuditsRelationManager');
    }

    #[On('auditRestored')]
    public function auditRestored()
    {
        $audit = $this->record->audits()->latest()->first();
        $columns = array_keys($audit->getModified());
        foreach ($columns as $key) {
            $this->refreshFormData([$key]);
        }
    }
}
