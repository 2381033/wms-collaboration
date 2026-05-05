<?php

namespace App\Http\Controllers\Master;

use App\Exports\StoreExport;
use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use App\Imports\CustomerImport;
use App\Imports\StoreImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Master\Customer as MasterCustomer;
use App\Models\Master\Store as MasterStore;
use App\Models\Reference\Country as ReferenceCountry;

class StoreController extends Controller
{
    public $menu_name = "customer-master/store";

    public function index(Request $request) {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        $company_id = Auth::user()->company_id;

        $details = MasterStore::where('company_id', $company_id)
                    ->where('principal_id', $request->principal_id)
                    ->get();

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
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm edit-data"><i class="far fa-edit"></i> Edit</a>';
                $button .= '&nbsp;&nbsp;';
                $button .= '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Hapus</button>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }

        $country = ReferenceCountry::where('active', 'Yes')->get();

        $data = [
            'country_list' => $country
        ];

        return view('master.store', $data);
    }

    public function customer(Request $request) {
        $company_id = Auth::user()->company_id;

        $customer = MasterCustomer::where('company_id', $company_id)
                            ->where('principal_id', $request->principal_id)
                            ->where('active', 'Yes')
                            ->get(['id', 'customer_name']);

        $data = [
            'customer_list'=>$customer,
        ];

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            'principal_id.required'=>'Principal name cannot be empty.',
            'store_code.required'=>'Store code cannot be empty.',
            'store_name.required'=>'Store name cannot be empty.',
        );

        $rules = array(
            'principal_id' => 'required',
            'store_code' => 'required',
            'store_name' => 'required',
        );

        $company_id = Auth::user()->company_id;

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->id;

        MasterStore::updateOrCreate(['id' => $id],
        [
            'company_id'=>$company_id,
            'principal_id' => $request->principal_id,
            'store_code'=>$request->store_code,
            'store_name'=>$request->store_name,
            'country_code'=>$request->country_code,
            'region_code'=>$request->region_code,
            'city_code'=>$request->city_code,
            'address1'=>$request->address1,
            'address2'=>$request->address2,
            'address3'=>$request->address3,
            'address4'=>$request->address4,
            'telephone'=>$request->telephone,
            'email'=>$request->email,
            'pic_name'=>$request->pic_name,
            'pic_phone'=>$request->pic_phone,
            'active' => $request->active
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id)
    {
        $data  = MasterStore::find($id);

        $region_list = DB::table("rt_region")
                    ->where("country_code", $data->country_code)
                    ->where("active", "Yes")
                    ->get(["region_code", "region_name"]);

        $city_list = DB::table("rt_city")
                    ->where("country_code", $data->country_code)
                    ->where("region_code", $data->region_code)
                    ->where("active", "Yes")
                    ->get(["city_code", "city_name"]);
        $data = [
            'edit_view'=>$data,
            'region_list'=>$region_list,
            'city_list'=>$city_list,
        ];

        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        try {
            MasterStore::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }

	public function export($principal_id)
	{
		return Excel::download(new StoreExport($principal_id), "store-$principal_id.xlsx");
    }

    public function import(Request $request)
	{
		// validasi
		$this->validate($request, [
			'file' => 'required|mimes:csv,xls,xlsx'
		]);

		// menangkap file excel
		$file = $request->file('file');

		// membuat nama file unik
		$nama_file = rand().".".$file->extension();

        $path = storage_path('app/file/store/' . $nama_file);
        $request->file('file')->storeAs('file/store', $nama_file);

		// import data
        Excel::import(new StoreImport($request->principal_upload), $path);

		// alihkan halaman kembali
		return redirect('/customer-master/store');
	}
}
