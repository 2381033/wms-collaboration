<?php

namespace App\Http\Controllers;

use App\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MenuController extends Controller
{
    public $menu_name = "";
    
    public function index(Request $request)
    {
        $this->menu_name = 'Menu';
        $this->authorize('akses-gate', $this->menu_name);

        $details = Menu::orderBy('id')->get();
        
        if ($request->ajax()) {
            return datatables()->of($details)
            ->addColumn('action', function($data){
                $button = "";
                if (Gate::allows('edit-gate', $this->menu_name)) {
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm edit-data"><i class="far fa-edit"></i> Edit</a>';
                }
                $button .= '&nbsp;&nbsp;';
                if (Gate::allows('hapus-gate', $this->menu_name)) {
                    $button .= '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Hapus</button>';                                 
                }
                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()                   
            ->make(true);
        }

        return view('menu', compact('details'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required'
        ], [
            'nama.required' => 'Nama menu tidak boleh kosong'
        ]);

        $id = $request->id;
        
        $post   =   Menu::updateOrCreate(['id' => $id],
                    [
                        'id'=> $request->id, 
                        'nama' => $request->nama,
                        'level_menu' => $request->level_menu,
                        'master_id' => $request->master_id,
                        'url' => $request->url,
                        'icon' => $request->icon,
                        'nomor_urut' => $request->nomor_urut,
                        'aktif' => $request->aktif
                    ]); 

        return response()->json($post);
    }
    
    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = Menu::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {        
        try { 
            $post = Menu::where('id',$id)->delete();
        } catch (\Illuminate\Database\QueryException $ex){ 
            $post = [ 'message'=> 'Tidak dapat dihapus, data ini sudah digunakan.', 'code' => $ex->getCode() ]; 
        }
     
        return response()->json($post);
    }
}
