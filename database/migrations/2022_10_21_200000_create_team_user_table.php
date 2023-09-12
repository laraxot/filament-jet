<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
<<<<<<< HEAD
        Schema::create('team_user', static function (Blueprint $blueprint) : void {
=======
        Schema::create('team_user', function (Blueprint $blueprint): void {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            $blueprint->id();
            $blueprint->foreignId('team_id');
            $blueprint->foreignId('user_id');
            $blueprint->string('role')->nullable();
            $blueprint->timestamps();
<<<<<<< HEAD
=======

>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            $blueprint->unique(['team_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_user');
    }
};
