<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\User;

class MenuUserController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $user_id = $request->user_id;

            $list = DB::table("sm_menu as a")
                    ->select("a.id", "a.name", "b.akses", "b.tambah", "b.edit", "b.hapus", "b.cetak")
                    ->leftJoin("sm_menu_user as b", function($join) use ($user_id) {
                        $join->on("a.id", "b.menu_id")
                            ->where("user_id", $user_id);
                    })
                    ->where("a.active", "Yes")
                    ->orderBy("a.id", "asc")
                    ->orderBy("a.parent_id", "asc")
                    ->get();

            return datatables()->of($list)
            ->editColumn("akses", function ($data)
            {
                $input = "<input type='hidden' value='$data->id' name='id[]'/>";
                $input .= "<select name='akses[]'>";
                if ($data->akses == "Yes") {
                    $input .= "<option value='Yes' selected>" . 'Yes' . "</option>";
                    $input .= "<option value='No'>" . 'No' . "</option>";
                } else {
                    $input .= "<option value='Yes'>" . 'Yes' . "</option>";
                    $input .= "<option value='No' selected>" . 'No' . "</option>";
                }
                $input .= "</select>";
                return $input;
            })
            ->editColumn("tambah", function ($data)
            {
                $input = "<select name='tambah[]'>";
                if ($data->tambah == "Yes") {
                    $input .= "<option value='Yes' selected>" . 'Yes' . "</option>";
                    $input .= "<option value='No'>" . 'No' . "</option>";
                } else {
                    $input .= "<option value='Yes'>" . 'Yes' . "</option>";
                    $input .= "<option value='No' selected>" . 'No' . "</option>";
                }
                $input .= "</select>";
                return $input;
            })
            ->editColumn("edit", function ($data)
            {
                $input = "<select name='edit[]'>";
                if ($data->edit == "Yes") {
                    $input .= "<option value='Yes' selected>" . 'Yes' . "</option>";
                    $input .= "<option value='No'>" . 'No' . "</option>";
                } else {
                    $input .= "<option value='Yes'>" . 'Yes' . "</option>";
                    $input .= "<option value='No' selected>" . 'No' . "</option>";
                }
                $input .= "</select>";
                return $input;
            })
            ->editColumn("hapus", function ($data)
            {
                $input = "<select name='hapus[]'>";
                if ($data->hapus == "Yes") {
                    $input .= "<option value='Yes' selected>" . 'Yes' . "</option>";
                    $input .= "<option value='No'>" . 'No' . "</option>";
                } else {
                    $input .= "<option value='Yes'>" . 'Yes' . "</option>";
                    $input .= "<option value='No' selected>" . 'No' . "</option>";
                }
                $input .= "</select>";
                return $input;
            })
            ->editColumn("cetak", function ($data)
            {
                $input = "<select name='cetak[]'>";
                if ($data->cetak == "Yes") {
                    $input .= "<option value='Yes' selected>" . 'Yes' . "</option>";
                    $input .= "<option value='No'>" . 'No' . "</option>";
                } else {
                    $input .= "<option value='Yes'>" . 'Yes' . "</option>";
                    $input .= "<option value='No' selected>" . 'No' . "</option>";
                }
                $input .= "</select>";
                return $input;
            })
            ->rawColumns(["akses", "tambah", "edit", "hapus", "cetak"])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $user_id = $request->user_id_menu;
                $menu_id = $request->id;
                $akses = $request->akses;
                $tambah = $request->tambah;
                $edit = $request->edit;
                $hapus = $request->hapus;
                $cetak = $request->cetak;

                for ($i=0; $i < count($menu_id) ; $i++) {
                    $user = User::find($user_id);

                    if ($user->menu()->where('menu_id', $menu_id[$i])->exists()) {
                        $user->menu()->detach($menu_id[$i]);
                    }

                    $user->menu()->attach($menu_id[$i], [
                        'akses'=>$akses[$i],
                        'tambah'=>$tambah[$i],
                        'edit'=>$edit[$i],
                        'hapus'=>$hapus[$i],
                        'cetak'=>$cetak[$i]
                    ]);
                }

                DB::commit();

                $message = ["success"=>"Sukses"];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ["error"=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
