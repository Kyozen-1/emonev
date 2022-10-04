<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class E78Controller extends Controller
{
    public function index()
    {
        return view('admin.laporan.e-78.index');
    }
}
