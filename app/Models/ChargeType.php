<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChargeType extends Model
{
 use HasFactory;

    protected $fillable = ['name'];

   public function gcash_transaction(): HasMany {
        return $this->hasMany(GcashTransaction::class, 'charge_type_id');
    }
}
