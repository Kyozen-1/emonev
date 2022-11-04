<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RenjaController extends Controller
{
    public function index()
    {
        return view('opd.renja.index');
    }
}
