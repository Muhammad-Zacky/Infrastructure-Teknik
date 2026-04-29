<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add audit trail and soft deletes to breakdown_logs table
        Schema::table('breakdown_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('document_proof');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->softDeletes();

            // Add foreign keys
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('breakdown_logs', function (Blueprint $table) {
            $table->dropForeignKey(['created_by']);
            $table->dropForeignKey(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by', 'deleted_at']);
        });
    }
};
