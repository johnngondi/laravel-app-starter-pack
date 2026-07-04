<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Gives a model an optional profile photo — a user's display picture or an
 * organisation's logo — stored on a public disk. Adapted from Jetstream's
 * trait so it has no Jetstream/Features dependency.
 *
 * Requires a nullable `profile_photo_path` column and a `name` attribute (used
 * to build the default avatar).
 *
 * @property string|null $profile_photo_path
 * @property-read string $profile_photo_url
 */
trait HasProfilePhoto
{
    /**
     * Store the given upload as the model's profile photo, replacing and
     * deleting any previous one.
     */
    public function updateProfilePhoto(UploadedFile $photo, string $storagePath = 'profile-photos'): void
    {
        tap($this->profile_photo_path, function (?string $previous) use ($photo, $storagePath): void {
            $this->forceFill([
                'profile_photo_path' => $photo->storePublicly($storagePath, ['disk' => $this->profilePhotoDisk()]),
            ])->save();

            if ($previous) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the model's profile photo.
     */
    public function deleteProfilePhoto(): void
    {
        if (is_null($this->profile_photo_path)) {
            return;
        }

        Storage::disk($this->profilePhotoDisk())->delete($this->profile_photo_path);

        $this->forceFill(['profile_photo_path' => null])->save();
    }

    /**
     * Whether a profile photo has been uploaded.
     */
    public function hasProfilePhoto(): bool
    {
        return ! is_null($this->profile_photo_path);
    }

    /**
     * The URL to the profile photo, falling back to a generated avatar.
     *
     * @return Attribute<string, never>
     */
    public function profilePhotoUrl(): Attribute
    {
        return Attribute::get(fn (): string => $this->profile_photo_path
            ? Storage::disk($this->profilePhotoDisk())->url($this->profile_photo_path)
            : $this->defaultProfilePhotoUrl());
    }

    /**
     * The generated avatar URL used when no photo has been uploaded.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        $initials = trim(collect(explode(' ', (string) $this->name))
            ->map(fn (string $segment): string => Str::substr($segment, 0, 1))
            ->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($initials).'&color=FFFFFF&background=1E3A8A';
    }

    /**
     * The disk profile photos are stored on.
     */
    protected function profilePhotoDisk(): string
    {
        return config('filesystems.profile_photo_disk', 'public');
    }
}
