<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet;

use ArtMin96\FilamentJet\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;
use Illuminate\Cache\Repository;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthenticationProvider implements TwoFactorAuthenticationProviderContract {
    /**
     * Create a new two factor authentication provider instance.
     *
     * @return void
     */
    public function __construct(
        /**
         * The underlying library providing two factor authentication helper services.
         */
        protected Google2FA $google2FA,
        /**
         * The cache repository implementation.
         */
        protected Repository $cacheRepository
    ) {
    }

    /**
     * Generate a new secret key.
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     *
     * @return string
     */
    public function generateSecretKey() {
        return $this->google2FA->generateSecretKey();
    }

    /**
     * Get the two factor authentication QR code URL.
     *
     * @param string $companyName
     * @param string $companyEmail
     * @param string $secret
     *
     * @return string
     */
    public function qrCodeUrl($companyName, $companyEmail, $secret) {
        return $this->google2FA->getQRCodeUrl($companyName, $companyEmail, $secret);
    }

    /**
     * Verify the given code.
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function verify($secret, $code): bool {
        if (is_int($customWindow = config('filament-jet-options.two-factor-authentication.window'))) {
            $this->google2FA->setWindow($customWindow);
        }

        /** @var int $oldTimestamp */
        $oldTimestamp = $this->cacheRepository->get($key = 'filament-jet.2fa_codes.'.md5($code));
        $timestamp = $this->google2FA->verifyKeyNewer(
            $secret,
            $code,
            $oldTimestamp
        );

        if (false !== $timestamp) {
            if (true === $timestamp) {
                $timestamp = $this->google2FA->getTimestamp();
            }

            $this->cacheRepository->put($key, $timestamp, ($this->google2FA->getWindow() ?: 1) * 60);

            return true;
        }

        return false;
    }
}
