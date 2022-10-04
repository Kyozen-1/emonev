<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Tc19Controller extends Controller
{
    public function index()
    {
        return view('admin.laporan.tc-19.index');
    }
}
