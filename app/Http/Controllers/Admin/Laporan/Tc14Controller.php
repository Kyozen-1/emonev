<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Tc14Controller extends Controller
{
    public function index()
    {
        return view('admin.laporan.tc-14.index');
    }
}
