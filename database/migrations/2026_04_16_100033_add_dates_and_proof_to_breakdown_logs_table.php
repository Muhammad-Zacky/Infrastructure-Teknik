<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('breakdown_logs', function (Blueprint $table) {
            // Kolom-kolom tanggal sesuai Excel Pelindo
            $table->date('troubleshoot_date')->nullable();
            $table->date('ba_date')->nullable();          // Berita Acara
            $table->date('work_order_date')->nullable();  // Work Order
            $table->date('pr_po_date')->nullable();       // PR / PO
            $table->date('sparepart_date')->nullable();   // Spare Part On Site
            $table->date('start_work_date')->nullable();  // Mulai Pekerjaan
            $table->date('com_test_date')->nullable();    // Com Test
            $table->date('resolved_date')->nullable();    // Selesai Pekerjaan
            
            // Kolom bukti fisik (Foto / PDF)
            $table->string('document_proof')->nullable(); 
        });
    }

    public function down()
    {
        Schema::table('breakdown_logs', function (Blueprint $table) {
            $table->dropColumn([
                'troubleshoot_date', 'ba_date', 'work_order_date', 'pr_po_date', 
                'sparepart_date', 'start_work_date', 'com_test_date', 'resolved_date', 
                'document_proof'
            ]);
        });
    }
};
