<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementHistory extends Model
{
    protected $table = 'movement_history';
    protected $primaryKey = 'movement_id';
    protected $fillable = ['material_id', 'service_id', 'movement_type', 'quantity', 'date', 'reason'];
    public $incrementing = true;
    public $timestamps = false;

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }
}