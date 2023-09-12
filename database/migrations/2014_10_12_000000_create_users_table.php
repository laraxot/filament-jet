<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
<<<<<<< HEAD
        Schema::create('users', static function (Blueprint $blueprint) : void {
=======
        Schema::create('users', function (Blueprint $blueprint): void {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            $blueprint->id();
            $blueprint->string('name');
            $blueprint->string('email')->unique();
            $blueprint->timestamp('email_verified_at')->nullable();
            $blueprint->string('password');
            $blueprint->rememberToken();
            $blueprint->foreignId('current_team_id')->nullable();
            $blueprint->string('profile_photo_path', 2048)->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
