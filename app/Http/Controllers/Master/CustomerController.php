<?php

namespace App\Http\Controllers\Master;

use App\Exports\CustomerExport;
use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use App\Imports\CustomerImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Master\Customer as MasterCustomer;
use App\Models\Master\CustomerType as MasterCustomerType;
use App\Models\Master\CustomerGroup as MasterCustomerGroup;

class CustomerController extends Controller
{
    public $menu_name = "customer-master/customer";

    public function index(Request $request) {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        $company_id = Auth::user()->company_id;

        $details = MasterCustomer::where('company_id', $company_id)
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

        return view('master.customer');
    }

    public function reference(Request $request) {
        $company_id = Auth::user()->company_id;

        $customer_type = MasterCustomerType::where('company_id', $company_id)
                            ->where('principal_id', $request->principal_id)
                            ->where('active', 'Yes')
                            ->get(['id', 'type_name']);

        $customer_group = MasterCustomerGroup::where('company_id', $company_id)
                            ->where('principal_id', $request->principal_id)
                            ->where('active', 'Yes')
                            ->get(['id', 'group_name']);

        $data = [
            'type_list'=>$customer_type,
            'group_list'=>$customer_group,
        ];

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            'customer_code.required'=>'Customer code cannot be empty.',
            'customer_name.required'=>'Customer name cannot be empty.',
            'group_id.required'=>'Group name cannot be empty.',
            'type_id.required'=>'Type name cannot be empty.',
        );

        $rules = array(
            'customer_code' => 'required',
            'customer_name' => 'required',
            'group_id' => 'required',
            'type_id' => 'required',
        );

        $company_id = Auth::user()->company_id;

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->id;

        MasterCustomer::updateOrCreate(['id' => $id],
        [
            'company_id'=>$company_id,
            'principal_id' => $request->principal_id,
            'customer_code'=>$request->customer_code,
            'customer_name'=>$request->customer_name,
            'group_id'=>$request->group_id,
            'type_id'=>$request->type_id,
            'address1'=>$request->address1,
            'address2'=>$request->address2,
            'address3'=>$request->address3,
            'address4'=>$request->address4,
            'phone'=>$request->phone,
            'email'=>$request->email,
            'pic_name'=>$request->pic_name,
            'pic_phone'=>$request->pic_phone,
            'store_id'=>$request->store_id,
            'active' => $request->active
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id)
    {
        $company_id = Auth::user()->company_id;

        $edit  = DB::table("iv_customer as a")
                    ->select("a.*", "b.store_name")
                    ->leftjoin("tm_store as b", "a.store_id", "b.id")
                    ->where("a.id", $id)
                    ->first();

        $customer_type = MasterCustomerType::where('company_id', $company_id)
                            ->where('principal_id', $edit->principal_id)
                            ->where('active', 'Yes')
                            ->get(['id', 'type_name']);

        $customer_group = MasterCustomerGroup::where('company_id', $company_id)
                            ->where('principal_id', $edit->principal_id)
                            ->where('active', 'Yes')
                            ->get(['id', 'group_name']);

        $data = [
            'edit_view'=>$edit,
            'type_list'=>$customer_type,
            'group_list'=>$customer_group,
        ];

        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        try {
            MasterCustomer::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }

	public function export($principal_id)
	{
		return Excel::download(new CustomerExport($principal_id), "$principal_id-cust.xlsx");
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

        $path = storage_path('app/file/excel/' . $nama_file);
        $request->file('file')->storeAs('file/excel', $nama_file);

        // Excel::import(new CustomerImport(), $path);

        Excel::import(new CustomerImport($request->principal_upload), $path);

        Storage::delete('/file/excel/'. $nama_file);

		return redirect('/customer-master/customer');
	}
}
