<?php

namespace App\Http\Controllers;

use App\Models\ChargeType;
use App\Models\GcashTransaction;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GcashViewController extends Controller
{
    public function index()
    {

        
        return Inertia::render('Gcash/Gcashv2', [
            'transaction_types' => TransactionType::all(),
            'charge_types' => ChargeType::all(),
            'allTransactions' => GcashTransaction::orderByDesc('id')->with('transaction_type', 'charge_type')->latest()->paginate(5),
        ]);
    }
}
