<?php

namespace App\Http\Controllers\Api\Export\Inbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Transaction\Export\InboundHeader as ExportInboundHeader;
use App\Models\Transaction\Export\InboundDetail as ExportInboundDetail;
use Illuminate\Support\Str;

class JobController extends Controller
{

    private function myBranch($username)
    {
        $idUser = DB::table('users')
            ->select('id')
            ->where('username', $username)
            ->value('id');
        $data = DB::table('sm_user_branch')
            ->where('user_id', $idUser)
            ->value('branch_id');
        return $data;
    }

    public function getJobMe($username)
    {
        $data = DB::table("ex_inbound_header")
            // ->where("user_id", $username)
            ->where("branch_id", $this->myBranch($username))
            ->where("status_flag", "Open")
            ->get();
        $data = $data->map(function ($value) {
            $value->forwarder_name = $this->getForwarderByid($value->forwarder_id);
            $value->shipper_name = $this->getShipperByid($value->shipper_id);
            $value->consignee_name = $this->getConsigneeByid($value->consignee_id);
            $value->status_job = $this->getDetail($value->id)->count();
            return $value;
        });
        return response()->json(['data' => $data]);
    }

    public function listMappingChecker($username)
    {
        $data = DB::table("ex_inbound_header")
            ->where("branch_id", $this->myBranch($username))
            ->where("status_flag", "Open")
            ->get();
        $data = $data->map(function ($value) {
            $value->forwarder_name = $this->getForwarderByid($value->forwarder_id);
            $value->shipper_name = $this->getShipperByid($value->shipper_id);
            $value->consignee_name = $this->getConsigneeByid($value->consignee_id);
            $value->status_job = $this->getDetail($value->id)->count();
            return $value;
        });
        return response()->json(['data' => $data]);
    }

    private function detailUser($username)
    {
        $data = DB::table('users')
            ->where('username', $username)
            ->first();
        return $data;
    }

    public function getJobChecker($username)
    {
        $data = DB::table("ex_inbound_header")
            ->where("branch_id", $this->myBranch($username))
            ->where('pic_name', $username)
            ->where("status_flag", "Open")
            ->get();
        $data = $data->map(function ($value) {
            $value->forwarder_name = $this->getForwarderByid($value->forwarder_id);
            $value->shipper_name = $this->getShipperByid($value->shipper_id);
            $value->consignee_name = $this->getConsigneeByid($value->consignee_id);
            $value->status_job = $this->getDetail($value->id)->count();
            return $value;
        });
        return response()->json(['data' => $data]);
    }

    public function detailJob($id)
    {
        $header = DB::table("ex_inbound_header")
            ->where("id", $id)
            ->where('status_flag', 'Open')
            ->first();
        $forwarder_name = $this->getForwarderByid($header->forwarder_id);
        $shipper_name = $this->getShipperByid($header->shipper_id);
        $consignee_name = $this->getConsigneeByid($header->consignee_id);
        $detail = DB::table("ex_inbound_detail")
            ->where("job_id", $id)
            ->get();
        $foto = DB::table("ex_inbound_foto_cargo")
            ->where("job_id", $id)
            ->get();
        $qty_actual = $detail->sum('quantity');
        $data = DB::table('ex_inbound_foto_cargo')
            ->where('job_id', $id)
            ->get();
        $image = [];
        $signatureDriver = "";
        $signatureChecker = "";
        if (!is_null($header->ttd_driver)) {
            $signatureDriver = base64_encode(file_get_contents(public_path('foto/warehouse-export/signature/' . $header->ttd_driver)));
        }
        if (!is_null($header->ttd_checker)) {
            $signatureChecker = base64_encode(file_get_contents(public_path('foto/warehouse-export/signature/' . $header->ttd_checker)));
        }
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $image[] = [
                    'id' => $key,
                    'foto' => base64_encode(file_get_contents(public_path('foto/warehouse-export/inbound-cargo/' . $value->file)))
                ];
            }
        } else {
            $image = [];
        }
        $typeSignature = "";
        if (is_null($header->ttd_driver)) {
            $typeSignature = 'driver';
        }
        if (!is_null($header->ttd_driver) and is_null($header->ttd_checker)) {
            $typeSignature = 'checker';
        }
        if (!is_null($header->ttd_driver) and !is_null($header->ttd_checker)) {
            $typeSignature = 'done';
        }
        return response()->json([
            'data' => [
                'header' => $header,
                'detail' => $detail,
                'forwarder' => $forwarder_name,
                'shipper' => $shipper_name,
                'consignee' => $consignee_name,
                'qty_actual' => $qty_actual,
                'image' => $image,
                'foto' => $foto,
                'typeSignature' => $typeSignature,
                'signatureDriver' => $signatureDriver,
                'signatureChecker' => $signatureChecker,
            ]
        ]);
    }

    public function closeJobByChecker(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $validate = DB::table('ex_inbound_foto_cargo')
                    ->where('job_id', $request->job_id)
                    ->count();
                if ($validate <= 6) {
                    $message = [
                        'message' => 'picture',
                    ];
                } else {
                    DB::table("ex_inbound_header")
                        ->where("id", $request->job_id)
                        ->update([
                            'remarks' => $request->remarks,
                            'checker_flag' => 'Confirmed',
                            'checker_confirmed_flag' => date('Y-m-d H:i:s')
                        ]);
                    DB::commit();
                    $message = [
                        'message' => 'Data Successfully Saved',
                    ];
                }
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => true, 'message' => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function postPIC(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                DB::table("ex_inbound_header")
                    ->where("id", $request->job_id)
                    ->update([
                        'pic_name' => $request->pic_name,
                    ]);
                DB::commit();
                $message = [
                    'message' => 'Data Successfully Saved',
                ];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => true, 'message' => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function getDetail($job_id)
    {
        $data = DB::table("ex_inbound_detail")
            ->where("job_id", $job_id)
            ->get();
        return $data;
    }

    private function getJob($branch_id)
    {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = ExportInboundHeader::where('branch_id', $branch_id)
            ->whereYear('job_date', $year)
            ->whereMonth('job_date', $month)
            ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }

        $job_no = 'I' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $job_no;
    }

    public function store(Request $request)
    {
        $rules = array(
            'branch_id' => 'required',
            'vehicle_no' => 'required',
            'po_number' => 'required',
            'forwarder_name' => 'required',
            'shipper_name' => 'required',
            'consignee_name' => 'required',
            'destination' => 'required',
            'final_destination' => 'required',
            'peb_no' => 'required',
            'aju_no' => 'required',
            'qty_cargo' => 'required|integer',
            'cbm' => 'required',
            'weight' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' =>  'validate', 'req' => $request->all()]);
        }
        $exception = DB::transaction(function () use ($request) {
            try {
                $forwarder_id = $this->getForwarder($request->forwarder_name);
                $shipper_id = $this->getShipper($request->shipper_name);
                $consignee_id = $this->getConsignee($request->consignee_name);
                $job_no = $this->getJob($request->branch_id);

                // penambahan validasi sesuai dengan tiket Manage Service 164707
                $exists = DB::table('ex_inbound_header')
                    ->where('po_number', Str::Upper($request->po_number))
                    ->where('vehicle_no', Str::Upper($request->vehicle_no))
                    ->where('peb_no', Str::Upper($request->peb_no))
                    ->exists();

                if ($exists) {
                    return ['error' => true, 'message' => 'Data Transaksi ini sudah terinput sebelumnya.'];
                }

                DB::table('ex_inbound_header')->insert([
                    'job_no' => $job_no,
                    'branch_id' => $request->branch_id,
                    'job_date' => date('Y-m-d H:i:s'),
                    'po_number' => Str::Upper($request->po_number),
                    'vehicle_no' => Str::Upper($request->vehicle_no),
                    'forwarder_id' => $forwarder_id->id,
                    'shipper_id' => $shipper_id->id,
                    'consignee_id' => $consignee_id->id,
                    'destination' => Str::Upper($request->destination),
                    'final_destination' => Str::Upper($request->final_destination),
                    'peb_no' => Str::Upper($request->peb_no),
                    'aju_no' => Str::Upper($request->aju_no),
                    'qty_cargo' => $request->qty_cargo,
                    'cbm' => $request->cbm,
                    'weight' => 0,
                    'user_id' => $request->created_by,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                DB::commit();
                $message = (['message' => 'success']);
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function getForwarder($name)
    {
        $data = DB::table("mt_forwarder")
            ->select('id')
            ->where('forwarder_name', $name)
            ->orderBy('id', 'DESC')
            ->first();
        return $data;
    }

    private function getShipper($name)
    {
        $data = DB::table("mt_shipper")
            ->select('id')
            ->where('shipper_name', $name)
            ->orderBy('id', 'DESC')
            ->first();
        return $data;
    }

    private function getConsignee($name)
    {
        $data = DB::table("mt_consignee")
            ->select('id')
            ->where('consignee_name', $name)
            ->orderBy('id', 'DESC')
            ->first();

        return $data;
    }

    private function getForwarderByid($id)
    {
        $data = DB::table("mt_forwarder")
            ->where('id', $id)
            ->orderBy('id', 'DESC')
            ->value('forwarder_name');
        return $data;
    }

    private function getShipperByid($id)
    {
        $data = DB::table("mt_shipper")
            ->where('id', $id)
            ->orderBy('id', 'DESC')
            ->value('shipper_name');
        return $data;
    }

    private function getConsigneeByid($id)
    {
        $data = DB::table("mt_consignee")
            ->where('id', $id)
            ->orderBy('id', 'DESC')
            ->value('consignee_name');

        return $data;
    }

    public function getJobPutaway($username)
    {
        $data = DB::table("ex_inbound_header")
            ->where("branch_id", $this->myBranch($username))
            ->where('status_flag', 'Confirmed')
            ->where("putaway_flag", "No")
            ->where("stapel_name", $username)
            ->get();
        $data = $data->map(function ($value) {
            $value->forwarder_name = $this->getForwarderByid($value->forwarder_id);
            $value->shipper_name = $this->getShipperByid($value->shipper_id);
            $value->consignee_name = $this->getConsigneeByid($value->consignee_id);
            return $value;
        });
        return response()->json(['data' => $data]);
    }

    public function detailPutaway($id)
    {
        $header = $this->detailHeader($id);
        $forwarder_name = $this->getForwarderByid($header->forwarder_id);
        $shipper_name = $this->getShipperByid($header->shipper_id);
        $consignee_name = $this->getConsigneeByid($header->consignee_id);
        $detail = DB::table("ex_inbound_detail")
            ->where("job_id", $id)
            ->get();
        $qty_actual = $detail->sum('quantity');
        $total_pallet = $detail->unique(fn($item) => $item->pallet_id . '-' . $item->serial_no)->count();

        return response()->json([
            'data' => [
                'header' => $header,
                'detail' => $detail,
                'forwarder' => $forwarder_name,
                'shipper' => $shipper_name,
                'consignee_name' => $consignee_name,
                'qty_actual' => $qty_actual,
                'total_pallet' => $total_pallet,
            ]
        ]);
    }

    public function getListDetailPutaway($type, $id)
    {
        if ($type == 'notcompleted') {
            $detail = DB::table("ex_inbound_detail")
                ->select(
                    'pallet_id',
                    'id',
                    DB::raw('GROUP_CONCAT(DISTINCT serial_no ORDER BY serial_no ASC SEPARATOR ", ") as po_list'),
                    DB::raw('SUM(quantity) as qty_total'),
                    'job_id',
                    'scan_location',
                    'scan_pallet_tag',
                    'scan_pallet_tag',
                    'unit'
                )
                ->where("job_id", $id)
                ->where('scan_location', 'No')
                ->groupBy('pallet_id')
                ->get()
                ->map(function ($row) {
                    // Ubah string jadi array list
                    $row->po_list = explode(',', $row->po_list);
                    return $row;
                });;
        } else {
            $detail = DB::table("ex_inbound_detail")
                ->select(
                    'pallet_id',
                    'id',
                    DB::raw('GROUP_CONCAT(DISTINCT serial_no ORDER BY serial_no ASC SEPARATOR ", ") as po_list'),
                    DB::raw('SUM(quantity) as qty_total'),
                    'job_id',
                    'scan_location',
                    'scan_pallet_tag',
                    'unit',
                    'location_code'
                )
                ->where("job_id", $id)
                ->where('scan_location', 'Yes')
                ->where('scan_pallet_tag', 'Yes')
                ->groupBy('pallet_id')
                ->get()
                ->map(function ($row) {
                    // Ubah string jadi array list
                    $row->po_list = explode(',', $row->po_list);
                    return $row;
                });;
        }
        $detail = $detail->map(function ($value) {
            $value->header = $this->detailHeader($value->job_id);
            return $value;
        });

        return response()->json(['data' => $detail]);
    }

    public function finishPutaway($id)
    {
        $detail = DB::table("ex_inbound_detail")
            ->where("job_id", $id)
            ->groupBy('pallet_id')
            ->get();

        $locationFlag = $detail->where('scan_location', 'Yes')
            ->where('scan_pallet_tag', 'Yes')
            ->count();

        $confirmFlag = false;
        $duplicateFlag = false;
        $duplicates = null;

        $arrLocation = $detail->pluck('location_code')->toArray();

        // Deteksi lokasi dobel
        $rawDuplicates = array_diff_assoc($arrLocation, array_unique($arrLocation));

        // Filter yang tidak mengandung "FLOOR"
        $filteredDuplicates = array_filter($rawDuplicates, function ($loc) {
            return stripos($loc, 'FLOOR') === false;
        });

        if ($detail->count() == $locationFlag) {
            $confirmFlag = true;
        }

        if (count($filteredDuplicates) > 0) {
            $duplicates = collect($filteredDuplicates)->first();
            $duplicateFlag = true;
        }

        $detail = $detail->map(function ($value) {
            $value->header = $this->detailHeader($value->job_id);
            return $value;
        });

        return response()->json(['data' => [
            'detail' => $detail,
            'confirmFlag' => $confirmFlag,
            'duplicateFlag' => $duplicateFlag,
            'duplicates' => $duplicates,
        ]]);
    }

    public function confirmPutaway(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job_id = ExportInboundHeader::Where('job_no', $request->job_no)
                    ->where('branch_id', $request->branch_id)
                    ->where('job_date', $request->job_date)
                    ->value('id');
                $detail = ExportInboundDetail::Where('job_id', $job_id)->orderBy('id', 'ASC')->get();
                foreach ($detail as $key => $value) {
                    DB::table('ex_stock_ledger')
                        ->where('job_no', $request->job_no)
                        ->where('branch_id', $request->branch_id)
                        ->where('serial_no', $value->serial_no)
                        ->update([
                            'location_id' => $value->location_id,
                            'location_code' => $value->location_code,
                        ]);
                }
                DB::table('ex_inbound_header')
                    ->where('id', $job_id)
                    ->update([
                        'putaway_flag' => 'Yes',
                    ]);
                DB::commit();
                $success = ['data' => ['success']];
                return $success;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => true, 'message' => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function cancelPutaway($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $inStock = DB::table('ex_inbound_detail')
                    ->where('id', $id)
                    ->first();

                DB::table('ex_inbound_detail')
                    ->where('id', $id)
                    ->update([
                        'scan_location'        => 'No',
                        'location_id'          => null,
                        'location_code'        => null
                    ]);

                DB::table('ex_inbound_detail')
                    ->where('serial_no', $inStock->serial_no)
                    ->update([
                        'scan_location'        => 'No',
                        'location_id'          => null,
                        'location_code'        => null
                    ]);

                $header = $this->detailHeader($inStock->job_id);
                $shipper_name = $this->getShipperByid($header->shipper_id);
                $detail = DB::table("ex_inbound_detail")->where("job_id", $header->id)->get();
                $qty_actual = $detail->sum('quantity');
                $total_pallet = $detail->groupBy('pallet_id')->count();

                DB::commit();
                $success = ['data' => [
                    'status' => 'succcess',
                    'header' => $header,
                    'detail' => $detail,
                    'shipper' => $shipper_name,
                    'qty_actual' => $qty_actual,
                    'total_pallet' => $total_pallet,
                ]];
                return $success;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => true, 'message' => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function detailHeader($id)
    {
        $data = DB::table("ex_inbound_header")
            ->where("id", $id)
            ->first();
        return $data;
    }
}
