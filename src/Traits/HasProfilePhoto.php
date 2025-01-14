<?php

namespace ArtMin96\FilamentJet\Traits;

use ArtMin96\FilamentJet\Features;
use Illuminate\Support\Facades\Storage;

trait HasProfilePhoto
{
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profile_photo_url;
    }

    /**
     * Update the user's profile photo.
     */
    public function updateProfilePhoto(?string $photo): void
    {
        tap($this->profile_photo_path, function ($previous) use ($photo) {
            $this->forceFill([
                'profile_photo_path' => $photo,
            ])->save();

            if ($previous && ! $photo) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the user's profile photo.
     */
    public function deleteProfilePhoto(): void
    {
        if (! Features::managesProfilePhotos()) {
            return;
        }

        if (is_null($this->profile_photo_path)) {
            return;
        }

        Storage::disk($this->profilePhotoDisk())->delete($this->profile_photo_path);

        $this->forceFill([
            'profile_photo_path' => null,
        ])->save();
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_path && $this->photoExists()
            ? Storage::disk($this->profilePhotoDisk())->url($this->profile_photo_path)
            : $this->filamentDefaultAvatar();
    }

    /**
     * Determine if the image file exists.
     */
    public function photoExists(): bool
    {
        if ($this->profile_photo_path == null) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }

        return Storage::disk($this->profilePhotoDisk())->exists($this->profile_photo_path);
    }

    public function filamentDefaultAvatar(): string
    {
        return app(config('filament.default_avatar_provider'))->get($this);
    }

    /**
     * Get the disk that profile photos should be stored on.
     */
    public function profilePhotoDisk(): string
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : config('filament-jet.profile_photo_disk', 'public');
    }

    /**
     * Get the directory that profile photos should be stored on.
     */
    public function profilePhotoDirectory(): string
    {
        return (string) config('filament-jet.profile_photo_directory', 'profile-photos');
    }
}
