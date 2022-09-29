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
use App\AkunOpd;
use App\Models\Opd;
use Illuminate\Support\Facades\Hash;

class OpdController extends Controller
{
    public function index()
    {
        if(request()->ajax())
        {
            $data = AkunOpd::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="detail" id="'.$data->id.'" class="detail btn btn-icon waves-effect btn-outline-success" title="Detail Data"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="change-password" id="'.$data->id.'"
                    class="change-password btn btn-icon waves-effect btn-outline-warning" title="Change Password"><i class="fas fa-lock"></i></button>';
                    // $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-icon waves-effect btn-outline-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    // $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    $button = $button_show . ' ' . $button_edit;
                    return $button;
                })
                ->editColumn('no_hp', function($data){
                    return $data->opd->no_hp;
                })
                ->editColumn('foto', function($data){
                    return '<img src="'.asset('images/opd/'.$data->opd->foto).'" style="width:5rem;">';
                })
                ->rawColumns(['aksi', 'foto'])
                ->make(true);
        }
        return view('admin.manajemen-akun.opd.index');
    }

    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'nama' => 'required',
            'no_hp' => 'required',
            'alamat' => 'required',
            'email' => 'required|unique:akun_opds',
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
        $fotoSize = public_path('images/opd/'.$fotoName);
        $foto->save($fotoSize, 100);

        $opd = new Opd;
        $opd->nama = $request->nama;
        $opd->no_hp = $request->no_hp;
        $opd->alamat = $request->alamat;
        $opd->negara_id = 62;
        $opd->provinsi_id = 5;
        $opd->kabupaten_id = 62;
        $opd->foto = $fotoName;
        $opd->save();

        $akun_opd = new AkunOpd;
        $akun_opd->name = $opd->nama;
        $akun_opd->email = $request->email;
        $akun_opd->password = Hash::make($request->password);
        $akun_opd->opd_id = $opd->id;
        $akun_opd->save();

        return response()->json(['success' => 'Berhasil Menyimpan Data']);
    }

    public function show($id)
    {
        $data = AkunOpd::find($id);
        $array = [
            'nama' => $data->name,
            'email' => $data->email,
            'provinsi' => $data->opd->provinsi->nama,
            'kabupaten' => $data->opd->kabupaten->nama,
            'no_hp' => $data->opd->no_hp,
            'alamat' => $data->opd->alamat,
            'foto' => $data->opd->foto
        ];

        return response()->json(['result' => $array]);
    }

    public function change_password(Request $request)
    {
        $akun_opd = AkunOpd::find($request->id);
        $akun_opd->password = Hash::make('12345678');
        $akun_opd->save();

        return response()->json(['success' => 'Berhasil Merubah Password! Passwordnya 12345678']);
    }
}
