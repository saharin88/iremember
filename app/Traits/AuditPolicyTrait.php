<?php

namespace App\Traits;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait AuditPolicyTrait
{
    public function audit(User $user, Model $person): bool
    {
        return $user->role === Role::ADMINISTRATOR || $user->role === Role::EDITOR;
    }

    public function restoreAudit(User $user, Model $person): bool
    {
        return $user->role === Role::ADMINISTRATOR;
    }
}
