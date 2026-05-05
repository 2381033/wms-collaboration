<?php

namespace App\Http\Controllers\Transaction\Export\Inbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Transaction\Export\InboundHeader as ExportInboundHeader;
use App\Models\Transaction\Export\InboundDetail as ExportInboundDetail;
use App\Models\Transaction\Export\StockLedger as ExportStockLedger;
use App\Exports\TallySheetDetailExport as tallySheetExcel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class DetailController extends Controller
{
    public function index(Request $request)
    {
        $details = [];
        if ($request->ajax()) {
            if (!empty($request->job_id) && !empty($request->job_id)) {
                $details = DB::table("ex_inbound_detail as a")
                    ->where("a.job_id", $request->job_id)
                    ->get();
            }

            return datatables()->of($details)
                ->editColumn("quantity", function ($data) {
                    $input = "<input type='hidden' value='" . $data->id . "' name='id[]' class='form-control'/><input type='text' value='$data->quantity' name='quantity[]' class='form-control'/>";

                    return $input;
                })
                ->rawColumns(["quantity"])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job_id = $request->job_id;
                $detail_id = $request->id;
                $quantity = $request->quantity;

                $job = ExportInboundHeader::find($job_id);

                $total = 0;
                for ($i = 0; $i < count($detail_id); $i++) {
                    $detail = ExportInboundDetail::find($detail_id[$i]);

                    $detail->quantity = $quantity[$i];
                    $detail->save();

                    $total = $total + $quantity[$i];
                }

                DB::commit();
                $message = ["success" => "Sukses"];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => $e->getMessage()];

                return $message;
            }
        });
        return response()->json($exception);
    }

    public function palletTag($id)
    {
        $view = DB::table("ex_inbound_header as a")
            ->select('a.*', "b.forwarder_name", "c.consignee_name", "d.shipper_name", "d.id as shipper_id")
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->join("mt_consignee as c", "a.consignee_id", "c.id")
            ->join("mt_shipper as d", "a.shipper_id", "d.id")
            ->where("a.id", $id)
            ->first();
        $checker_name = '-';
        if (!is_null($view)) {
            $checker_name = $this->detailUser($view->pic_name)->name ?? '-';
        }

        $list_data = DB::table("ex_inbound_detail as a")
            ->where("a.job_id", $id)
            ->where("a.quantity", ">", 0)
            ->get();
        $total_pallet = $list_data->groupBy('pallet_id')->count();
        $total_receipt = array_sum($list_data->pluck('quantity')->toArray());

        $data = [
            "view" => $view,
            "list_data" => $list_data,
            "checker_name" => $checker_name,
            "total_pallet" => $total_pallet,
            "total_receipt" => $total_receipt,
        ];

        return view("transaction.export.inbound.barcode", $data);
    }

    private function detailUser($username)
    {
        $data = DB::table('users')->where('username', $username)->first();
        return $data;
    }

    public function tally_sheet($type, $id)
    {
        $header = DB::table("ex_inbound_header as a")
            ->select('a.*', "b.forwarder_name", "c.consignee_name", "d.shipper_name")
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->join("mt_consignee as c", "a.consignee_id", "c.id")
            ->join("mt_shipper as d", "a.shipper_id", "d.id")
            ->where("a.id", $id)
            ->first();

        $foto_cargo = DB::table('ex_inbound_foto_cargo')
            ->select('file', 'created_at')
            ->orderBy('id', 'ASC')
            ->where('job_id', $id)
            ->get();
        $truck_photos = $foto_cargo->filter(function ($item) {
            return stripos($item->file, 'truck') !== false;
        });

        $unloading_start = $truck_photos->first();
        $unloading_finish = $foto_cargo->last();

        $vehicle = DB::table('ex_gate_in_cargo')
            ->select('driver_name', 'created_at')
            ->whereDate('created_at', $header->job_date)
            ->where('vehicle_number', $header->vehicle_no)
            ->first();

        $detail = DB::table("ex_inbound_detail as a")
            ->where("a.job_id", $id)
            ->where("a.quantity", ">", 0)
            ->get();
        $qtyTotal = $detail->sum('quantity');

        $cbm_total = [];
        $vgm_total = [];
        foreach ($detail as $key => $value) {
            $cbm_total[] = ($value->length * $value->width * $value->height * $value->quantity);
            $vgm_total[] = $value->weight;
            $value->serial_no_formatted = str_replace('/', '-', $value->serial_no);
        }
        $cbm_total = array_sum($cbm_total);
        $cbm_total = number_format(($cbm_total) / 1000000, 3, '.', '');
        $vgm_total = array_sum($vgm_total);
        $data = [
            "header" => $header,
            "detail" => $detail,
            'cbm_total' => $cbm_total,
            'vgm_total' => $vgm_total,
            'foto_cargo' => $foto_cargo,
            'driver' => $vehicle->driver_name ?? '-',
            'gatein' => $vehicle->created_at ?? '-',
            'qtyTotal' => $qtyTotal,
            'unloading_start' => $unloading_start,
            'unloading_finish' => $unloading_finish,
        ];
        if ($type == 'download') {
            // $summary = [
            //     'peb_no' => $header->peb_no,
            //     'qtyTotal' => $qtyTotal,
            //     'vgm_total' => $vgm_total,
            //     'cbm_total' => number_format($cbm_total, 3, '.', ''),
            // ];

            return Excel::download(new tallySheetExcel($type, $data), 'tally_sheet_' . $header->forwarder_name . '.xlsx');
        }
        return view("transaction.export.inbound.tally_sheet_" . $type, $data);
    }

    public function updateNoPeb()
    {
        return view("transaction.export.update_peb");
    }

    public function getListUpdateNoPeb()
    {
        $data = DB::table('ex_inbound_header as a')
            ->select('a.*', "b.forwarder_name", "c.consignee_name", "d.shipper_name")
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->join("mt_consignee as c", "a.consignee_id", "c.id")
            ->join("mt_shipper as d", "a.shipper_id", "d.id")
            ->where('a.branch_id', $this->myBranch())
            ->whereNotNull('a.aju_no')
            ->whereIn('a.peb_no', [0, '-'])
            // ->where('a.peb_no', '-')
            ->where('a.updated_peb', 'No')
            ->orderBy('a.id', 'DESC')
            ->get();

        return datatables()->of($data)->make(true);
    }

    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');
        return $data;
    }

    public function updatePeb(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job = ExportInboundHeader::find($request->id);
                $job->peb_no = $request->peb_no;
                // $job->aju_no = null;
                $job->updated_peb = 'Yes';
                $job->save();

                DB::table('ex_stock_ledger')
                    ->where('job_no', $job->job_no)
                    ->update([
                        'peb_no' => $request->peb_no,
                        // 'aju_no' => null
                    ]);
                $this->updateToWims($request, $job);
                DB::commit();
                $message = ["message" => "success"];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => $e->getMessage()];

                return $message;
            }
        });
        return response()->json($exception);
    }

    private function updateToWims($request, $job)
    {
        $userName = Auth::user()->username;
        $job = $job;
        DB::connection('mysql_wims')
            ->table('ex_inbound_header')
            ->where('id', $job->id)
            ->update([
                'peb_no' => $request->peb_no,
                // 'aju_no' => null, pertahankan dalam db wims
                'updated_peb' => 'Yes', // ubah menjadi yes karena sudah diupdate peb nya
                'sync_time' => now(),
                'updated_at' => now(),
                'updated_by' => $userName,
            ]);
        DB::connection('mysql_wims')->table('ex_stock_ledger')
            ->where('job_id', $job->id)
            ->update([
                'peb_no' => $request->peb_no,
                // 'aju_no' => null,
                'updated_at' => now(),
                'updated_by' => $userName,
            ]);
    }

    public function tallySheetExcel($id)
    {
        return Excel::download(new tallySheetExcel($id), 'users.xlsx');
    }

    public function deleteStock($job_id)
    {
        $exception = DB::transaction(function () use ($job_id) {
            try {
                $header = ExportInboundHeader::find($job_id);
                DB::table('ex_stock_ledger')
                    ->where('job_no', $header->job_no)
                    ->where('branch_id', $header->branch_id)
                    ->where('po_number', $header->po_number)
                    ->delete();
                DB::table('ex_inbound_detail')->where('job_id', $job_id)->delete();
                DB::commit();
                $message = ["message" => "success"];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => $e->getMessage()];

                return $message;
            }
        });
        return response()->json($exception);
    }

    public function updatePalletize(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job_id = $request->job_id;
                $flagColud = false;
                $detail = ExportInboundDetail::Where('job_id', $job_id)->count();
                $header = ExportInboundHeader::find($job_id);
                $header = ExportInboundHeader::find($job_id);

                // Ubah po_number dari master menjadi array
                $masterPoNumbers = explode('|', $header->po_number);
                $invalidPo = array_diff($request->po_number, $masterPoNumbers);

                if (!empty($invalidPo)) {
                    throw new \Exception('PO number tidak valid: ' . implode(', ', $invalidPo));
                }

                if ($detail == 0) {
                    $flagColud = true;
                }

                if (!$flagColud) {
                    //DELETE DETAIL 
                    DB::table('ex_stock_ledger')
                        ->where('job_no', $header->job_no)
                        ->where('branch_id', $header->branch_id)
                        ->where('po_number', $header->po_number)
                        ->delete();
                    //DELETE LEDGER 
                    DB::table('ex_inbound_detail')->where('job_id', $job_id)->delete();
                }
                $floor = DB::table('ex_location')
                    ->where('branch_id', $this->myBranch())
                    ->where('location_code', 'LIKE', '%FLOOR%')
                    ->first();
                $total_pallet = count(array_unique($request->pallet_id));
                for ($i = 0; $i < count($request->po_number); $i++) {
                    $serial_no = $request->po_number[$i] . "-" . $request->peb_no . "-" . Str::of($request->pallet_id[$i])->padLeft(2, '0') . "-" . $job_id;
                    DB::table('ex_inbound_detail')
                        ->insert([
                            'serial_no'   => $serial_no,
                            'job_id'      => $job_id,
                            'pallet_id'   => $request->pallet_id[$i],
                            'quantity'    => $request->quantity[$i],
                            'length'      => $request->length[$i],
                            'width'       => $request->width[$i],
                            'height'      => $request->height[$i],
                            'unit'        => $request->unit[$i],
                            'user_id'     => Auth::user()->username,
                            'location_id' => $flagColud ? $floor->id : $request->location_id[$i],
                            'location_code' => $flagColud ? $floor->location_code : $request->location_code[$i],
                            'scan_pallet_tag' => 'Yes',
                            'scan_location' => 'Yes',
                            'created_at'  => date('Y-m-d H:i:s'),
                            'updated_at'  => date('Y-m-d H:i:s'),
                        ]);

                    DB::table('ex_stock_ledger')
                        ->insert([
                            'branch_id'   => $header->branch_id,
                            'job_date'    => date('Y-m-d H:i:s'),
                            'po_number'   => $header->po_number,
                            'job_no'      => $header->job_no,
                            'vehicle_no'  => $header->vehicle_no,
                            'forwarder_id' => $header->forwarder_id,
                            'shipper_id'   => $header->shipper_id,
                            'consignee_id' => $header->consignee_id,
                            'destination'  => $header->destination,
                            'peb_no'       => $header->peb_no,
                            'pic_name'     => $header->pic_name,
                            'qty_cargo'    => $request->quantity[$i],
                            'cbm'          => $request->length[$i] * $request->width[$i] * $request->height[$i] / 1000000 * $request->quantity[$i],
                            'total_pallet' => $total_pallet,
                            'serial_no' => $serial_no,
                            'location_id' => $flagColud ? $floor->id : $request->location_id[$i],
                            'location_code' => $flagColud ? $floor->location_code : $request->location_code[$i],
                            'pallet_id'   => $request->pallet_id[$i],
                            'quantity'   => $request->quantity[$i],
                            'user_id'   => Auth::user()->username,
                            'created_at'  => date('Y-m-d H:i:s'),
                            'updated_at'  => date('Y-m-d H:i:s'),
                        ]);
                }
                $message = ["success" => "success"];

                DB::commit();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => $e->getMessage()];

                return $message;
            }
        });
        return response()->json($exception);
    }
}
