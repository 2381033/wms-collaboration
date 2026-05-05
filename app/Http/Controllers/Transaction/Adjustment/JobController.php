<?php

namespace App\Http\Controllers\Transaction\Adjustment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Adjustment\Job as AdjustmentJob;
use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Adjustment\Detail as AdjustmentDetail;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $details = DB::table("iv_adjustment_job as a")
                ->select("a.*", "b.type_name")
                ->join("iv_adjustment_type as b", "a.type_id", "b.id")
                ->where("a.company_id", $company_id)
                ->where("a.branch_id", $request->branch_id)
                ->whereBetween('a.adjust_date', [$date_from, $date_to])
                ->where("a.confirmed_flag", $request->confirmed_flag)
                ->get();

            return datatables()->of($details)
                ->editColumn("adjust_date", function ($data) {
                    return date("d/m/Y", strtotime($data->adjust_date));
                })
                ->editColumn("confirmed_flag", function ($data) {
                    if ($data->confirmed_flag == "Yes") {
                        $status = "<div class='btn btn-sm btn-success'>Completed</div>";
                    } else {
                        $status = "<div class='btn btn-sm btn-danger'>Open</div>";
                    }
                    return $status;
                })
                ->addColumn("adjust_no", function ($data) {
                    $button = "";
                    $button .= "<a href='" . URL("/inventory/stock-adjustment/create/$data->id") . "' class='btn btn-default btn-sm'><i class='far fa-file'></i> " . $data->adjust_no . "</a>";
                    return $button;
                })
                ->rawColumns(["confirmed_flag", "adjust_no"])
                ->addIndexColumn()
                ->make(true);
        }

        return view("transaction.adjustment.index");
    }

    public function create($id = "")
    {
        $job_view = DB::table("iv_adjustment_job as a")
            ->select("a.*", "b.type_name")
            ->join("iv_adjustment_type as b", "a.type_id", "b.id")
            ->where("a.id", $id)
            ->first();

        $adjustment_type = DB::table("iv_adjustment_type")->where("active", "Yes")->get();

        $data = [
            "job_view" => $job_view,
            "type_list" => $adjustment_type
        ];

        return view("transaction.adjustment.create", $data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            "branch_id.required" => "Branch name field is required.",
            "type_id.required" => "Adjustment type field is required.",
            "description.required" => "Description field is required.",
        );

        $rules = array(
            "branch_id" => "required",
            "type_id" => "required",
            "description" => "required",
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()->all()]);
        }

        $id = $request->adjust_id;
        $company_id = Auth::user()->company_id;
        $entry_date = \Carbon\Carbon::now();
        $adjust_date = \Carbon\Carbon::today();

        $year = $adjust_date->year;
        $month = $adjust_date->month;

        $job = AdjustmentJob::where("company_id", $company_id)
            ->whereYear("adjust_date", $year)
            ->whereMonth("adjust_date", $month)->max("adjust_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }

        $adjust_no = "4" . $year . Str::of($month)->padLeft(2, "0") . Str::of($increment)->padLeft(4, "0");

        $data   =   AdjustmentJob::updateOrCreate(
            ["id" => $id],
            [
                "company_id" => $company_id,
                "adjust_no" => $adjust_no,
                "adjust_date" => $adjust_date,
                "branch_id" => $request->branch_id,
                "type_id" => $request->type_id,
                "description" => $request->description,
                "entry_date" => $entry_date
            ]
        );

        return response()->json(["success" => url("/inventory/stock-adjustment/create/" . $data->id)]);
    }

    public function downloadTemplate($principal)
    {
        $branchList = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('branch_id')->toArray();
        $stock = DB::table("iv_stock_ledger as a")
            ->select(
                "a.id",
                "a.serial_no",
                "a.product_code",
                // "a.product_name",
                "a.qtya",
                "a.location_code",
            )
            ->where("a.principal_id", $principal)
            ->whereIn("a.branch_id", $branchList)
            // ->groupBy("a.product_id")
            ->where('qtya', '>', 0)
            ->orderBy("a.product_code", "asc")
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'id');
        $sheet->setCellValue('B1', 'serial_no');
        $sheet->setCellValue('C1', 'product_code');
        // $sheet->setCellValue('D1', 'product_name');
        $sheet->setCellValue('D1', 'qtya');
        $sheet->setCellValue('E1', 'qty Adjust');
        $sheet->setCellValue('F1', 'location_code');
        $sheet->setCellValue('G1', 'adjust_type');
        $row = 2;
        foreach ($stock as $item) {
            $sheet->setCellValue("A{$row}", $item->id);
            $sheet->setCellValue("B{$row}", $item->serial_no);
            $sheet->setCellValue("C{$row}", $item->product_code);
            // $sheet->setCellValue("D{$row}", $item->product_name);
            $sheet->setCellValue("D{$row}", $item->qtya);
            $sheet->setCellValue("E{$row}", '0');
            $sheet->setCellValue("F{$row}", $item->location_code);
            $sheet->setCellValue("G{$row}", 'Plus/Minus');
            $row++;
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = "adjusment-template.xlsx";
        $filePath = storage_path("app/public/{$fileName}");

        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('excel')->getPathname();
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $updates = [];
            $rowNumber = 1;

            foreach (array_slice($rows, 1) as $r) {
                $rowNumber++;

                $id             = trim($r[0] ?? null);
                $serial_no      = trim($r[1] ?? null);
                $product_code   = trim($r[2] ?? null);
                $qtya           = trim($r[3] ?? null);
                $qty_adjust     = trim($r[4] ?? null);
                $location_code  = trim($r[5] ?? null);
                $adjust_type    = strtolower(str_replace(' ', '', trim($r[6] ?? null)));


                if (empty($id)) {
                    throw new \Exception("ID tidak boleh kosong. Error di baris {$rowNumber}.");
                }

                if ($qty_adjust == "" || !is_numeric($qty_adjust) || $qty_adjust <= 0) {
                    throw new \Exception("Qty tidak boleh kosong. Error di baris {$rowNumber}.");
                }

                if (!in_array($adjust_type, ['plus', 'minus'])) {
                    throw new \Exception("Adjust Type hanya boleh 'plus' atau 'minus'. Nilai '{$r[5]}' tidak valid di baris {$rowNumber}.");
                }

                $updates[] = [
                    'id' => $id,
                    'serial_no' => $serial_no,
                    'product_code' => $product_code,
                    'qtya' => $qtya,
                    'qty_adjust' => $qty_adjust,
                    'location_code' => $location_code,
                    'adjust_type' => $adjust_type,
                ];
            }

            DB::transaction(function () use ($updates, $request) {
                $company_id = Auth::user()->company_id;
                $adjust_id = $request->id_header;
                foreach ($updates as $value) {
                    $stock = StockLedger::find($value['id']);
                    if (!$stock) {
                        throw new \Exception("Unique ID stock {$value['id']} tidak ditemukan.");
                    }
                    $actual_qty = ($value['qty_adjust'] * $stock->uppp) + ($stock->actual_mqty * $stock->muppp) + $stock->actual_bqty;
                    $actual_pqty = ($value['qty_adjust']  - ($value['qty_adjust'] % $stock->uppp)) / $stock->uppp;
                    $actual_mqty = (($value['qty_adjust'] % $stock->uppp) - (($value['qty_adjust'] % $stock->uppp) % $stock->muppp)) / $stock->muppp;
                    $actual_bqty = $value['qty_adjust'] % $stock->uppp % $stock->muppp;
                    $qty = ($actual_pqty * $stock->uppp) + ($actual_mqty * $stock->muppp) + $actual_bqty;
                    AdjustmentDetail::Insert(
                        [
                            "company_id" => $company_id,
                            "principal_id" => $stock->principal_id,
                            "adjust_id" => $adjust_id,
                            "status_flag" => 'Exist',
                            "adjust_type" => ucwords($value['adjust_type']),
                            "job_no" => $stock->job_no,
                            "serial_id" => $value['id'],
                            "serial_no" => $stock->serial_no,
                            "product_id" => $stock->product_id,
                            "product_code" => $stock->product_code,
                            "po_number" => $stock->po_number,
                            "lot_no" => $stock->lot_no,
                            "document_ref" => $stock->document_ref,
                            "mfg_date" => $stock->mfg_date,
                            "exp_date" => $stock->exp_date,
                            "manufactur_id" => $stock->manufactur_id,
                            "status_id" => $stock->status_id,
                            "site_id" => $stock->site_id,
                            "area_id" => $stock->area_id,
                            "location_id" => $stock->location_id,
                            "location_code" => $stock->location_code,
                            "puom" => $stock->puom,
                            "muom" => $stock->muom,
                            "buom" => $stock->buom,
                            "uppp" => $stock->uppp,
                            "muppp" => $stock->muppp,
                            "pqty" => $stock->pqty,
                            "mqty" => $stock->mqty,
                            "bqty" => $stock->bqty,
                            "qty" => $qty,
                            "actual_pqty" => $actual_pqty,
                            "actual_mqty" => $actual_mqty,
                            "actual_bqty" => $actual_bqty,
                            "actual_qty" => $actual_qty,
                            "base_unit" => $stock->base_unit,
                            "pallet_qty" => $stock->pallet_qty,
                            "created_at" => date("Y-m-d H:i:s"),
                            // "updated_at" => date("Y-m-d H:i:s"),
                        ]
                    );
                }
            });
            return back()->with('success', 'Excel berhasil diproses tanpa error!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function storeUpload(Request $request)
    {
        $actual_qty = ($request->actual_pqty * $request->uppp) + ($request->actual_mqty * $request->muppp) + $request->actual_bqty;

        if ($actual_qty == 0) {
            return response()->json(["error" => ["Quantity cannot be empty!"]]);
        }

        try {
            $company_id = Auth::user()->company_id;
            $id = $request->detail_id;
            $adjust_id = $request->adjust_id;
            $status_flag = $request->status_flag;
            $entry_date = \Carbon\Carbon::now();

            if ($status_flag == "Exist") {
                $serial_id = $request->serial_id;
                $stock = StockLedger::find($serial_id);

                $pqty = ($stock->qtya  - ($stock->qtya % $stock->uppp)) / $stock->uppp;
                $mqty = (($stock->qtya % $stock->uppp) - (($stock->qtya % $stock->uppp) % $stock->muppp)) / $stock->muppp;
                $bqty = $stock->qtya % $stock->uppp % $stock->muppp;
                $qty = ($pqty * $stock->uppp) + ($mqty * $stock->muppp) + $bqty;

                AdjustmentDetail::updateOrCreate(
                    ["id" => $id],
                    [
                        "company_id" => $company_id,
                        "principal_id" => $stock->principal_id,
                        "adjust_id" => $adjust_id,
                        "status_flag" => $status_flag,
                        "adjust_type" => $request->adjust_type,
                        "job_no" => $stock->job_no,
                        "serial_id" => $serial_id,
                        "serial_no" => $stock->serial_no,
                        "product_id" => $stock->product_id,
                        "product_code" => $stock->product_code,
                        "po_number" => $stock->po_number,
                        "lot_no" => $stock->lot_no,
                        "document_ref" => $stock->document_ref,
                        "mfg_date" => $stock->mfg_date,
                        "exp_date" => $stock->exp_date,
                        "manufactur_id" => $stock->manufactur_id,
                        "status_id" => $stock->status_id,
                        "site_id" => $stock->site_id,
                        "area_id" => $stock->area_id,
                        "location_id" => $stock->location_id,
                        "location_code" => $stock->location_code,
                        "puom" => $stock->puom,
                        "muom" => $stock->muom,
                        "buom" => $stock->buom,
                        "uppp" => $stock->uppp,
                        "muppp" => $stock->muppp,
                        "pqty" => $pqty,
                        "mqty" => $mqty,
                        "bqty" => $bqty,
                        "qty" => $qty,
                        "actual_pqty" => $request->actual_pqty,
                        "actual_mqty" => $request->actual_mqty,
                        "actual_bqty" => $request->actual_bqty,
                        "actual_qty" => $actual_qty,
                        "base_unit" => $stock->base_unit,
                        "pallet_qty" => $stock->pallet_qty,
                        "entry_date" => $entry_date
                    ]
                );
            }

            $message = ["success" => "Data Successfully Saved"];

            return $message;
        } catch (\Exception $e) {
            $message = ["error" => [$e->getMessage()]];
        }

        return response()->json($message);
    }
}
