<?php

namespace App\Http\Controllers\Transaction\Inbound;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use App\Models\Transaction\Export\InboundDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Session;

use App\Models\Transaction\Inbound\Job as inboundJob;
use App\Models\Transaction\Inbound\Detail as InboundDetails;

class JobController extends Controller
{
    public function __construct()
    {
        if (!GlobalHelpers::checkLogin()) {
            return response()->redirectTo("login");
        }
    }

    public $menu_name = "warehouse/inbound";

    public function index(Request $request)
    {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');


            $list_data = inboundJob::from('iv_inbound_job as a')
                ->select('a.*', 'v.vehicle_no')
                ->join('iv_principal as b', 'a.principal_id', 'b.id')
                ->join('users_principal as c', 'a.principal_id', 'c.principal_id')
                ->leftJoin(
                    DB::raw('(SELECT inbound_id, vehicle_no FROM iv_inbound_vehicle GROUP BY inbound_id) v'),
                    'a.id',
                    '=',
                    'v.inbound_id'
                )
                ->where('a.company_id', $company_id)
                ->where('c.user_id', $user_id)
                ->where('a.branch_id', $request->branch_id)
                ->where('a.principal_id', $request->principal_id)
                ->whereBetween('a.job_date', [$date_from, $date_to])
                ->where("a.confirmed_flag", "<>", "Cancel")
                ->where(DB::raw("case when a.confirmed_flag = 'Yes' then 'A' else 'O' end"), $request->status_code)
                ->get();

            return datatables()->of($list_data)
                ->editColumn('job_date', function ($data) {
                    return date('d/m/Y', strtotime($data->job_date));
                })
                ->editColumn('eta', function ($data) {
                    return date('d/m/Y', strtotime($data->eta));
                })
                ->editColumn('confirmed_flag', function ($data) {
                    if ($data->confirmed_flag == 'Yes') {
                        $status = '<div class="btn btn-sm btn-success">Completed</div>';
                    } else {
                        if ($data->received_flag == 'Yes' && $data->allocated_flag == "No") {
                            $status = '<div class="btn btn-sm btn-info">Received</div>';
                        } else if ($data->allocated_flag == "Yes") {
                            $status = '<div class="btn btn-sm btn-warning">Allocated</div>';
                        } else {
                            $status = '<div class="btn btn-sm btn-danger">Open</div>';
                        }
                    }
                    return $status;
                })
                ->addColumn('job_no', function ($data) {
                    $button = "";
                    $button .= '<a href="' . URL("/warehouse/inbound/create/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                    return $button;
                })
                ->rawColumns(['confirmed_flag', 'job_no'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('transaction.inbound.index');
    }

    public function create($id = "")
    {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        $user = Auth::user();
        $company_id = $user->company_id;
        $user_id = $user->id;

        $job_view = inboundJob::from('iv_inbound_job as a')
            ->select('a.*', 'c.multi_level', 'c.quality_flag', 'v.vehicle_no')
            ->join('users_principal as b', 'a.principal_id', 'b.principal_id')
            ->leftJoin('iv_inbound_vehicle as v', 'a.id', 'v.inbound_id')
            ->join('iv_principal as c', 'a.principal_id', 'c.id')
            ->where('b.user_id', $user_id)
            ->where('a.id', $id)
            ->first();
        $ata = null;
        if (!is_null($job_view)) {
            $ata = DB::table('iv_gate_in_cargo')
                ->select('gate_in_at')
                ->where('activity', 'INBOUND')
                ->where('vehicle_number', $job_view->vehicle_no)
                ->whereDate('gate_in_at', date('Y-m-d'))
                ->where('branch_id', $job_view->branch_id)
                ->value('gate_in_at');
        }

        $button_putaway = $job_view && $job_view->allocated_flag != 'Yes';

        $class_list = DB::table("iv_job_class")
            ->where('company_id', $company_id)
            ->where('active', 'Yes')->get();

        $mode_list = DB::table("iv_mode")
            ->where('company_id', $company_id)
            ->where('active', 'Yes')->get();

        $container_type = DB::table("iv_container_type")
            ->where('company_id', $company_id)
            ->where('active', 'Yes')->get();

        $container_size = DB::table("iv_container_size")
            ->where('company_id', $company_id)
            ->where('active', 'Yes')->get();

        $per_pallet = DB::table("iv_inbound_per_pallet")
            ->where('inbound_id', $id)
            ->get();

        $total_pallet = $per_pallet->count();
        $filled_location = $per_pallet->whereNotNull('location_code')->count();

        $button_gr = $total_pallet > 0 && $total_pallet == $filled_location;

        $site_arr = DB::table('users_site')
            ->where('user_id', $user_id)
            ->pluck('site_id');

        $location = DB::table('iv_location as a')
            ->select('a.location_code', 'a.id', 'b.site_name')
            ->join('iv_site as b', 'b.id', 'a.site_id')
            ->where('a.active', 'yes')
            ->whereIn('a.site_id', $site_arr)
            ->get();

        $list_data = InboundDetails::from('iv_inbound_detail as a')
            ->select('a.*', 'b.product_name', 'b.id as id_product', 'b.length', 'b.width', 'b.height')
            ->join('iv_product as b', 'a.product_id', 'b.id')
            ->where('a.company_id', $company_id)
            ->where('a.inbound_id', $id)
            ->whereNull('a.putaway_date')
            ->get();

        $pallet_summary = DB::table('iv_inbound_per_pallet')
            ->select(
                'picking_id',
                DB::raw("COUNT(*) as total"),
                DB::raw("SUM(CASE WHEN qrcode IS NOT NULL AND location_code IS NOT NULL THEN 1 ELSE 0 END) as filled")
            )
            ->where('inbound_id', $id)
            ->groupBy('picking_id')
            ->get()
            ->keyBy('picking_id');

        $list_data->map(function ($value) use ($pallet_summary) {
            $summary = $pallet_summary[$value->id] ?? null;

            $value->counting = $summary->total ?? 0;
            $value->wherenotnull = $summary->filled ?? 0;

            return $value;
        });

        $list_confirm = DB::table('iv_inbound_batch')
            ->where('inbound_id', $id)
            ->get();

        $vehicle  = DB::table('iv_gate_in_cargo')
            ->whereDate('gate_in_at', date('Y-m-d'))
            ->whereIn('site_id', $site_arr)
            ->where('activity', 'INBOUND')
            ->get();

        return view('transaction.inbound.create', [
            'job_view' => $job_view,
            'class_list' => $class_list,
            'mode_list' => $mode_list,
            'container_type_list' => $container_type,
            'container_size_list' => $container_size,
            'perpallet' => $per_pallet,
            'button_gr' => $button_gr,
            'button_putaway' => $button_putaway,
            'location' => $location,
            'list_data' => $list_data,
            'list_confirm' => $list_confirm,
            'vehicle' => $vehicle,
            'ata' => $ata
        ]);
    }

    function getDetailVehicle($vehicle_number)
    {
        $data = DB::table('iv_gate_in_cargo')
            ->where('vehicle_number', $vehicle_number)
            ->whereDate('gate_in_at', date('Y-m-d'))
            ->where('activity', 'INBOUND')
            ->first();

        return response()->json($data);
    }


    public function edit(Request $request)
    {
        $data = DB::table("iv_inbound_job")->find($request->inbound_id);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validate = DB::table('iv_freeze_job')
            ->where('branch_id', $request->branch_id)
            ->where('principal_id', $request->principal_id)
            ->where('status_flag', 'Run')
            ->where('freeze_activity', 'LIKE', '%inbound%')
            ->count();
        if ($validate > 0) {
            return response()->json(['error' => ['Principal access is temporarily restricted (Freeze).']]);
        }
        $user_id = Auth::user()->username;
        $id = $request->inbound_id;
        if ($id > 0) {
            $job_status = inboundJob::find($id);

            if ($job_status->received_flag == 'Yes') {
                return response()->json(['error' => ['Job Received.']]);
            }
        }

        $messsages = array(
            'branch_id.required' => 'Principal name cannot be empty.',
            'principal_id.required' => 'Principal name cannot be empty.',
            'class_id.required' => 'Job classification cannot be empty.',
            'mode_id.required' => 'Mode of transport cannot be empty.',
            'description.required' => 'Description cannot be empty.',
            // 'reference_no.required'=>'Reference number cannot be empty.',
            'eta.required' => 'ETA cannot be empty.',
        );

        $rules = array(
            'branch_id' => 'required',
            'principal_id' => 'required',
            'class_id' => 'required',
            'mode_id' => 'required',
            'description' => 'required',
            // 'reference_no' => 'required',
            'eta' => 'required',
            'user_id' => $user_id
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $company_id = Auth::user()->company_id;
        $job_date = \Carbon\Carbon::today();
        $entry_date = \Carbon\Carbon::now();

        $year = $job_date->year;
        $month = $job_date->month;

        if (empty($id)) {
            $job = inboundJob::where('company_id', $company_id)
                ->whereYear('job_date', $year)
                ->whereMonth('job_date', $month)
                ->max("job_no");

            if (is_null($job)) {
                $increment = 1;
            } else {
                $increment = substr($job, 7, 4) + 1;
            }

            $job_no = '1' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');
        } else {
            $job  = inboundJob::find($id);

            $job_no = $job->job_no;
        }
        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->eta);
        $eta = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $job   =   inboundJob::updateOrCreate(
            ['id' => $id],
            [
                'company_id' => $company_id,
                'branch_id' => $request->branch_id,
                'principal_id' => $request->principal_id,
                'job_no' => $job_no,
                'job_date' => $job_date,
                'class_id' => $request->class_id,
                'mode_id' => $request->mode_id,
                'description' => $request->description,
                // 'reference_no' => $request->reference_no,
                // 'reference_other' => $request->reference_other,
                'eta' => $eta,
                'remarks' => $request->remarks,
                'entry_date' => $entry_date
            ]
        );

        return response()->json(['success' => url('/warehouse/inbound/create/' . $job->id), 'inbound_id' => $job->id]);
    }

    public function add_per_pallet(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $total = array_sum($request->qtyPerPallet);
                $qty_inbound = $request->qty;

                //hapus dulu
                DB::table('iv_inbound_per_pallet')
                    ->where('picking_id', $request->picking_id)
                    ->delete();

                if ($total > $qty_inbound) {
                    return response()->json([
                        'status' => 'lebih_besar'
                    ]);
                } else if ($total < $qty_inbound) {
                    return response()->json([
                        'status' => 'lebih_kecil'
                    ]);
                } else {
                    $detail = DB::table('iv_inbound_detail')
                        ->select('qrcode', 'ean_code')
                        ->where('id', $request->picking_id)
                        ->get();
                    $ean_code_string = $detail[0]->ean_code;
                    $ean_code_array = explode(',', $ean_code_string);

                    if (count($request->qtyPerPallet) > 0) {
                        $index = 0;
                        for ($i = 0; $i < count($request->qtyPerPallet); $i++) {
                            $qtyPerPallet = (int) $request->qtyPerPallet[$i];
                            $ean_codes_to_insert = array_slice($ean_code_array, $index, $qtyPerPallet);
                            $index += $qtyPerPallet;
                            DB::table('iv_inbound_per_pallet')->insert([
                                'inbound_id' => $request->inbound_id,
                                'picking_id' => $request->picking_id,
                                'qrcode' => $detail[0]->qrcode,
                                'total_qty' => $request->qty,
                                'product_code' => $request->product_code,
                                'total_pallet' => $request->jumlah_pallet,
                                'qty_per_pallet' => $qtyPerPallet,
                                'ean_code' => !empty($ean_codes_to_insert) ? implode(',', $ean_codes_to_insert) : null,
                                'created_at' => now(),
                                'created_by' => Auth::user()->username,
                            ]);
                        }
                    }
                    $message = ['message' => 'ok'];
                }
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function byPassScan($inbound_id)
    {
        DB::transaction(function () use ($inbound_id) {
            try {
                $data = DB::table('iv_inbound_detail')
                    ->where('inbound_id', $inbound_id)
                    ->get();

                foreach ($data as $key => $value) {
                    DB::table('iv_inbound_per_pallet')
                        ->where('picking_id', $value->id)
                        ->where('inbound_id', $value->inbound_id)
                        ->update([
                            'qrcode' => $value->qrcode,
                            'scan_pallet_tag' => 'Yes',
                        ]);
                }
                DB::commit();
                Session::flash('success', 'Berhasil bypass, Silahkan Start Putaway..');
                return back();
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ["error" => $e->getMessage()];
                Session::flash('success', $message);
                return back();
            }
        });
        return back();
    }
}
