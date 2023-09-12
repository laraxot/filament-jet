<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
<<<<<<< HEAD
        Schema::create('teams', static function (Blueprint $blueprint) : void {
=======
        Schema::create('teams', function (Blueprint $blueprint): void {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            $blueprint->id();
            $blueprint->foreignId('user_id')->index();
            $blueprint->string('name');
            $blueprint->boolean('personal_team');
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
