<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // superadmin atau operator
            $table->string('role')->default('operator')->after('email'); 
            // Cabang tempat dia bekerja (bisa null jika dia adalah superadmin pusat)
            $table->foreignId('entity_id')->nullable()->constrained('entities')->nullOnDelete()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['entity_id']);
            $table->dropColumn(['role', 'entity_id']);
        });
    }
};
