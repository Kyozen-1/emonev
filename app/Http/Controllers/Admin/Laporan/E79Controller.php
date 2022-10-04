<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class E79Controller extends Controller
{
    public function index()
    {
        return view('admin.laporan.e-79.index');
    }
}
