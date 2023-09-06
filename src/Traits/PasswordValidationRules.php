<?php

namespace ArtMin96\FilamentJet\Traits;

use ArtMin96\FilamentJet\Rules\Password;
use Illuminate\Contracts\Validation\Rule;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, (Rule | array | string)>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', new Password, 'confirmed'];
    }
}
