<?php

namespace App\Policies;

use App\Models\Upload;
use App\Models\User;

/**
 * Upload authorization. Every ability returns true for now — the methods are
 * in place so permissions (e.g. Spatie roles) can be layered on later without
 * touching the controllers or views that reference them.
 */
class UploadPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Upload $upload): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Upload $upload): bool
    {
        return true;
    }

    public function delete(User $user, Upload $upload): bool
    {
        return true;
    }

    public function download(User $user, Upload $upload): bool
    {
        return true;
    }

    public function restore(User $user, Upload $upload): bool
    {
        return true;
    }

    public function forceDelete(User $user, Upload $upload): bool
    {
        return true;
    }
}
