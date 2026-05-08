<?php

namespace App\Http\Controllers\Transaction\Export\Inbound;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Master\Export\Forwarder as ExportForwarder;
use App\Models\Transaction\Export\InboundHeader as ExportInboundHeader;
use App\Models\Transaction\Export\InboundDetail as ExportInboundDetail;
use App\Models\Transaction\Inbound\Job as InboundJob;
use App\Models\Master\Export\Consignee as ExportConsignee;
use App\Models\Master\Export\Shipper as ExportShipper;
use App\Models\Transaction\Export\StockLedger as ExportStockLedger;
use App\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;

class JobController extends Controller
{
    public $menu_name = "export/inbound";

    public function index(Request $request)
    {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        if ($request->ajax()) {
            $date_from = \Carbon\Carbon::parse($request->date_from)->format('Y-m-d');
            $date_to = \Carbon\Carbon::parse($request->date_to)->format('Y-m-d');

            $list_data = InboundJob::from('ex_inbound_header as a')
                ->select('a.*', "b.forwarder_name", "c.consignee_name", "d.shipper_name")
                ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                ->join("mt_consignee as c", "a.consignee_id", "c.id")
                ->join("mt_shipper as d", "a.shipper_id", "d.id")
                ->where('a.branch_id', $request->branch_id)
                ->whereBetween('a.job_date', [$date_from, $date_to])
                ->where('a.status_flag', $request->status_flag)
                ->orderBy("a.job_no", "desc")
                ->get();

            $list_data = $list_data->map(function ($value) {
                $value->qty_actual = DB::table('ex_inbound_detail')
                    ->where('job_id', $value->id)
                    ->sum('quantity');
                if (!empty($value->qty_cargo)) {
                    $qtyArray = explode('|', $value->qty_cargo);
                    $qtySum = array_sum(array_map('intval', $qtyArray));
                    $value->qty_summary = $qtySum;
                } else {
                    $value->qty_summary = 0;
                }
                return $value;
            });


            return datatables()->of($list_data)
                ->editColumn('job_date', function ($data) {
                    return date('d/m/Y', strtotime($data->job_date));
                })
                ->addColumn('job_no', function ($data) {
                    $button = "";
                    $button .= '<a href="' . URL("/export/inbound/show/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                    return $button;
                })
                ->rawColumns(['job_no'])
                ->addIndexColumn()
                ->make(true);
        }

        return view("transaction.export.inbound.index");
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $headerSheet = $spreadsheet->getActiveSheet();
        $headerSheet->setTitle('Header');

        $headerSheet->fromArray([
            [
                'Forwarder Name',
                'Shipper Name',
                'Consignee Name',
                'PEB No',
                'AJU No',
                'VGM (Kg) by Doc',
                'Destination',
                'Final Destination'
            ],
        ]);
        $detailSheet = $spreadsheet->createSheet();
        $detailSheet->setTitle('Detail');

        $detailSheet->fromArray([
            [
                'PO No',
                'Qty',
                'Vol CBM',
            ],
        ]);
        $fileName = 'template_receiving.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function create()
    {
        $branch_id  = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->pluck('branch_id')->toArray();

        $auth_group_id = DB::table('auth_group')
            ->where('name', 'Checker')
            ->value('id');

        $checker = DB::table("users")
            ->where("auth_group_id", $auth_group_id)
            ->get();

        $branch = DB::table('mt_branch')
            ->whereIn('id', $branch_id)
            ->get();

        $vehicle = DB::table("ex_gate_in_cargo")
            ->select('vehicle_number', 'id')
            ->where("confirmed_flag", "No")
            ->whereDate('created_at', date('Y-m-d'))
            ->get();



        $data = [
            'checker' => $checker,
            'branch' => $branch,
            'vehicle' => $vehicle,
        ];

        return view("transaction.export.inbound.create", $data);
    }
    public function gateTime($nopol)
    {
        $data = DB::table('ex_gate_in_cargo')
            ->where('vehicle_number', $nopol)
            ->orderBy('id', 'desc')
            ->first();
        return response()->json($data);
    }

    public function show($id)
    {
        $header = InboundJob::from('ex_inbound_header as a')
            ->select('a.*', "b.forwarder_name", "c.consignee_name", "d.shipper_name")
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->join("mt_consignee as c", "a.consignee_id", "c.id")
            ->join("mt_shipper as d", "a.shipper_id", "d.id")
            ->where('a.id', $id)
            ->first();
        $arr = [];
        $po = $header->po_number;
        $qty_cargo = $header->qty_cargo;
        $sum_qty_cargo =  explode('|', $qty_cargo);
        $sum_qty_cargo =  array_sum($sum_qty_cargo);
        $cbm = $header->cbm;
        $arr = array(
            'po_number' => $po,
            'qty_cargo' => $qty_cargo,
            'cbm' => $cbm
        );
        $draft = [];
        foreach ($arr as $key => $value) {
            $draft[$key] = explode('|', $value);
        }

        $branchme = DB::table('mt_branch')
            ->where('id', $header->branch_id)
            ->first();

        $detail = DB::table('ex_inbound_detail')
            ->where('job_id', $header->id)
            ->get();
        $po_number = $detail->map(function ($item) {
            $segments = explode('-', $item->serial_no);
            $item->po_number = $segments[0] ?? null;
            return $item;
        })->groupBy('po_number');

        $branch_id  = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->pluck('branch_id')->toArray();
        $branch = DB::table('mt_branch')
            ->whereIn('id', $branch_id)
            ->get();
        $uom = DB::table('rt_uom')
            ->orderBy('code', 'ASC')
            ->pluck('code')->toArray();
        $auth_group_id = DB::table('auth_group')->get();
        $users = DB::table('users')->where('active', 'Yes')->get();

        $checker = $auth_group_id->where('name', 'Checker Export')->first()->id;
        $checker =  $users->where("auth_group_id", $checker);

        $stapel = $auth_group_id->where('name', 'Stapel')->first()->id;
        $stapel = $users->where("auth_group_id", $stapel);
        $data = [
            "header" => $header,
            'checker' => $checker,
            'branch' => $branch,
            'branchme' => $branchme,
            'detail' => $detail,
            'stapel' => $stapel,
            'draft' => $draft,
            'uom' => $uom,
            'po_number' => $po_number,
        ];

        return view("transaction.export.inbound.show", $data);
    }
    private function myBranch($user_id)
    {
        $branch = DB::table('sm_user_branch')
            ->where('user_id', $user_id)
            ->first()->branch_id;

        return $branch;
    }

    private function getOrCreateMaster($model, $column, $value, $branchId, $label)
    {
        $query = $model::where($column, 'LIKE', '%' . $value . '%')
            ->where('branch_id', $branchId);

        $count = $query->count();

        if ($count === 0) {
            $data = new $model();
            $data->$column  = $value;
            $data->branch_id = $branchId;
            $data->save();

            return $data->id;
        }

        if ($count === 1) {
            return $query->first()->id;
        }

        DB::rollBack();
        $message = ['error' => ["More than one name of {$label}"]];
        return $message;
    }

    public function store(Request $request)
    {
        $rules = array(
            'branch_id' => 'required',
            'vehicle_no' => 'required',
            'forwarder_name' => 'required',
            'shipper_name' => 'required',
            'consignee_name' => 'required',
            'final_destination' => 'required',
            'destination' => 'required',
            'peb_no' => 'required',
            'aju_no' => 'required',
            'vgm' => 'required',
            // 'gateDate' => 'required',
            // 'gateTime' => 'required',
            // 'vehicleNumber' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            try {
                $user_name = Auth::user()->username;
                $id = $request->id;
                $job = ExportInboundHeader::find($id);
                $isNew = false;

                if (!isset($job)) {
                    $job = new ExportInboundHeader();
                    $isNew = true;

                    $job_no = $this->getJob($request->branch_id);

                    $job->job_no = $job_no;
                    $job->job_date = \Carbon\Carbon::today();
                }

                $forwarder_count = DB::table('mt_forwarder as a')
                    ->where('a.forwarder_name', 'LIKE', "$request->forwarder_name")
                    ->where('branch_id', $request->branch_id)
                    ->orderBy("a.forwarder_name")
                    ->count();

                if ($forwarder_count == 0) {
                    $forwarder = new ExportForwarder();
                    $forwarder->forwarder_name = $request->forwarder_name;
                    $forwarder->branch_id      = $request->branch_id;
                    $forwarder->save();

                    $forwarder_id = $forwarder->id;
                } else if ($forwarder_count == 1) {
                    $forwarder_id = ExportForwarder::where("forwarder_name", $request->forwarder_name)
                        ->where('branch_id', $request->branch_id)
                        ->first()->id;
                } else {
                    DB::rollBack();

                    $message = ['error' => ["More than one name of forwarder"]];

                    return $message;
                }

                $consignee_count = DB::table('mt_consignee as a')
                    ->orderBy("a.consignee_name")
                    ->where('a.consignee_name', 'LIKE', "$request->consignee_name")
                    ->where('branch_id', $request->branch_id)
                    ->count();


                if ($consignee_count == 0) {
                    $consignee = new ExportConsignee();
                    $consignee->consignee_name = $request->consignee_name;
                    $consignee->branch_id      = $request->branch_id;
                    $consignee->save();

                    $consignee_id = $consignee->id;
                } else if ($consignee_count == 1) {
                    $consignee_id = ExportConsignee::where("consignee_name", $request->consignee_name)
                        ->where('branch_id', $request->branch_id)
                        ->first()->id;
                } else {
                    DB::rollBack();

                    $message = ['error' => ["More than one name of consignee"]];

                    return $message;
                }

                $shipper_count = DB::table('mt_shipper as a')
                    ->where('a.shipper_name', 'LIKE', "$request->shipper_name")
                    ->where('branch_id', $request->branch_id)
                    ->orderBy("a.shipper_name")
                    ->count();

                if ($shipper_count == 0) {
                    $shipper = new ExportShipper();
                    $shipper->shipper_name     = $request->shipper_name;
                    $shipper->branch_id      = $request->branch_id;
                    $shipper->save();

                    $shipper_id = $shipper->id;
                } else if ($shipper_count == 1) {
                    $shipper_id = ExportShipper::where("shipper_name", $request->shipper_name)
                        ->where('branch_id', $request->branch_id)
                        ->first()->id;
                } else {
                    DB::rollBack();
                    $message = ['error' => ["More than one name of shipper"]];
                    return $message;
                }
                if ($request->has('po')) {
                    $po = implode('|', $request->po);
                    $qty = implode('|', $request->qty);
                    $cbm = implode('|', $request->cbm);
                    $duplicates = array_count_values($request->po);
                    $duplicates = array_filter($duplicates, function ($count) {
                        return $count > 1;
                    });
                    if (!empty($duplicates)) {
                        DB::rollBack();
                        $message = ['error' => ["PO Duclicate: " . implode(", ", array_keys($duplicates))]];
                        return $message;
                    }
                }
                $job->branch_id = $request->branch_id;
                $job->vehicle_no = $request->vehicle_no;
                $job->forwarder_id = $forwarder_id;
                $job->shipper_id = $shipper_id;
                $job->consignee_id = $consignee_id;
                $job->peb_no = $request->peb_no;
                $job->destination = $request->destination;
                $job->final_destination = $request->final_destination;
                $job->user_id = $user_name;
                $job->aju_no = $request->aju_no;
                $job->vgm = $request->vgm;
                if ($isNew) {
                    $job->created_at = date('Y-m-d H:i:s');
                }
                $job->created_by = Auth::user()->username;
                $job->po_number = isset($request->po) ? $po : $job->po_number;
                $job->qty_cargo = isset($request->qty) ? $qty : $job->qty_cargo;
                $job->cbm = isset($request->cbm) ? $cbm : $job->cbm;
                $job->pic_name = isset($request->pic_name) ? $request->pic_name : NULL;
                $job->gate_in_by_ao = $request->gateDate . ' ' . $request->gateTime;
                $job->vehicle_no_by_ao = $request->vehicleNumber;
                $job->save();

                DB::table('ex_stock_ledger')
                    ->where('peb_no', $job->peb_no)
                    ->where('po_number', $job->po_number)
                    ->where('job_no', $job->job_no)
                    ->update([
                        'forwarder_id' => $forwarder_id
                    ]);

                DB::commit();
                $message = ['success' => url('/export/inbound/show/' . $job->id)];
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

        $lastJob = ExportInboundHeader::whereMonth('job_date', $month)
            ->lockForUpdate()
            ->orderBy('job_no', 'desc')
            ->first();

        if (!$lastJob) {
            $increment = 1;
        } else {
            $increment = (int) substr($lastJob->job_no, 7, 4) + 1;
        }

        $job_no = 'I' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($increment, 4, '0', STR_PAD_LEFT);

        return $job_no;
    }

    public function submit(Request $request)
    {
        $result = DB::transaction(function () use ($request) {
            try {
                $userName = Auth::user()->username;
                $job = ExportInboundHeader::findOrFail($request->job_id);

                $details = ExportInboundDetail::where('job_id', $job->id)->get();

                if ($details->isEmpty()) {
                    throw new \Exception('Detail data not found.');
                }

                $totalQty = $details->sum('quantity');
                $totalPallet = $details->groupBy('pallet_id')->count();
                $today = now();
                $jobDate = $job->job_date ?? $today;

                $ledgerRows = [];
                $transactionRows = [];

                foreach ($details as $value) {
                    $cbm = ($value->length * $value->width * $value->height / 1000000) * $value->quantity;

                    $ledgerRows[] = [
                        'branch_id' => $job->branch_id,
                        'job_no' => $job->job_no,
                        'job_date' => $jobDate,
                        'po_number' => $job->po_number,
                        'vehicle_no' => $job->vehicle_no,
                        'forwarder_id' => $job->forwarder_id,
                        'consignee_id' => $job->consignee_id,
                        'shipper_id' => $job->shipper_id,
                        'destination' => $job->destination,
                        'peb_no' => $job->peb_no,
                        'aju_no' => $job->aju_no,
                        'pic_name' => $job->pic_name,
                        'qty_cargo' => $totalQty,
                        'cbm' => $cbm,
                        'weight' => $job->weight,
                        'total_pallet' => $totalPallet,
                        'serial_no' => $value->serial_no,
                        'pallet_id' => $value->pallet_id,
                        'quantity' => $value->quantity,
                        'user_id' => $userName,
                        'created_at' => $today,
                        'updated_at' => $today,
                    ];

                    $transactionRows[] = [
                        'job_type' => 'in',
                        'branch_id' => $job->branch_id,
                        'peb_no' => $job->peb_no,
                        'aju_no' => $job->aju_no,
                        'job_no' => $job->job_no,
                        'po_number' => $job->po_number,
                        'vehicle_no' => $job->vehicle_no,
                        'forwarder_id' => $job->forwarder_id,
                        'shipper_id' => $job->shipper_id,
                        'consignee_id' => $job->consignee_id,
                        'created_at' => $today,
                        'destination' => $job->destination,
                        'serial_no' => $value->serial_no,
                        'cbm' => $cbm,
                        'quantity' => $value->quantity,
                        'total_pallet' => $totalPallet,
                        'pallet_id' => $value->pallet_id,
                        'qty_cargo' => $totalQty,
                        'weight' => $job->weight,
                        'user_id' => $userName,
                    ];
                }

                ExportStockLedger::insert($ledgerRows);
                DB::table('ex_stock_transaction')->insert($transactionRows);
                $job->update(['status_flag' => 'Confirmed']);

                $foto = DB::table('ex_inbound_foto_cargo')->select('created_at')->where('job_id', $job->id)->first();
                $vehicle = DB::table("ex_gate_in_cargo")
                    ->select('created_at')
                    ->where("vehicle_number", $job->vehicle_no)
                    ->whereDate('created_at', $job->created_at)
                    ->first();
                // $this->storeToWims($job, $details, $foto, $vehicle);
                DB::commit();
                return ['success' => "Success"];
            } catch (\Exception $e) {
                throw $e;
            }
        });

        return response()->json($result);
    }

    private function getForwarderName($id)
    {
        $forwarder = ExportForwarder::find($id);
        if ($forwarder) {
            return $forwarder->forwarder_name;
        }
        return null;
    }

    private function getConsigneeName($id)
    {
        $consignee = ExportConsignee::find($id);
        if ($consignee) {
            return $consignee->consignee_name;
        }
        return null;
    }

    private function getShipperName($id)
    {
        $shipper = ExportShipper::find($id);
        if ($shipper) {
            return $shipper->shipper_name;
        }
        return null;
    }

    private function storeToWims($header, $details, $foto, $vehicle)
    {
        $conn = DB::connection('mysql_wims');
        $existing = $conn->table('ex_inbound_header')
            ->where('id', $header->id)
            ->exists();
        if ($existing) {
            $conn->table('ex_stock_ledger')
                ->where('job_id', $header->id)
                ->delete();
            $conn->table('ex_inbound_detail')
                ->where('job_id', $header->id)
                ->delete();
            $conn->table('ex_inbound_header')
                ->where('id', $header->id)
                ->delete();
        }

        $userName = Auth::user()->username;
        $header = $header;

        $qtyCargo = 0;
        if (!empty($header->qty_cargo)) {
            $qtyParts = explode('|', $header->qty_cargo);
            $qtyCargo = collect($qtyParts)->sum(function ($item) {
                return (float) $item;
            });
        }

        $cbmTotal = 0;
        if (!empty($header->cbm)) {
            $cbmParts = explode('|', $header->cbm);
            $cbmTotal = collect($cbmParts)->sum(function ($item) {
                return (float) $item;
            });
        }

        DB::connection('mysql_wims')
            ->table('ex_inbound_header')
            ->insert([
                'id' => $header->id,
                'job_no' => $header->job_no,
                'job_date' => $header->job_date,
                'po_number' => $header->po_number,
                'vehicle_no' => $header->vehicle_no,
                'forwarder_id' => $header->forwarder_id,
                'forwarder_name' => $this->getForwarderName($header->forwarder_id),
                'consignee_id' => $header->consignee_id,
                'consignee_name' => $this->getConsigneeName($header->consignee_id),
                'shipper_id' => $header->shipper_id,
                'shipper_name' => $this->getShipperName($header->shipper_id),
                'peb_no' => $header->peb_no,
                'aju_no' => $header->aju_no,
                'vgm' => $header->vgm,
                'qty_cargo' => $qtyCargo,
                'cbm' => $cbmTotal,
                'total_pallet' => $details->groupBy('pallet_id')->count(),
                'status_flag' => 'Confirmed',
                'weight' => $header->weight,
                'pic_name' => $header->pic_name,
                'destination' => $header->destination,
                'updated_peb' => 'No',
                'checker_confirmed_flag' => $header->checker_confirmed_flag,
                'exinbound_foto_cargo_created_at' => $foto ? $foto->created_at : null,
                'exgate_in_cargo_created_at' => $vehicle ? $vehicle->created_at : null,
                'sync_time' => now(),
                'created_at' => now(),
                'created_by' => $userName,
            ]);
        $ledgerRows = [];
        $detailRows = [];
        $totalQty = $details->sum('quantity');
        foreach ($details as $value) {
            $detailRows[] = [
                'job_id' => $header->id,
                'serial_no' => $value->serial_no,
                'pallet_id' => $value->pallet_id,
                'quantity' => $value->quantity,
                'length' => $value->length,
                'width' => $value->width,
                'height' => $value->height,
                'unit' => $value->unit,
                'created_by' => $userName,
                'created_at' => now(),
            ];

            $cbm = ($value->length * $value->width * $value->height / 1000000) * $value->quantity;
            $ledgerRows[] = [
                'job_id' => $header->id,
                'job_no' => $header->job_no,
                'job_date' => $header->job_date,
                'po_number' => $header->po_number,
                'vehicle_no' => $header->vehicle_no,
                'forwarder_id' => $header->forwarder_id,
                'forwarder_name' => $this->getForwarderName($header->forwarder_id),
                'consignee_id' => $header->consignee_id,
                'consignee_name' => $this->getConsigneeName($header->consignee_id),
                'shipper_id' => $header->shipper_id,
                'shipper_name' => $this->getShipperName($header->shipper_id),
                'destination' => $header->destination,
                'peb_no' => $header->peb_no,
                'aju_no' => $header->aju_no,
                'qty_cargo' => $totalQty,
                'cbm' => $cbm,
                'weight' => $header->weight,
                'total_pallet' => $details->groupBy('pallet_id')->count(),
                'serial_no' => $value->serial_no,
                'status_flag' => 'Inbound',
                'quantity' => $value->quantity,
                'pallet_id' => $value->pallet_id,
                'created_by' => $userName,
                'created_at' => now(),
            ];
        }
        DB::connection('mysql_wims')->table('ex_inbound_detail')->insert($detailRows);
        DB::connection('mysql_wims')->table('ex_stock_ledger')->insert($ledgerRows);
    }

    public function updateWeight(Request $request)
    {
        DB::transaction(function () use ($request) {
            try {
                for ($i = 0; $i < count($request->id_detail_inbound); $i++) {
                    ExportInboundDetail::where("id", $request->id_detail_inbound[$i])
                        ->update([
                            'weight' => $request->weight[$i]
                        ]);
                }
                DB::commit();
                $message = ['success' => "Success"];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });

        return back();
    }

    public function getPalletize($job_id)
    {
        try {
            $data = ExportInboundDetail::where("job_id", $job_id)->orderBy('pallet_id', 'ASC')->get();
            $data = $data->map(function ($item) {
                $segments = explode('-', $item->serial_no);
                $item->po_number = $segments[0] ?? null; // Ambil segmen ke-0
                return $item;
            });
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateStaple($job_id, $username)
    {
        DB::transaction(function () use ($job_id, $username) {
            try {
                DB::table('ex_inbound_header')->where('id', $job_id)
                    ->update([
                        'stapel_name' => $username
                    ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }
        });
        return back();
    }
    public function showImages($job_id)
    {
        $exception = DB::transaction(function () use ($job_id) {
            try {
                $data = DB::table('ex_inbound_foto_cargo')
                    ->select('file', 'id')
                    ->where('job_id', $job_id)
                    ->get();
                return ['data' => $data];
            } catch (\Exception $e) {
                throw $e;
            }
        });

        return response()->json($exception);
    }

    public function deleteImage($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $data = DB::table('ex_inbound_foto_cargo')
                    ->select('file')
                    ->where('id', $id)
                    ->delete();
                return ['data' => $data];
            } catch (\Exception $e) {
                throw $e;
            }
        });

        return response()->json($exception);
    }

    public function backtoChecker($job_id)
    {
        $exception = DB::transaction(function () use ($job_id) {
            try {
                DB::table("ex_inbound_header")
                    ->where("id", $job_id)
                    ->update([
                        'checker_flag' => 'Open',
                        'checker_confirmed_flag' => null,
                    ]);
                DB::commit();
                $message = [
                    'message' => 'success',
                ];
                return $message;
            } catch (\Exception $e) {
                throw $e;
            }
        });

        return response()->json($exception);
    }
}
