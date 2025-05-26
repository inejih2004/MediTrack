<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpirationDate extends Model
{
    
    protected $primaryKey = 'expiration_id';
    protected $fillable = ['material_id', 'service_id', 'expiration_date', 'alert_status', 'last_alert_date'];
    public $timestamps = false; // Disable timestamps

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}