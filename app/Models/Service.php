<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'service_id';
    protected $fillable = ['service_name', 'description'];
    public $incrementing = true;
    public $timestamps = false;

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'service_id', 'service_id');
    }

    public function expirationDates()
    {
        return $this->hasMany(ExpirationDate::class, 'service_id', 'service_id');
    }

    public function movementHistories()
    {
        return $this->hasMany(MovementHistory::class, 'service_id', 'service_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'service_id', 'service_id');
    }
}