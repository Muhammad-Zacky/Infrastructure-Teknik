<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreakdownLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'infrastructure_id',
        'issue_detail',
        'repair_status',
        'vendor_pic',
        // Tambahan Kolom Baru:
        'troubleshoot_date',
        'ba_date',
        'work_order_date',
        'pr_po_date',
        'sparepart_date',
        'start_work_date',
        'com_test_date',
        'resolved_date',
        'document_proof',
        'created_by',
        'updated_by'
    ];

    // Relasi ke tabel Infrastruktur
    public function infrastructure()
    {
        return $this->belongsTo(Infrastructure::class)->withTrashed();
    }

    /**
     * Relasi ke tabel users untuk audit trail
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
