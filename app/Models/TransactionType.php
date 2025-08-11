<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionType extends Model
{

    use HasFactory;

    protected $fillable = ['charge'];

   public function gcash_transaction(): HasMany {
        return $this->hasMany(GcashTransaction::class, 'transaction_type_id');
    }
}
