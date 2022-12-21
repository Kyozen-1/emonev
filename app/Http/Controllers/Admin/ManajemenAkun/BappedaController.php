<?php

namespace App\Http\Controllers\Admin\ManajemenAkun;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use RealRashid\SweetAlert\Facades\Alert;
use DB;
use Validator;
use DataTables;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Hash;

class BappedaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax())
        {
            $data = User::where('id', '!=', 1)->where('status_hapus', '0')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="detail" id="'.$data->id.'" class="detail btn btn-icon waves-effect btn-outline-success" title="Detail Data"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="change-password" id="'.$data->id.'"
                    class="change-password btn btn-icon waves-effect btn-outline-warning" title="Change Password"><i class="fas fa-lock"></i></button>';
                    $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-icon waves-effect btn-outline-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    return $button;
                })
                ->editColumn('foto', function($data){
                    return '<img src="'.asset('images/bappeda/'.$data->foto).'" style="width:5rem;">';
                })
                ->rawColumns(['aksi', 'foto'])
                ->make(true);
        }
        return view('admin.manajemen-akun.bappeda.index');
    }

    public function get_akun_tidak_aktif()
    {
        if(request()->ajax())
        {
            $data = User::where('id', '!=', 1)->where('status_hapus', '1')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    return '<button type="button" name="aktif" id="'.$data->id.'" class="aktif btn btn-icon waves-effect btn-outline-success" title="Aktifkan Akun"><i class="fas fa-check"></i></button>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'name' => 'required',
            'no_hp' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'foto' => 'mimes:jpeg,jpg,png|required|max:1024'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $fotoExtension = $request->foto->extension();
        $fotoName =  uniqid().'-'.date("ymd").'.'.$fotoExtension;
        $foto = Image::make($request->foto);
        $fotoSize = public_path('images/bappeda/'.$fotoName);
        $foto->save($fotoSize, 100);

        $user = new User;
        $user->name = $request->name;
        $user->no_hp = $request->no_hp;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->foto = $fotoName;
        $user->save();

        return response()->json(['success' => 'Berhasil menambahkan akun']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = User::find($id);

        return response()->json(['result' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = User::find($id);

        return response()->json(['result' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->status_hapus = '1';
        $user->save();

        return response()->json(['success' => 'Berhasil menghapus akun']);
    }

    public function change_password(Request $request)
    {
        $user = User::find($request->id);
        $user->password = Hash::make('12345678');
        $user->save();

        return response()->json(['success' => 'Berhasil! Password baru 12345678']);
    }

    public function aktif($id)
    {
        $user = User::find($id);
        $user->status_hapus = '0';
        $user->save();

        return response()->json(['success' => 'Berhasil mengaktifkan kembali akun']);
    }
}
