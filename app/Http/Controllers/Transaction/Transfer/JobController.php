<?php

namespace App\Http\Controllers\Transaction\Transfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Transaction\Transfer\Job as TransferJob;
use App\Exports\TransferLokasiTemplate as MovementTemplate;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            if (!empty($request->principal_id)) {
                $details = DB::table("iv_transfer_job as a")
                                ->select('a.*')
                                ->join('users_principal as b', 'a.principal_id', 'b.principal_id')
                                ->where('b.user_id', $user_id)
                                ->where('a.company_id', $company_id)
                                ->where('a.branch_id', $request->branch_id)
                                ->where('a.principal_id', $request->principal_id)
                                ->where("a.confirmed_flag", $request->confirmed_flag)
                                ->whereBetween('a.job_date', [$date_from, $date_to])
                                ->get();
            } else {
                $details = DB::table("iv_transfer_job as a")
                                ->select('a.*')
                                ->join('users_principal as b', 'a.principal_id', 'b.principal_id')
                                ->where('b.user_id', $user_id)
                                ->where('a.company_id', $company_id)
                                ->whereBetween('a.job_date', [$date_from, $date_to])
                                ->where("a.confirmed_flag", $request->confirmed_flag)
                                ->get();
            }

            return datatables()->of($details)
            ->editColumn('job_date', function ($data)
            {
                return date('d/m/Y', strtotime($data->job_date) );
            })
            ->editColumn('confirmed_flag', function ($data) {
                if ($data->confirmed_flag == 'Yes') {
                    $status = '<div class="btn btn-sm btn-success">Completed</div>';
                } else {
                    $status = '<div class="btn btn-sm btn-danger">Open</div>';
                }
                return $status;
            })
            ->addColumn('job_no', function($data){
                $button = "";
                $button .= '<a href="' . URL("/inventory/stock-transfer/create/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                return $button;
            })
            ->rawColumns(['confirmed_flag', 'job_no'])
            ->addIndexColumn()
            ->make(true);
        }

        return view('transaction.transfer.index');
    }

    public function create($id = "") {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        $job_view = TransferJob::from("iv_transfer_job as a")
                        ->select('a.*')
                        ->join('users_principal as b', 'a.principal_id', 'b.principal_id')
                        ->where('b.user_id', $user_id)
                        ->where('a.company_id', $company_id)
                        ->where('a.id', $id)
                        ->first();

        $data = [ 'job_view' => $job_view ];

        return view('transaction.transfer.create', $data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            'branch_id.required'=>'Principal name field is required.',
            'principal_id.required'=>'Principal name field is required.',
            'description.required'=>'Description field is required.',
        );

        $rules = array(
            'branch_id' => 'required',
            'principal_id' => 'required',
            'description' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->transfer_id;
        $company_id = Auth::user()->company_id;
        $job_date = \Carbon\Carbon::today();
        $entry_date = \Carbon\Carbon::now();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = TransferJob::where('company_id', $company_id)
                         ->whereYear('job_date', $year)
                         ->whereMonth('job_date', $month)->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }

        $job_no = '3' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        $data   =   TransferJob::updateOrCreate(['id' => $id],
                    [
                        'company_id'=>$company_id,
                        'branch_id'=>$request->branch_id,
                        'principal_id'=>$request->principal_id,
                        'job_no' => $job_no,
                        'job_date' => $job_date,
                        'description' => $request->description,
                        'entry_date' => $entry_date
                    ]);

        return response()->json(['success'=>url('/inventory/stock-transfer/create/' . $data->id)]);
    }

    public function downloadTemplate($job_id){
		return Excel::download(new MovementTemplate($job_id), "template-transfer-lokasi.xlsx");
    }
}
