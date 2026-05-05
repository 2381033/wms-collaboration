<?php

namespace App\Http\Controllers\Transaction\Export\Outbound;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public $menu_name = "export/outbound";

    public function index(Request $request)
    {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        if ($request->ajax()) {
            $date_from = \Carbon\Carbon::parse($request->date_from)->format('Y-m-d');
            $date_to = \Carbon\Carbon::parse($request->date_to)->format('Y-m-d');

            $list_data = \App\Models\Transaction\Outbound\Job::from('ex_outbound_header as a')
                ->select('a.*', "b.forwarder_name")
                ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                ->where('a.branch_id', $request->branch_id)
                ->whereBetween('a.job_date', [$date_from, $date_to])
                ->where('a.status_flag', $request->status_flag)
                ->orderBy("a.job_no", "desc")
                ->get();

            return datatables()->of($list_data)
                ->editColumn('job_date', function ($data) {
                    return date('d/m/Y', strtotime($data->job_date));
                })
                ->addColumn('job_no', function ($data) {
                    $button = "";
                    $button .= '<a href="' . URL("/export/outbound/create/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                    return $button;
                })
                ->rawColumns(['job_no'])
                ->addIndexColumn()
                ->make(true);
        }

        return view("transaction.export.outbound.index");
    }
    public function create($id = 0)
    {
        $company_id = Auth::user()->company_id;

        $header = \App\Models\Transaction\Inbound\Job::from('ex_outbound_header as a')
            ->select('a.*', "b.forwarder_name")
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->where('a.id', $id)
            ->first();

        $size_list = DB::table("iv_container_size")->where("company_id", $company_id)->where("active", "Yes")->get();

        $data = [
            "header" => $header,
            "size_list" => $size_list
        ];

        return view("transaction.export.outbound.create", $data);
    }

    public function store(Request $request)
    {
        $rules = array(
            'branch_id' => 'required',
            'forwarder_id' => 'required',
            'size_id' => 'required',
            'container_no' => 'required',
            'destination' => 'required',
            // 'surveyor_name' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            try {
                $user_name = Auth::user()->username;
                $id = $request->id;

                $job = \App\Models\Transaction\Export\OutboundHeader::find($id);

                if (!isset($job)) {
                    $isDuplicate = \App\Models\Transaction\Export\OutboundHeader::where('container_no', Str::Upper($request->container_no))
                        ->where('destination', Str::Upper($request->destination))
                        ->where('forwarder_id', $request->forwarder_id)
                        ->exists();

                    if ($isDuplicate) {
                        return ['error' => ['Job dengan No. Container ini sudah dibuat hari ini.']];
                    }

                    $job = new \App\Models\Transaction\Export\OutboundHeader();

                    $job_no = $this->getJob($request->branch_id);

                    $job->job_no = $job_no;
                    $job->job_date = \Carbon\Carbon::today();
                }

                $job->branch_id = $request->branch_id;
                $job->forwarder_id = $request->forwarder_id;
                $job->size_id = $request->size_id;
                $job->container_no = Str::Upper($request->container_no);
                $job->destination = Str::Upper($request->destination);
                $job->vessel_name = Str::Upper($request->vessel_name);
                $job->surveyor_name = Str::Upper($request->surveyor_name);
                $job->voyage_no = Str::Upper($request->voyage_no);
                $job->remarks = Str::Upper($request->remarks);
                $job->qty_cargo = 0;
                $job->cbm = 0;
                $job->weight = 0;
                $job->total_pallet = 0;
                $job->user_id = $user_name;
                $job->save();

                DB::commit();

                $message = ['success' => url('/export/outbound/create/' . $job->id)];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    private function getJob($branch_id)
    {
        $job_date = \Carbon\Carbon::today();
        $year = $job_date->year;
        $month = $job_date->month;

        $lastJob = \App\Models\Transaction\Export\InboundHeader::whereYear('branch_id', $branch_id)
            ->whereMonth('job_date', $month)
            ->lockForUpdate()
            ->orderBy('job_no', 'desc')
            ->first();

        $job = \App\Models\Transaction\Export\OutboundHeader::whereYear('job_date', $year)
            ->whereMonth('job_date', $month)
            ->max("job_no");

        if (!$lastJob) {
            $increment = 1;
        } else {
            $increment = (int) substr($lastJob->job_no, 7, 4) + 1;
        }

        $job_no = 'I' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($increment, 4, '0', STR_PAD_LEFT);

        return $job_no;
    }

    public function submitOldPakFirman(Request $request)
    {
        $result = DB::transaction(function () use ($request) {
            try {
                $userName = Auth::user()->username;
                $now = now();

                $job = \App\Models\Transaction\Export\OutboundHeader::findOrFail($request->job_id);
                $orders = \App\Models\Transaction\Export\OutboundOrder::where('job_id', $job->id)->get();

                $transactionRows = [];

                foreach ($orders as $order) {
                    $details = \App\Models\Transaction\Export\OutboundDetail::where([
                        ['job_id', '=', $job->id],
                    ])->get();

                    $confirmedCount = $details->where('status_flag', 'Confirmed')->count();

                    if ($confirmedCount === 0) {
                        throw new \Exception("Pallets unprocessed for order {$order->id}");
                    }

                    $status = ($confirmedCount >= $order->total_pallet) ? 'Full' : 'Partial';
                    $order->update(['status_flag' => $status]);

                    $confirmedDetails = $details->where('status_flag', 'Confirmed');

                    foreach ($confirmedDetails as $detail) {
                        $transactionRows[] = [
                            'job_type' => 'out',
                            'branch_id' => $job->branch_id,
                            'job_no' => $job->job_no,
                            'po_number' => $detail->po_number ?? null,
                            'vehicle_no' => $job->container_no,
                            'forwarder_id' => $job->forwarder_id,
                            'shipper_id' => $job->shipper_id,
                            'consignee_id' => $job->consignee_id,
                            'destination' => $job->destination ?? null,
                            'peb_no' => $detail->peb_no,
                            'aju_no' => $detail->aju_no ?? null,
                            'serial_no' => $detail->serial_no,
                            'pallet_id' => $detail->pallet_id,
                            'quantity' => $detail->quantity,
                            'cbm' => $job->cbm ?? 0,
                            'weight' => $job->weight ?? 0,
                            'total_pallet' => $order->total_pallet ?? 0,
                            'qty_cargo' => $details->sum('quantity'),
                            'user_id' => $userName,
                            'created_at' => $now,
                        ];
                    }
                }

                if (!empty($transactionRows)) {
                    DB::table('ex_stock_transaction')->insert($transactionRows);
                }

                $hasOpenOrders = \App\Models\Transaction\Export\OutboundOrder::where('job_id', $job->id)
                    ->where('status_flag', '!=', 'Full')
                    ->exists();

                if (!$hasOpenOrders) {
                    $job->update(['status_flag' => 'Confirmed']);
                }

                return ['success' => 'Success'];
            } catch (\Exception $e) {
                throw $e;
            }
        });

        return response()->json($result);
    }

    public function submit(Request $request)
    {
        $result = DB::transaction(function () use ($request) {
            try {
                $userName = Auth::user()->username;
                $now = now();

                $job = \App\Models\Transaction\Export\OutboundHeader::findOrFail($request->job_id);
                $orders = \App\Models\Transaction\Export\OutboundOrder::where('job_id', $job->id)->get();

                $detailsByJob = \App\Models\Transaction\Export\OutboundDetail::where('job_id', $job->id)->get();

                $transactionRows = [];

                foreach ($orders as $order) {

                    $details = $detailsByJob;

                    $confirmedCount = $details->where('status_flag', 'Confirmed')->count();

                    if ($confirmedCount === 0) {
                        throw new \Exception("Pallets unprocessed for order {$order->id}");
                    }

                    $status = ($confirmedCount >= $order->total_pallet) ? 'Full' : 'Partial';
                    $order->update(['status_flag' => $status]);

                    $confirmedDetails = $details->where('status_flag', 'Confirmed');

                    foreach ($confirmedDetails as $detail) {
                        $transactionRows[] = [
                            'job_type' => 'out',
                            'branch_id' => $job->branch_id,
                            'job_no' => $job->job_no,
                            'po_number' => $detail->po_number ?? null,
                            'vehicle_no' => $job->container_no,
                            'forwarder_id' => $job->forwarder_id,
                            'shipper_id' => $job->shipper_id,
                            'consignee_id' => $job->consignee_id,
                            'destination' => $job->destination ?? null,
                            'peb_no' => $detail->peb_no,
                            'aju_no' => $detail->aju_no ?? null,
                            'serial_no' => $detail->serial_no,
                            'pallet_id' => $detail->pallet_id,
                            'quantity' => $detail->quantity,
                            'cbm' => $job->cbm ?? 0,
                            'weight' => $job->weight ?? 0,
                            'total_pallet' => $order->total_pallet ?? 0,
                            'qty_cargo' => $details->sum('quantity'),
                            'user_id' => $userName,
                            'created_at' => $now,
                        ];
                    }
                }

                if (!empty($transactionRows)) {
                    foreach (array_chunk($transactionRows, 500) as $chunk) {
                        DB::table('ex_stock_transaction')->insert($chunk);
                    }
                }

                $hasOpenOrders = \App\Models\Transaction\Export\OutboundOrder::where('job_id', $job->id)
                    ->where('status_flag', '!=', 'Full')
                    ->exists();

                if (!$hasOpenOrders) {
                    $job->update(['status_flag' => 'Confirmed']);
                }

                return ['success' => 'Success'];
            } catch (\Exception $e) {
                throw $e;
            }
        });

        return response()->json($result);
    }


    public function update(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job = \App\Models\Transaction\Export\OutboundHeader::find($request->job_id);

                $job->surveyor_name = $request->surveyor_name;
                $job->vessel_name = $request->vessel_name;
                $job->voyage_no = $request->voyage_no;
                $job->save();

                DB::commit();

                $message = ['success' => "Success"];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
