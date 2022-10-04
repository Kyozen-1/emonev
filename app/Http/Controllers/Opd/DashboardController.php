<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\AkunOpd;

class DashboardController extends Controller
{
    public function index()
    {
        return view('opd.dashboard.index');
    }

    public function change(Request $request)
    {
        $user = AkunOpd::find(Auth::user()->id);
        $user->color_layout = $request->color_layout;
        $user->nav_color = $request->nav_color;
        $user->behaviour = $request->behaviour;
        $user->layout = $request->layout;
        $user->radius = $request->radius;
        $user->placement = $request->placement;
        $user->save();
    }
}
