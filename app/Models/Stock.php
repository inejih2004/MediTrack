<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stock';
    protected $primaryKey = 'stock_id';
    protected $fillable = ['service_id', 'material_id', 'quantity', 'threshold'];
    public $incrementing = true;
    public $timestamps = false;

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }
}