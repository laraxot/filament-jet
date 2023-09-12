<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_invitations', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('team_id')->constrained()->cascadeOnDelete();
            $blueprint->string('email');
            $blueprint->string('role')->nullable();
            $blueprint->timestamps();
    }
    public function down(): void
    {
        Schema::dropIfExists('team_invitations');
    }
};
