<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Infrastructure extends Model
{
    use HasFactory, SoftDeletes;

    // Atribut yang diizinkan untuk diisi ke dalam database (Mass Assignment)
    // PASTIKAN ADA KATA 'image' DI DALAM ARRAY INI
    protected $fillable = [
        'entity_id',
        'category',
        'code_name',
        'type',
        'quantity',
        'status',
        'image',
        'created_by',
        'updated_by'
    ];

    /**
     * Relasi ke tabel entities
     * (Setiap alat/infrastruktur dimiliki oleh satu entitas/cabang)
     */
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Relasi ke tabel breakdown_logs
     * (Satu alat/infrastruktur bisa memiliki banyak riwayat kerusakan)
     */
    public function breakdownLogs()
    {
        // Pastikan nama model BreakdownLog sudah sesuai dengan yang ada di folder Models kamu
        return $this->hasMany(BreakdownLog::class, 'infrastructure_id');
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
