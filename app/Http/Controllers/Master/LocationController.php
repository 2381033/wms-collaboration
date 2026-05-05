<?php

namespace App\Http\Controllers\Master;

use App\Exports\LocationExport;
use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use App\Imports\LocationImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Master\Site as MasterSite;
use App\Models\Master\SiteArea as MasterSiteArea;
use App\Models\Master\Location as MasterLocation;
use App\Models\Master\LocationStatus as MasterLocationStatus;
use App\Models\Master\LocationType as MasterLocationType;

class LocationController extends Controller
{
    public $menu_name = "site-master/location";

    public function index(Request $request) {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        $company_id = Auth::user()->company_id;

        $details = DB::table("iv_location as a")
                    ->select("a.*", "b.area_name")
                    ->leftjoin("iv_site_area as b", "a.area_id", "b.id")
                    ->where("a.company_id", $company_id)
                    ->where("a.site_id", $request->site_id)
                    ->where("a.area_id", "like", $request->area_id == 0 ? "%" : $request->area_id)
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

        $location_type = MasterLocationType::where('active', 'Yes')
                            ->get(['id', 'description']);

        $site = MasterSite::where('company_id', $company_id)
                            ->where('active', 'Yes')
                            ->get(['id', 'site_name']);

        $area = MasterSiteArea::where('company_id', $company_id)
                            ->where('site_id', $site->first()->id)
                            ->where('active', 'Yes')
                            ->get(['id', 'area_name']);

        $location_status = MasterLocationStatus::where('active', 'Yes')
                            ->get(['status_code', 'status_name']);

        $data = [
            'location_type_list'=>$location_type,
            'location_status_list'=>$location_status,
            'site_list'=>$site,
            'area_list'=>$area
        ];

        return view('master.location', $data);
    }

    public function area(Request $request) {
        $company_id = Auth::user()->company_id;

        $area = MasterSiteArea::where('company_id', $company_id)
                            ->where('site_id', $request->site_id)
                            ->where('active', 'Yes')
                            ->get(['id', 'area_name']);

        $data = [
            'area_list'=>$area
        ];

        return response()->json($data);
    }

    public function store(Request $request) {
        $messsages = array(
            'area_id.required'=>'Area name cannot be empty.',
            'location_code.required'=>'Location code cannot be empty.',
            'location_name.required'=>'Location name cannot be empty.',
            'status_code.required'=>'Status name cannot be empty.',
            'type_id.required'=>'Type name cannot be empty.',
        );

        $rules = array(
            'area_id' => 'required',
            'location_code' => 'required',
            'location_name' => 'required',
            'status_code' => 'required',
            'type_id' => 'required',
        );

        $company_id = Auth::user()->company_id;

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->id;

        MasterLocation::updateOrCreate(['id' => $id],
        [
            'company_id'=>$company_id,
            'site_id' => $request->site_id,
            'area_id'=>$request->area_id,
            'location_code'=>$request->location_code,
            'location_name'=>$request->location_name,
            'status_code'=>$request->status_code,
            'type_id'=>$request->type_id,
            'location_aisle'=>$request->location_aisle,
            'location_column'=>$request->location_column,
            'location_level'=>$request->location_level,
            'active' => $request->active
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id) {
        $company_id = Auth::user()->company_id;

        $edit  = MasterLocation::where('id', $id)->first();

        $area = MasterSiteArea::where('company_id', $company_id)
                            ->where('site_id', $edit->site_id)
                            ->where('active', 'Yes')
                            ->get(['id', 'area_name']);

        $data = [
            'edit_view'=>$edit,
            'area_list'=>$area
        ];

        return response()->json($data);
    }

    public function destroy(Request $request) {
        try {
            MasterLocation::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }

	public function export($site_id) {
		return Excel::download(new LocationExport($site_id), "$site_id-location.xlsx");
    }

    public function import(Request $request) {
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

		// import data
        Excel::import(new LocationImport($request->site_upload, $request->area_upload), $path);

        Storage::delete('/file/excel/'. $nama_file);

		// alihkan halaman kembali
		return redirect('/site-master/location');
	}

    public function print($site_id, $area_id) {
        $list = MasterLocation::where("site_id", $site_id)->where("area_id", $area_id)->get();

        return view("report.master.barcode", compact("list"));
    }
}
