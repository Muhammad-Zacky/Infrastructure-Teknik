<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infrastructure extends Model
{
    use HasFactory;

    // PASTIKAN ADA KATA 'image' DI DALAM ARRAY INI
    protected $fillable = ['entity_id', 'category', 'code_name', 'type', 'quantity', 'status', 'image'];

    // Relasi ke tabel entities
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
