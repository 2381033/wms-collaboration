<?php

namespace App\Http\Controllers\Master;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Site as MasterSite;
use App\Models\Master\Principal as MasterPrincipal;
use App\Models\Reference\Currency as ReferenceCurrency;
use App\Models\Reference\UoM as ReferenceUoM;

class PrincipalController extends Controller
{
    public $menu_name = "product-master/principal";

    public function index(Request $request) {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        $company_id = Auth::user()->company_id;

        $details = MasterPrincipal::where('company_id', $company_id)->get();

        if ($request->ajax()) {
            return datatables()->of($details)
            ->editColumn('active', function ($data) {
                if ($data->active == 'Yes') {
                    $status = '<div class="btn btn-sm btn-success"><i class="fas fa-check"></i><span> ' . $data->active . '</span></div>';
                } else {
                    $status = '<div class="btn btn-sm btn-warning"><i class="fas fa-trash"></i><span> ' . $data->active . '</span></div>';
                }
                return $status;
            })
            ->addColumn('action', function($data){
                $button = "";
                $button .= '<a href="'. URL('/product-master/principal/edit/'.$data->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-info btn-sm"><i class="far fa-edit"></i> Edit</a>';
                $button .= '&nbsp;&nbsp;';
                $button .= '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Hapus</button>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }

        return view('master.principal.index');
    }

    public function create() {
        $company_id = Auth::user()->company_id;

        $site = MasterSite::where('company_id', $company_id)
                    ->where('active', 'Yes')
                    ->get();

        $unit = ReferenceUoM::where('active', 'Yes')
                    ->get();

        $currency = ReferenceCurrency::where('active', 'Yes')
                    ->get();

        $branch = DB::table('mt_branch as a')
                    ->where('a.active', 'Yes')
                    ->get(['a.id', 'a.branch_name']);

        $data = [
            'site_list' => $site,
            'unit_list' => $unit,
            'currency_list' => $currency,
            'branch_list'=>$branch
        ];

        return view('master.principal.create', $data);
    }

    public function edit($id)
    {
        $company_id = Auth::user()->company_id;

        $principal  = MasterPrincipal::where('id', $id)->first();

        $site = MasterSite::where('company_id', $company_id)
                    ->where('active', 'Yes')
                    ->get();

        $branch = DB::table('mt_branch as a')
                    ->where('a.active', 'Yes')
                    ->get(['a.id', 'a.branch_name']);

        $unit = ReferenceUoM::where('active', 'Yes')
                    ->get();

        $currency = ReferenceCurrency::where('active', 'Yes')
                    ->get();

        $data = [
            'site_list' => $site,
            'branch_list'=> $branch,
            'view' => $principal,
            'unit_list' => $unit,
            'currency_list' => $currency
        ];

        return view('master.principal.create', $data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            'short_name.required'=>'Short name cannot be empty.',
            'principal_name.required'=>'Principal name cannot be empty.',
        );

        $rules = array(
            'short_name' => 'required',
            'principal_name' => 'required'
        );

        $company_id = Auth::user()->company_id;

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->id;

        $data = MasterPrincipal::updateOrCreate(['id' => $id],
        [
            'company_id'=>$company_id,
            'short_name'=>$request->short_name,
            'principal_name' => $request->principal_name,
            'interface_name' => $request->interface_name,
            'address1'=>$request->address1,
            'address2'=>$request->address2,
            'address3'=>$request->address3,
            'address4'=>$request->address4,
            'phone'=>$request->phone,
            'email'=>$request->email,
            'pic_name'=>$request->pic_name,
            'pic_phone'=>$request->pic_phone,
            'active' => $request->active
        ]);

        return response()->json(['success'=>url('/product-master/principal/edit/' . $data->id), 'inbound_id' => $data->id]);
    }

    public function destroy(Request $request)
    {
        try {
            MasterPrincipal::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }
}
