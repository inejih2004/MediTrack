<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'material_id';
    protected $fillable = ['material_name', 'type', 'description'];
    public $incrementing = true;
    public $timestamps = false;

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'material_id', 'material_id');
    }

    public function expirationDates()
    {
        return $this->hasMany(ExpirationDate::class, 'material_id', 'material_id');
    }

    public function movementHistories()
    {
        return $this->hasMany(MovementHistory::class, 'material_id', 'material_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'material_id', 'material_id');
    }
}