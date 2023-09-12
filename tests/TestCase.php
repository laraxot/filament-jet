<?php

declare(strict_types=1);

namespace ArtMin96\FilamentJet\Tests;

use ArtMin96\FilamentJet\FilamentJetServiceProvider;
use Filament\FilamentServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra {
    protected function setUp(): void {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName): string => 'ArtMin96\\FilamentJet\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    public function getEnvironmentSetUp($app): void {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_filament-jet_table.php.stub';
        $migration->up();
        */
    }

    protected function getPackageProviders($app): array {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            FilamentJetServiceProvider::class,
        ];
    }
}
