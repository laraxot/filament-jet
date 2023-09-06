<?php

namespace ArtMin96\FilamentJet\Traits;

use ArtMin96\FilamentJet\Contracts\TwoFactorAuthenticationProvider;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\RecoveryCode;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Exception;

/**
 * @property string $two_factor_confirmed_at
 */
trait TwoFactorAuthenticatable
{
    /**
     * Determine if two-factor authentication has been enabled.
     */
    public function hasEnabledTwoFactorAuthentication(): bool
    {
        if (FilamentJet::confirmsTwoFactorAuthentication()) {
            return ! is_null($this->two_factor_secret) &&
                ! is_null($this->two_factor_confirmed_at);
        }

        return ! is_null($this->two_factor_secret);
    }

    public function hasConfirmedTwoFactorAuthentication(): bool
    {
        if (FilamentJet::confirmsTwoFactorAuthentication()) {
            return ! is_null($this->two_factor_confirmed_at);
        }

        return false;
    }

    /**
     * Get the user's two factor authentication recovery codes.
     *
     * @return array
     */
    public function recoveryCodes()
    {
        if ($this->two_factor_recovery_codes === null) {
            throw new Exception('['.__LINE__.']['.__FILE__.']');
        }

        return (array) json_decode((string) decrypt($this->two_factor_recovery_codes), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Replace the given recovery code with a new one in the user's stored codes.
     *
     * @param  string  $code
     * @return void
     */
    public function replaceRecoveryCode($code)
    {
        if ($code === null) {
            throw new Exception('['.__LINE__.']['.__FILE__.']');
        }

        if ($this->two_factor_recovery_codes === null) {
            throw new Exception('['.__LINE__.']['.__FILE__.']');
        }

        $this->forceFill([
            'two_factor_recovery_codes' => encrypt(str_replace(
                $code,
                RecoveryCode::generate(),
                (string) decrypt($this->two_factor_recovery_codes)
            )),
        ])->save();
    }

    /**
     * Get the QR code SVG of the user's two factor authentication QR code URL.
     */
    public function twoFactorQrCodeSvg(): string
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd
            )
        ))->writeString($this->twoFactorQrCodeUrl());

        return trim(substr((string) $svg, strpos((string) $svg, "\n") + 1));
    }

    /**
     * Get the two factor authentication QR code URL.
     *
     * @return string
     */
    public function twoFactorQrCodeUrl()
    {
        $app_name = (string) config('app.name');
        if ($app_name === null) {
            throw new Exception('['.__LINE__.']['.__FILE__.']');
        }

        if ($this->two_factor_secret === null) {
            throw new Exception('['.__LINE__.']['.__FILE__.']');
        }

        return app(TwoFactorAuthenticationProvider::class)->qrCodeUrl(
            // config('app.name'),
            $app_name,
            $this->{FilamentJet::username()},
            (string) decrypt($this->two_factor_secret)
        );
    }
}
