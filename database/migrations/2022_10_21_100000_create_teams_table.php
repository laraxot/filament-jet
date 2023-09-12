<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void {
        Schema::create('teams', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('user_id')->index();
            $blueprint->string('name');
            $blueprint->boolean('personal_team');
            $blueprint->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('teams');
    }
};
