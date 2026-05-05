<?php

namespace App\Http\Controllers\NewUpdated;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VMPriceTemplate as VMPriceTemplate;
use App\Exports\VMPriceTemplateEdit as VMPriceTemplateHarga;
use App\Imports\VMPriceUpload as VMPriceUpload;
use App\Imports\VMPriceUpdateHargaFromExcel as VMPriceUpdateHargaFromExcel;
use Carbon\Carbon;
use Illuminate\Support\Carbon as SupportCarbon;

class TracingCustomereExportController extends Controller
{
    public function index()
    {
        return view('new.TracingCustomerExport.index');
    }

    public function trace(Request $request)
    {
        return $this->getData($request->peb_no);
    }

    private function getData($peb_no)
    {
        $data = DB::table('ex_inbound_header')
            ->where('peb_no', $peb_no)
            ->whereYear('created_at', date('Y'))
            ->orderBy('id', 'DESC')
            ->get();
        $data = $data->map(function ($value) {
            $value->shipper_name = DB::table('mt_shipper')->where('id', $value->shipper_id)->value('shipper_name') ?? '-';
            return $value;
        });
        $list = [];
        foreach ($data as $key => $value) {
            $list[] = [
                'peb_no' => $value->peb_no,
                'shipper_name' => $value->shipper_name,
                'gate_in' => is_null($value->gate_in) ? '-' : Carbon::parse($value->gate_in)->format('d-m-Y H:i') . ' WIB',
                'vehicle_no' => $value->vehicle_no,
            ];
        }
        return response()->json($list);
    }

    public function getListMasterPrice($service, $mot, $vendor)
    {
        $data = $this->getMasterPrice($service, $mot)->where('vendor', $vendor);
        return datatables()->of($data)
            ->editColumn('vehicle_type', function ($data) {
                if (!is_null($data->vehicle_type)) {
                    $vehicle_type = $data->vehicle_type;
                } else {
                    $vehicle_type = "-";
                }
                return $vehicle_type;
            })
            ->editColumn('uom', function ($data) {
                if (!is_null($data->uom)) {
                    $uom = $data->uom;
                } else {
                    $uom = "-";
                }
                return $uom;
            })
            ->editColumn('min_charge', function ($data) {
                if (!is_null($data->min_charge)) {
                    $min_charge = $data->min_charge;
                } else {
                    $min_charge = "";
                }
                return $min_charge;
            })
            ->editColumn('vendor', function ($data) {
                if (!is_null($data->vendor)) {
                    $vendor = $data->vendor;
                } else {
                    $vendor = "";
                }
                return $vendor;
            })
            ->editColumn('mot', function ($data) {
                if (!is_null($data->mot)) {
                    $mot = $data->mot;
                } else {
                    $mot = "";
                }
                return $mot;
            })
            ->editColumn('product_type', function ($data) {
                if (!is_null($data->product_type)) {
                    $product_type = $data->product_type;
                } else {
                    $product_type = "-";
                }
                return $product_type;
            })
            ->editColumn('vendor', function ($data) {
                if (!is_null($data->vendor)) {
                    $vendor = $data->vendor;
                } else {
                    $vendor = "-";
                }
                return $vendor;
            })
            ->editColumn('price', function ($data) {
                if (!is_null($data->price)) {
                    $price = number_format($data->price, 0, ",", ".");
                } else {
                    $price = "-";
                }
                return $price;
            })
            ->editColumn('valid_untill', function ($data) {
                if (!is_null($data->valid_untill)) {
                    $valid_untill =  formatTanggalIndonesia2($data->valid_untill, 2, ',', '.');
                } else {
                    $valid_untill = "-";
                }
                return $valid_untill;
            })
            ->editColumn('created_at', function ($data) {
                if (!is_null($data->created_at)) {
                    $created_at =  formatTanggalIndonesia2($data->created_at, 2, ',', '.');
                } else {
                    $created_at = "-";
                }
                return $created_at;
            })
            ->rawColumns(['vehicle_type', 'kota_kab', 'uom', 'vendor', 'min_charge', 'vendor', 'mot', 'product_type', 'price', 'valid_untill'])
            ->addIndexColumn()
            ->make(true);
    }

    public function priceMaster()
    {
        $service = DB::table('price_master')
            ->select('service')
            ->where('service', '!=', '')
            ->groupBy('service')
            ->get();
        return view("new.VMPrice.PriceMaster", compact('service'));
    }

    public function priceTrace()
    {
        $master = DB::table('price_master')
            ->where('service', '!=', '')
            ->where('active', 'Yes')
            ->get();
        return view("new.VMPrice.PriceTrace", compact('master'));
    }

    public function priceActivity()
    {
        $data = DB::table('price_activity')->whereDate('created_at', date('Y-m-d'))->get();
        return view("new.VMPrice.PriceActivityUsers", compact('data'));
    }

    public function detailActivity($user_id)
    {
        $data = DB::table('price_activity')
            ->orderBy('id', 'DESC')
            ->where('user_id', $user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->get();
        $data = $data->map(function ($value) {
            $value->time = Carbon::parse($value->created_at)->format('d-M-Y H:i');
            $value->ship_date = Carbon::parse($value->shipment_date)->format('d-M-Y');
            return $value;
        });
        return response()->json($data);
    }

    public function templateUploadPrice($service, $mot)
    {
        return Excel::download(new VMPriceTemplate($service, $mot), "template-upload-price.xlsx");
    }

    public function templateEditHarga(Request $request)
    {
        return Excel::download(new VMPriceTemplateHarga($request->service, $request->mot, $request->vendor), "template-update-price.xlsx");
    }

    public function uploadPrice(Request $request)
    {
        $file = $request->file('file');
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
        Excel::import(new VMPriceUpload($request->service, $request->mot), $file);
        return back();
    }

    public function updatePriceExcel(Request $request)
    {
        $file = $request->file('file');
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
        Excel::import(new VMPriceUpdateHargaFromExcel($request->service, $request->mot), $file);
        return back();
    }

    public function disablePrice($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                DB::table('price_master')
                    ->where('id', $id)
                    ->update([
                        'active' => 'No'
                    ]);

                DB::table('price_fcl_sea')
                    ->where('id_master', $id)
                    ->update([
                        'active' => 'No'
                    ]);

                $message = ['success' => 'successfully..'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function historyData($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $history = $this->getHistoryMaster($id);
                $master = $this->objectMaster($id);
                DB::commit();
                $data = ['data' =>
                [
                    'history' => $history,
                    'master' => $master
                ]];
                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function editData($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $master = $this->objectMaster($id);
                $detail = null;
                if ($master->service == 'FCL' and $master->mot == 'SEA') {
                    $detail = DB::table('price_fcl_sea')
                        ->where('id_master', $master->id)
                        ->first();
                }
                return [
                    'object' => [
                        'master' => $master,
                        'detail' => $detail
                    ]
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function updateData(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $master = DB::table('price_master')
                    ->where('id', $request->id_master)
                    ->first();

                DB::table('price_history')
                    ->insert([
                        'master_id' => $master->id,
                        'price_old' => $master->price,
                        'valid_untill_old' => $master->valid_untill,
                        'price_new' => $request->price,
                        'valid_untill_new' => $request->valid_untill,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::user()->username,
                    ]);

                DB::table('price_master')
                    ->where('id', $request->id_master)
                    ->update([
                        'min_charge' => isset($request->min_charge) ? $request->min_charge : null,
                        'price' => $request->price,
                        'valid_untill' => $request->valid_untill,
                    ]);

                DB::table('price_fcl_sea')
                    ->where('id_master', $request->id_master)
                    ->update([
                        'trucking_origin' => isset($request->trucking_origin) ? $request->trucking_origin : null,
                        'adm_bl' => isset($request->adm_bl) ? $request->adm_bl : null,
                        'segel' => isset($request->segel) ? $request->segel : null,
                        'materai' => isset($request->materai) ? $request->materai : null,
                        'apbs' => isset($request->apbs) ? $request->apbs : null,
                        'thc_lolo' => isset($request->thc_lolo) ? $request->thc_lolo : null,
                        'ffs' => isset($request->ffs) ? $request->ffs : null,
                        'ocf' => isset($request->ocf) ? $request->ocf : null,
                        'thc_lolo_destinasi' => isset($request->thc_lolo_destinasi) ? $request->thc_lolo_destinasi : null,
                        'trucking_destinasi' => isset($request->trucking_destinasi) ? $request->trucking_destinasi : null,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->username,
                    ]);

                DB::commit();
                $message = ['message' => 'Data Successfully Saved'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getKotaKab($origin, $mot, $prod, $service, $vehicle)
    {
        $exception = DB::transaction(function () use ($origin, $mot, $prod, $service, $vehicle) {
            try {
                $master = $this->getMaster()
                    ->where('origin', $origin)
                    ->where('mot', $mot)
                    ->where('product_type', $prod)
                    ->where('service', $service)
                    // ->Where(DB::raw("COALESCE(vehicle_type, null)"), $vehicle)
                    ->whereNotNull('kota_kab');
                if ($vehicle != 'null') {
                    $vehicle  = $master->where('vehicle_type', $vehicle);
                }
                $master = $master->groupBy('kota_kab');
                return $master;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getSelectService($mot, $prod)
    {
        $exception = DB::transaction(function () use ($mot, $prod) {
            try {
                $master = $this->getMaster()
                    ->where('mot', $mot)
                    ->where('product_type', $prod)
                    ->groupBy('service');
                return $master;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getDestination($origin, $kotakab, $mot, $prod, $service, $vehicle)
    {
        $exception = DB::transaction(function () use ($origin, $kotakab, $mot, $prod, $service, $vehicle) {
            try {
                $master = $this->getMaster()
                    ->where('origin', $origin)
                    ->where('mot', $mot)
                    ->where('product_type', $prod)
                    ->where('service', $service)
                    ->where('kota_kab', $kotakab)
                    ->whereNotNull('destination');
                if ($vehicle != 'null') {
                    $vehicle  = $master->where('vehicle_type', $vehicle);
                }
                $master = $master->groupBy('destination');
                return $master;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getSelectMOT($origin, $kota_kab, $destination)
    {
        $exception = DB::transaction(function () use ($origin, $kota_kab, $destination) {
            try {
                $master = $this->getMaster()
                    ->where('origin', $origin)
                    ->where('kota_kab', $kota_kab)
                    ->where('destination', $destination)
                    ->whereNotNull('mot')
                    ->groupBy('mot');
                return $master;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getProdType($origin, $kota_kab, $destination, $mot)
    {
        $exception = DB::transaction(function () use ($origin, $kota_kab, $destination, $mot) {
            try {
                $master = $this->getMaster()
                    ->where('origin', $origin)
                    ->where('kota_kab', $kota_kab)
                    ->where('destination', $destination)
                    ->where('mot', $mot)
                    ->whereNotNull('product_type')
                    ->groupBy('product_type');
                return $master;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    // public function getSelectService($origin, $kota_kab, $destination, $mot, $prod)
    // {
    //     $exception = DB::transaction(function () use ($origin, $kota_kab, $destination, $mot, $prod) {
    //         try {
    //             $master = $this->getMaster()
    //                 ->where('origin', $origin)
    //                 ->where('kota_kab', $kota_kab)
    //                 ->where('destination', $destination)
    //                 ->where('mot', $mot)
    //                 ->where('product_type', $prod)
    //                 ->whereNotNull('service')
    //                 ->groupBy('service');
    //             return $master;
    //         } catch (\Exception $e) {
    //             DB::rollBack();
    //             $message = ['error' => $e->getMessage()];
    //             return $message;
    //         }
    //     });
    //     return response()->json($exception);
    // }

    public function getSelectVehicle($service)
    {
        $exception = DB::transaction(function () use ($service) {
            try {
                $master = $this->getMaster()
                    ->where('service', $service)
                    ->whereNotNull('vehicle_type')
                    ->groupBy('vehicle_type');
                return $master;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function traceHarga(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $vehicle_type = "";
                if (!is_null($request->vehicle_type) or isset($request->vehicle_type)) {
                    $vehicle_type = $request->vehicle_type;
                }
                $data = DB::table('price_master')
                    ->orderBy('price', 'ASC')
                    ->where('origin', $request->origin)
                    ->where('destination', $request->destination)
                    ->where('product_type', $request->product_type)
                    ->where('kota_kab', $request->kota_kab)
                    ->where('service', $request->service)
                    ->where('price', '>', 0)
                    ->where(DB::raw("COALESCE(vehicle_type, '')"), $vehicle_type)
                    ->get();

                $data = $data->map(function ($value) use ($request) {
                    $valid_untill = Carbon::parse($value->valid_untill);
                    $shipment = Carbon::parse($request->shipment);
                    if ($shipment > $valid_untill) {
                        $value->flag_expired = true;
                    } else {
                        $value->flag_expired = false;
                    }
                    return $value;
                });

                //insert to history pencatian
                $this->historyPencarian($request);

                //get parameter
                $params = $this->getParameter($request->mot, $request->product_type, $request->service);

                return ['data' => [
                    'data' => $data,
                    'params' => $params
                ]];
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function historyPencarian($request)
    {
        DB::transaction(function () use ($request) {
            try {
                DB::table('price_activity')
                    ->insert([
                        'user_id' => Auth::user()->id,
                        'origin'  => $request->origin,
                        'destination'  => $request->destination,
                        'product_type'  => $request->product_type,
                        'service'  => $request->service,
                        'mot'  => $request->mot,
                        'vehicle_type'  => $request->vehicle_type,
                        'shipment_date'  =>  Carbon::parse($request->shipment_date)->format('Y-m-d'),
                        'kg'  => $request->kg,
                        'cbm'  => $request->cbm,
                        'created_at'  => date('Y-m-d H:i:s'),
                        'created_by'  => Auth::user()->username,
                    ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }
        });
    }

    private function getParameter($mot, $product_type, $service)
    {
        $data = DB::table('price_params')
            ->where('mot', $mot)
            ->where('product_type', $product_type)
            ->where('service', $service)
            ->first();
        return $data;
    }
}
