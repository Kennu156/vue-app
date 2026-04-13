<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class SharksController extends Controller
{
    public function index()
    {
        return Inertia::render('Sharks/Index');
    }
}