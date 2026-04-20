<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('breakdown_logs', function (Blueprint $table) {
            // Mengubah kolom yang tadinya ENUM menjadi String biasa (VARCHAR)
            $table->string('repair_status')->change();
        });
    }

    public function down()
    {
        Schema::table('breakdown_logs', function (Blueprint $table) {
            // Tidak perlu dikembalikan ke ENUM, biarkan saja jadi string
        });
    }
};
