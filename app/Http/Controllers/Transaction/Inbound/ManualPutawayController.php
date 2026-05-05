<?php

namespace App\Http\Controllers\Transaction\Inbound;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Inbound\Batch as inboundBatch;
use App\Models\Transaction\Inbound\Vehicle as inboundVehicle;
use App\Models\Transaction\Inbound\Job as inboundJob;
use App\Models\Master\Location as masterLocation;
use App\Models\Master\PalletUnit as masterPalletUnit;

class ManualPutawayController extends Controller
{
    public function __construct()
    {
        if (!GlobalHelpers::checkLogin()) {
            return response()->redirectTo("login");
        }
    }

    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $list_data = inboundBatch::from('iv_inbound_batch as a')
                ->select('a.*', 'b.product_name', 'c.site_name', 'd.area_name')
                ->join('iv_product as b', 'a.product_id', 'b.id')
                ->join('iv_site as c', 'a.site_id', 'c.id')
                ->join('iv_site_area as d', 'a.area_id', 'd.id')
                ->where('a.company_id', $company_id)
                ->where('a.inbound_id', $request->inbound_id)
                ->where('a.confirmed_flag', 'No')
                ->get();

            return datatables()->of($list_data)
                ->editColumn('exp_date', function ($data) {
                    return date('d/m/Y', strtotime($data->exp_date));
                })
                ->editColumn('mfg_date', function ($data) {
                    return date('d/m/Y', strtotime($data->mfg_date));
                })
                ->addColumn('action', function ($data) {
                    $button = "";
                    if ($data->confirmed_flag == 'No') {
                        $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Edit" class="edit-manual btn btn-info btn-sm"><i class="far fa-edit"></i></a>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<button type="button" id="' . $data->id . '" class="delete-manual btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $messsages = array(
            'vehicle_manual.required' => 'Vehicle no field is required.',
            'product_id.required' => 'Product name field is required.',
            'site_id.required' => 'Site name field is required.',
            'area_id.required' => 'Area name field is required.',
            'location_id.required' => 'Location field is required.',
        );

        $rules = array(
            'vehicle_manual' => 'required',
            'product_id' => 'required',
            'site_id' => 'required',
            'area_id' => 'required',
            'location_id' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $qty = ($request->pqty * $request->uppp) + ($request->mqty * $request->muppp) + $request->bqty;

        if ($qty == 0) {
            return response()->json(['error' => ['Quantity cannot be empty!']]);
        }

        $exception = DB::transaction(function () use ($request, $qty) {
            $vehicle = inboundVehicle::where('inbound_id', $request->inbound_packing)
                ->where('vehicle_no', $request->vehicle_packing)->first();

            try {
                $id = $request->packing_id;
                $inbound_id = $request->inbound_packing;
                $company_id = Auth::user()->company_id;

                $job = inboundJob::find($inbound_id);
                $principal_id = $job->principal_id;

                if (isset($id) && !empty($id)) {
                    $batchin = inboundBatch::find($id);

                    if ($batchin->location_code !== $request->m_location_code) {
                        $location_old = masterLocation::where('company_id', $company_id)
                            ->where('site_id', $batchin->site_id)
                            ->where('area_id', $batchin->area_id)
                            ->where('location_id', $batchin->location_id)->first();

                        $location_old->status_code = 'E';
                        $location_old->save();
                    }
                } else {
                    $batchin = new inboundBatch;
                }

                $location = masterLocation::find($request->location_id);

                $pallet_qty = masterPalletUnit::select('base_qty')
                    ->where('company_id', $company_id)
                    ->where('principal_id', $principal_id)
                    ->where('product_id', $request->product_id)
                    ->where('type_id', $location->type_id)
                    ->first();

                if ($qty > $pallet_qty->base_qty) {
                    DB::rollBack();

                    $message = ['error' => ['Pallet quantity maximum : ' . $pallet_qty->base_qty / $request->uppp . ' ' . $request->puom]];

                    return $message;
                }

                $mfg_date = null;
                if (isset($request->mfg_date) && !empty($request->mfg_date)) {
                    $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->mfg_date);
                    $mfg_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');
                }

                $exp_date = null;
                if (isset($request->exp_date) && !empty($request->exp_date)) {
                    $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->exp_date);
                    $exp_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');
                }

                $batchin->company_id = $company_id;
                $batchin->inbound_id = $inbound_id;
                $batchin->principal_id = $job->principal_id;
                $batchin->packing_id = 0;
                $batchin->serial_no = 0;
                $batchin->job_no = $job->job_no;
                $batchin->vehicle_no = $request->vehicle_manual;
                $batchin->product_id = $request->product_id;
                $batchin->product_code = $request->product_code;
                $batchin->po_number = $request->po_number;
                $batchin->lot_no = $request->lot_no;
                $batchin->document_ref = $request->document_ref;
                $batchin->manufactur_id = $request->manufactur_id;
                $batchin->status_id = $request->status_id;
                $batchin->mfg_date = $mfg_date;
                $batchin->exp_date = $exp_date;
                $batchin->puom = $request->puom;
                $batchin->muom = $request->muom;
                $batchin->buom = $request->buom;
                $batchin->uppp = $request->uppp;
                $batchin->muppp = $request->muppp;
                $batchin->pqty = $request->pqty;
                $batchin->mqty = $request->mqty;
                $batchin->bqty = $request->bqty;
                $batchin->qty = $qty;
                $batchin->site_id = $request->site_id;
                $batchin->area_id = $request->area_id;
                $batchin->location_id = $request->location_id;
                $batchin->location_code = $location->location_code;
                $batchin->save();

                $location->status_code = 'R';
                $location->save();

                DB::commit();

                $message = ['success' => 'Data Successfully Saved'];

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
