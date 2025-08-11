<?php

namespace App\Http\Controllers;


use Inertia\Inertia;

class IncomeController extends Controller
{
    public function index(){
        return Inertia::render('Gcash/Income');
    }
}
