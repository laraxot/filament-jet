<?php

namespace ArtMin96\FilamentJet\Contracts;

/**
 * ArtMin96\FilamentJet\Contracts\HasProfilePhotoContract
 */
interface HasProfilePhotoContract
{
    public function getFilamentAvatarUrl(): ?string;

    /**
     * Update the user's profile photo.
     */
    public function updateProfilePhoto(?string $photo): void;

    /**
     * Delete the user's profile photo.
     */
    public function deleteProfilePhoto(): void;

    /**
     * Get the URL to the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute(): string;

    /**
     * Determine if the image file exists.
     */
    public function photoExists(): bool;

    public function filamentDefaultAvatar(): string;

    /**
     * Get the disk that profile photos should be stored on.
     */
    public function profilePhotoDisk(): string;

    /**
     * Get the directory that profile photos should be stored on.
     */
    public function profilePhotoDirectory(): string;
}
