<?php

namespace App\Http\Controllers\Master\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExportLocationImport as ImportLocation;

class ExportController extends Controller
{
    public function index()
    {
        $company_id = Auth::user()->company_id;

        $service_list = DB::table('mt_service')->where("active", "Yes")->get();
        $size_list = DB::table('iv_container_size')->where("company_id", $company_id)->where("active", "Yes")->get();

        $checker = DB::table('ex_master_checker as mc')
            ->select("mc.*", "mb.branch_name")
            ->join("mt_branch as mb", "mb.id", "mc.branch_id")
            ->orderBy('name', 'ASC')
            ->get();

        $location = DB::table('ex_location as a')
            ->select("a.*", "b.branch_name")
            ->join("mt_branch as b", "b.id", "a.branch_id")
            ->orderBy('a.location_code', 'ASC')
            ->get();

        $branch = DB::table('mt_branch')
            ->where('active', 'Yes')
            ->get();

        $data = [
            "service_list" => $service_list,
            "size_list" => $size_list,
            "checker" => $checker,
            "branch" => $branch,
            "location" => $location,
        ];
        return view('master.export.master', $data);
    }

    public function actionChecker($type, $id)
    {
        if ($type == 'enable') {
            $status = 1;
        } else {
            $status = 0;
        }
        DB::table('ex_master_checker')->where('id', $id)->update([
            'status' => $status
        ]);
        return back();
    }

    public function addChecker($name)
    {
        DB::table('ex_master_checker')->insert([
            'name'   => $name,
            'status' => 1
        ]);
        return back();
    }

    public function storeChecker(Request $request)
    {
        $messsages = array(
            'checkername.required' => 'Name cannot be empty.',
        );

        $rules = array(
            'checkername' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $id = $request->idChecker;
        $name = $request->checkername;
        $branch = $request->checkerBranch_id;
        $status = ($request->checkerStatus == "No") ? 0 : 1;
        if ($id) {
            DB::table('ex_master_checker')->where('id', $id)->update([
                'name'   => $name,
                'branch_id'   => $branch,
                'status' => $status
            ]);
            return response()->json(['success' => 'Updated new records.']);
        } else {
            DB::table('ex_master_checker')->insert([
                'name'   => $name,
                'branch_id'   => $branch,
                'status' => $status
            ]);
            return response()->json(['success' => 'Added new records.']);
        }
    }

    public function storeLocation(Request $request)
    {
        $messsages = array(
            'location_code.required' => 'Location Code cannot be empty.',
            'location_aisle.required' => 'Location Aisle cannot be empty.',
            'location_column.required' => 'Location Column cannot be empty.',
            'location_level.required' => 'Location Level cannot be empty.',
        );

        $rules = array(
            'location_code' => 'required',
            'location_aisle' => 'required',
            'location_column' => 'required',
            'location_level' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $id = $request->idLocation;
        DB::table('ex_location')
        ->updateOrInsert(
            ['id' =>  $id],
            [
                'branch_id'         => $request->branch,
                'location_code'     => $request->location_code,
                'location_name'     => $request->location_code,
                'location_aisle'    => $request->location_aisle,
                'location_column'   => $request->location_column,
                'location_level'    => $request->location_level,
            ]
        );
        return response()->json(['success' => 'Added new records.']);
    }

    public function editChecker($id)
    {
        $data  = DB::table('ex_master_checker')->where('id', $id)->first();
        return response()->json($data);
    }

    public function editLocation($id)
    {
        $data  = DB::table('ex_location')->where('id', $id)->first();
        return response()->json($data);
    }

    public function toggleLocation($id, $type)
    {
        if ($type == 'enable') {
            $status = 'Yes';
        } else {
            $status = 'No';
        }
        DB::table('ex_location')->where('id', $id)->update([
            'active' => $status
        ]);
        return response()->json(['success' => 'Added new records.']);
    }

    public function uploadLocation(Request $request)
    {
        $this->validate($request, [
            'excel' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('excel');
        Excel::import(new ImportLocation($request->branch_id), $file);
        return back();
    }
}
