<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GcashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'charge_type_id',
        'transaction_type_id',
        'reference_number',
        'created_at'
    ];

    public function transaction_type()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }
    
    public function charge_type()
    {
        return $this->belongsTo(ChargeType::class, 'charge_type_id');
    }
}
