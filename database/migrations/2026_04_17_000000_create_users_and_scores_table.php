<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Users tabula jau eksistē, šo migrāciju izlaižam
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('player_count')->default(2);
            $table->integer('score');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('scores');
        Schema::dropIfExists('users');
    }
};
