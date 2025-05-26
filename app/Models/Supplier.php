<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'supplier_id';
    protected $fillable = ['supplier_name', 'contact_info', 'payment_terms', 'performance_rating'];
    public $incrementing = true;
    public $timestamps = false;

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'supplier_id', 'supplier_id');
    }
}