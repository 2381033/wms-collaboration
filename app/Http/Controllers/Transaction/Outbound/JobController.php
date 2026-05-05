<?php

namespace App\Http\Controllers\Transaction\Outbound;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Session;
use Illuminate\Support\Carbon;

use App\Models\Transaction\Outbound\Job as outboundJob;
use App\Models\Master\ModeOfTransport as ModeOfTransport;
use App\Models\Master\JobClass as JobClass;


class JobController extends Controller
{
    public $menu_name = "warehouse/outbound";

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

            $list_data = outboundJob::from('iv_outbound_job as a')
                ->select('a.*')
                ->join('iv_principal as b', 'a.principal_id', 'b.id')
                ->join('users_principal as c', 'a.principal_id', 'c.principal_id')
                ->where('a.company_id', $company_id)
                ->where('c.user_id', $user_id)
                ->where('a.branch_id', $request->branch_id)
                ->where('a.principal_id', $request->principal_id)
                ->whereBetween('a.job_date', [$date_from, $date_to])
                ->where("a.confirmed_flag", "<>", "Cancel")
                ->where(DB::raw("case when a.confirmed_flag = 'Yes' then 'A' else 'O' end"), $request->status_code)
                ->get();

            return datatables()->of($list_data)
                // ->editColumn('job_date', function ($data) {
                //     return date('d/m/Y', strtotime($data->job_date));
                // })
                // ->editColumn('eta', function ($data) {
                //     return date('d/m/Y', strtotime($data->eta));
                // })
                ->editColumn('confirmed_flag', function ($data) {
                    if ($data->confirmed_flag == 'Yes') {
                        $status = '<div class="btn btn-sm btn-success">Completed</div>';
                    } else {
                        if ($data->allocated_flag == 'Yes') {
                            $status = '<div class="btn btn-sm btn-warning">Allocated</div>';
                        } else {
                            $status = '<div class="btn btn-sm btn-danger">Open</div>';
                        }
                    }
                    return $status;
                })
                ->addColumn('job_no', function ($data) {
                    $button = "";
                    $button .= '<a href="' . URL("/warehouse/outbound/create/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                    return $button;
                })
                ->rawColumns(['confirmed_flag', 'job_no'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('transaction.outbound.index');
    }

    public function create($id = "")
    {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        $job_view = outboundJob::from('iv_outbound_job as a')
            ->select('a.*', "c.multi_level")
            ->join('users_principal as b', 'a.principal_id', 'b.principal_id')
            ->join('iv_principal as c', 'a.principal_id', 'c.id')
            ->where('b.user_id', $user_id)
            ->where('a.id', $id)
            ->first();

        $class_list = JobClass::where('company_id', $company_id)
            ->where('active', 'Yes')->get();
        $mode_list = ModeOfTransport::where('company_id', $company_id)
            ->where('active', 'Yes')->get();

        $list_sku = DB::table('iv_outbound_batch')->where('outbound_id', $id)->get();
        $count_sku = $list_sku->count();
        $validasi = $list_sku->whereNotNull('location_confirm_at')->count();
        if ($count_sku > 0 and $count_sku == $validasi) {
            $confirm_checker = true;
        } else {
            $confirm_checker = false;
        }
        $container_size = DB::table('iv_container_size')->select('id', 'size_name')->orderBy('size_name', 'asc')->get();
        $site_arr = DB::table('users_site')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('site_id')
            ->toArray();

        $location = DB::table('iv_location')
            ->where('active', 'yes')
            ->whereIn('site_id', $site_arr)
            ->get();

        $data = [
            'job_view' => $job_view,
            'class_list' => $class_list,
            'mode_list' => $mode_list,
            'confirm_checker' => $confirm_checker,
            'location' => $location,
            'container_size' => $container_size
        ];


        return view('transaction.outbound.create', $data);
    }

    public function getListPickByChecker($outbound_id)
    {
        $data = DB::table("iv_outbound_batch as a")
            ->select(
                "a.product_code",
                "a.product_id",
                "a.location_id",
                "a.id",
                "b.product_name",
                "a.location_code",
                "a.qty",
                "a.lot_no",
                "a.scan_pallet_tag",
                "a.scan_location",
                "b.puom",
            )
            ->join("iv_product as b", "a.product_id", "b.id")
            ->where("a.outbound_id", $outbound_id)
            ->orderBy("a.location_code", 'ASC')
            ->get();
        $data = $data->map(function ($value) {
            $value->soa = DB::table('iv_stock_ledger')
                ->select('qtya')
                ->where('lot_no', $value->lot_no)
                ->where('location_id', $value->location_id)
                ->where('product_id', $value->product_id)
                ->sum('qtya');
            return $value;
        });
        return response()->json($data);
    }

    public function edit(Request $request)
    {
        $data = outboundJob::find($request->outbound_id);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $user_id = Auth::user()->username;
        $id = $request->outbound_id;

        if ($id > 0) {
            $job_status = outboundJob::find($id);

            if ($job_status->received_flag == 'Yes') {
                return response()->json(['error' => ['Job Received.']]);
            }
        }

        $messsages = array(
            'branch_id.required' => 'Branch name cannot be empty.',
            'principal_id.required' => 'Principal name cannot be empty.',
            'class_id.required' => 'Job classification cannot be empty.',
            'mode_id.required' => 'Mode of transport cannot be empty.',
            'description.required' => 'Description cannot be empty.',
            // 'reference_no.required'=>'Reference number cannot be empty.',
            'etd.required' => 'ETD cannot be empty.',
        );

        $rules = array(
            'branch_id' => 'required',
            'principal_id' => 'required',
            'class_id' => 'required',
            'mode_id' => 'required',
            'description' => 'required',
            // 'reference_no' => 'required',
            'etd' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $company_id = Auth::user()->company_id;
        $entry_date = \Carbon\Carbon::now();
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        if (empty($id)) {
            $job = outboundJob::where('company_id', $company_id)
                ->whereYear('job_date', $year)
                ->whereMonth('job_date', $month)
                ->max("job_no");

            if (is_null($job)) {
                $increment = 1;
            } else {
                $increment = substr($job, 7, 4) + 1;
            }

            $job_no = '2' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');
        } else {
            $job  = outboundJob::find($id);

            $job_no = $job->job_no;
        }

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->etd);
        $etd = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $job   =   outboundJob::updateOrCreate(
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
                'etd' => $etd,
                'remarks' => $request->remarks,
                'entry_date' => $entry_date,
                'user_id' => $user_id
            ]
        );

        return response()->json(['success' => url('/warehouse/outbound/create/' . $job->id), 'outbound_id' => $job->id]);
    }

    public function updateETD($tanggal, $outbound_id)
    {
        $tanggal = date('Y-m-d', strtotime($tanggal));
        DB::table('iv_outbound_job')->where('id', $outbound_id)->update([
            'etd' => $tanggal
        ]);

        DB::table('iv_outbound_despatch')->where('outbound_id', $outbound_id)->update([
            'etd' => $tanggal
        ]);

        DB::table('iv_outbound_order')->where('outbound_id', $outbound_id)->update([
            'due_date' => $tanggal
        ]);

        Session::flash('success', 'Update Successfully..');
        return response()->json([
            'status' => 'ok',
        ]);
    }

    public function scanLokasi($location_code)
    {
        $mySite = DB::table('users_site')->where('user_id', Auth::user()->id)->pluck('site_id')->toArray();
        $data = DB::table('iv_location')
            ->where('location_code', $location_code)
            ->whereIn('site_id', $mySite)
            ->first();

        return response()->json([
            'data' => $data,
        ]);
    }

    public function postScanLokasi(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $master = DB::table('iv_outbound_batch')
                    ->where('id', $request->id)
                    ->first()->location_code;
                if ($master == $request->location_code) {
                    $master = DB::table('iv_outbound_batch')
                        ->where('id', $request->id)
                        ->update([
                            'location_confirm'    => 'Y',
                            'location_confirm_at' => date('Y-m-d H:i:s'),
                            'scan_location' => 'Y',
                            'scan_location_by' => Auth::user()->username,
                            'scan_location_at' => date('Y-m-d H:i:s'),
                        ]);

                    $message = ['status' => 'ok'];
                } else {
                    $message = ['status' => 'notok'];
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

    public function scanPalletTag($id_batch, $product_code)
    {
        $exception = DB::transaction(function () use ($id_batch, $product_code) {
            try {
                $master = DB::table('iv_outbound_batch')
                    ->where('id', $id_batch)
                    ->first();
                if (is_null($master)) {
                    $message = ['status' => 'null'];
                } else {
                    if ($master->product_code != $product_code) {
                        $message = ['status' => 'not_same'];
                    } else {
                        //jika principal mostrans dan satoria maka bypass scan location
                        $principal_bypass = [32, 37];
                        if (in_array($master->principal_id, $principal_bypass)) {
                            DB::table('iv_outbound_batch')
                                ->where('id', $id_batch)
                                ->update([
                                    'location_confirm'    => 'Y',
                                    'location_confirm_at' => date('Y-m-d H:i:s'),
                                    'qty_checker' => 0,
                                    'scan_pallet_tag' => 'Y',
                                    'scan_pallet_by' => Auth::user()->username,
                                    'scan_pallet_at' => date('Y-m-d H:i:s'),
                                    'scan_location' => 'Y',
                                    'scan_location_by' => Auth::user()->username,
                                    'scan_location_at' => date('Y-m-d H:i:s'),
                                ]);
                        }
                        $message = ['status' => 'success', 'data' => $master];
                    }
                }
                DB::commit();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function validasiQtyBatch(Request $request)
    {
        DB::beginTransaction();
        try {
            $master = DB::table('iv_outbound_batch')
                ->where('id', $request->id_batch)
                ->first();
            if ((int)$request->qty > (int)$master->qty) {
                return response()->json([
                    'status' => 'lebih_besar',
                ]);
            }
            if ((int)$master->qty == (int)$request->qty) {
                DB::table('iv_outbound_batch')
                    ->where('id', $request->id_batch)
                    ->update([
                        'qty_checker' => $request->qty - (int)$request->qty,
                        'scan_pallet_tag' => 'Y',
                        'scan_pallet_by' => Auth::user()->username,
                        'scan_pallet_at' => date('Y-m-d H:i:s'),
                    ]);

                DB::commit();
                return response()->json([
                    'status' => 'scanned',
                ]);
            } else {
                DB::table('iv_outbound_batch')
                    ->where('id', $request->id_batch)
                    ->update([
                        // 'location_confirm'    => 'Y',
                        // 'location_confirm_at' => date('Y-m-d H:i:s'),
                        'qty'         => $request->qty,
                        'pqty'         => $request->qty,
                        'qty_checker' => (int)$master->qty - (int)$request->qty,
                        'scan_pallet_tag' => 'Y',
                        'scan_pallet_by' => Auth::user()->username,
                        'scan_pallet_at' => date('Y-m-d H:i:s'),
                        // 'scan_location' => 'Y',
                        // 'scan_location_by' => Auth::user()->username,
                        // 'scan_location_at' => date('Y-m-d H:i:s'),
                    ]);


                //update qty despatch
                $qty_batch = DB::table('iv_outbound_batch')
                    ->where('outbound_id', $request->outbound_id)
                    ->sum('qty');

                DB::table('iv_outbound_despatch')
                    ->where('outbound_id', $request->outbound_id)
                    ->update([
                        'pqty' => $qty_batch
                    ]);
                DB::commit();
                return response()->json([
                    'status' => 'ok',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            $message = ["error" => $e->getMessage()];
            return $message;
            Session::flash('success', $message);
            return back();
        }
    }

    public function byPassScan($outbound_id)
    {
        DB::beginTransaction();
        try {
            DB::table('iv_outbound_batch')
                ->where('outbound_id', $outbound_id)
                ->update([
                    'location_confirm'    => 'Y',
                    'location_confirm_at' => date('Y-m-d H:i:s'),
                    'qty_checker' => 0,
                    'scan_pallet_tag' => 'Y',
                    'scan_pallet_by' => Auth::user()->username,
                    'scan_pallet_at' => date('Y-m-d H:i:s'),
                    'scan_location' => 'Y',
                    'scan_location_by' => Auth::user()->username,
                    'scan_location_at' => date('Y-m-d H:i:s'),
                ]);

            DB::commit();
            Session::flash('success', 'Data has been saved successfully');
        } catch (\Exception $e) {
            DB::rollback();
            $message = ["error" => $e->getMessage()];
            return $message;
            Session::flash('success', $message);
            return back();
        }
        return back();
    }
}
