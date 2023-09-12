<?php

use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
<<<<<<< HEAD
        Schema::table('users', static function (Blueprint $blueprint) : void {
            $blueprint->text('two_factor_secret')
                ->after('password')
                ->nullable();
=======
        Schema::table('users', function (Blueprint $blueprint): void {
            $blueprint->text('two_factor_secret')
                ->after('password')
                ->nullable();

>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            $blueprint->text('two_factor_recovery_codes')
                ->after('two_factor_secret')
                ->nullable();
            if (FilamentJet::confirmsTwoFactorAuthentication()) {
                $blueprint->timestamp('two_factor_confirmed_at')
                    ->after('two_factor_recovery_codes')
                    ->nullable();
            }
        });
    }

    public function down(): void
    {
<<<<<<< HEAD
        Schema::table('users', static function (Blueprint $blueprint) : void {
=======
        Schema::table('users', function (Blueprint $blueprint): void {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            $blueprint->dropColumn(array_merge([
                'two_factor_secret',
                'two_factor_recovery_codes',
            ], FilamentJet::confirmsTwoFactorAuthentication() ? [
                'two_factor_confirmed_at',
            ] : []));
        });
    }
};
