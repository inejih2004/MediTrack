<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';
    protected $primaryKey = 'purchase_id';
    protected $fillable = ['service_id', 'supplier_id', 'material_id', 'quantity', 'purchase_date', 'cost', 'invoice_number', 'delivery_date'];
    public $incrementing = true;
    public $timestamps = false;

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }
}