<?php

namespace App\Http\Controllers\NewUpdated\CrossdockNew\Scan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PermintaanEditOutboundEmail;
use App\Mail\KonfirmasiEditOutbound;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Arr;


class ScanCargoController extends Controller
{
    private function myBranch()
    {
        $branch = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('branch_id')->toArray();

        return $branch;
    }

    private function getCustomer()
    {
        $customer = DB::table('cross_mt_customer')
            ->whereIn('id_branch', $this->myBranch())
            ->get();

        return $customer;
    }

    private function getBranch()
    {
        $branch = DB::table('mt_branch')
            ->whereIn('id', $this->myBranch())
            ->get();

        return $branch;
    }

    private function getJob()
    {
        $data = DB::table('cross_outbound_header')
            ->whereIn('id_branch', $this->myBranch())
            ->get();

        return $data;
    }

    private function getWarehouse()
    {
        $data = DB::table('cross_user_warehouse')
            ->where('id_user', Auth::user()->id)
            ->get()->pluck('id_warehouse')->toArray();

        $data = DB::table('cross_mt_warehouse')
            ->whereIn('id', $data)
            ->get();

        return $data;
    }

    public function index()
    {
        $customer       = $this->getCustomer();
        $warehouse      = $this->getWarehouse();
        $branch         = $this->getBranch();

        return view('new.CrossDock.Scan.index', compact('customer', 'warehouse', 'branch'));
    }

    private function getCargoList($id)
    {
        $data = DB::table('cross_outbound_detail')
            ->where('id_header', $id)
            ->get();

        return $data;
    }

    public function getStock($cargo_id, $id_warehouse, $id_customer, $id_branch, $id_header)
    {
        $id_stock = $this->getCargoList($id_header)->pluck('id_stock')->toArray();

        $data = DB::table('cross_stock_ledger')
            ->where('id_cargo', $cargo_id)
            ->where('id_warehouse', $id_warehouse)
            ->where('id_customer', $id_customer)
            ->where('id_branch', $id_branch)
            ->where('on_actual', '>', 0)
            ->whereNotIn('id', $id_stock)
            ->get();

        return response()->json($data);
    }

    public function searchJob($id_warehouse)
    {
        $data = DB::table('cross_outbound_header')
            ->where('id_warehouse', $id_warehouse)
            ->get();

        $data->map(function ($value) {
            $value->warehouse = $this->getWarehouse($value->id_warehouse)->first()->name;
            $value->customer  = $this->whereCustomer($value->id_customer);
        });

        return datatables()->of($data)->make(true);
    }

    private function whereCustomer($id)
    {
        $customer = DB::table('cross_mt_customer')
            ->where('id', $id)
            ->value('name');

        return $customer;
    }

    private function whereWarehouse($id)
    {
        $warehouse = DB::table('cross_mt_warehouse')
            ->where('id', $id)
            ->value('name');

        return $warehouse;
    }


    public function detailJob($id)
    {
        $id             = Crypt::decryptString($id);
        $data           = $this->joinTableCargo()->where('id_header', $id)->where('scan_flag', 'No');
        if (count($data) > 0) {
            $customer       = $this->whereCustomer($data->first()->id_customer);
            $warehouse      = $this->whereWarehouse($data->first()->id_warehouse);
        } else {
            Session::flash('warning', 'Cargo has been scanned..');
            return back();
        }

        return view('new.CrossDock.Scan.show', compact('data', 'customer', 'warehouse'));
    }

    public function validasiCargo($qr, $id_detail, $id_stock)
    {
        $exception = DB::transaction(function () use ($qr, $id_detail, $id_stock) {
            try {
                $master = DB::table('cross_stock_ledger')
                    ->where('id', $id_stock)
                    ->where('qrcode', $qr)
                    ->first();
                if (!is_null($master)) {
                    DB::table('cross_outbound_detail')
                        ->where('id', $id_detail)
                        ->update([
                            'scan_flag' => 'Yes',
                            'scan_at'   => date('Y-m-d H:i:s'),
                            'scan_by'   => Auth::user()->username,
                            'status'       => 5
                        ]);

                    DB::commit();

                    $message = ['message' => 'Data Successfully Saved'];

                    return $message;
                } else {
                    DB::rollBack();
                    $message = ['message' => 'notfound'];
                    return $message;
                }
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function detailJobFrontend($id)
    {
        return redirect('crossDock/scanCargo/detailJob/' . Crypt::encryptString($id));
    }

    public function showJobMe()
    {
        $data = DB::table('cross_outbound_header')
            ->where('created_by', Auth::user()->username)
            ->orderBy('id', 'DESC')
            ->first();

        return $data;
    }

    public function storeOrderDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $master = DB::table('cross_stock_ledger')->whereIn('id', $request->id)->get();
                foreach ($master as $value) {
                    DB::table('cross_outbound_detail')
                        ->insert([
                            'id_header'     => $request->id_header,
                            'id_stock'      => $value->id,
                            'qty'           => $value->on_actual,
                            'date_out'      => date('Y-m-d'),
                            'created_at'    => date('Y-m-d H:i:s'),
                            'created_by'    => Auth::user()->username,
                            'status'        => 2
                        ]);
                }
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

    public function deleteDetail($id)
    {
        DB::table('cross_outbound_detail')
            ->where('id', $id)
            ->delete();

        Session::flash('success', 'Data Berhasil Di Hapus..');
        return back();
    }

    public function editCargo($id)
    {
        $data = DB::table('cross_outbound_detail')
            ->where('id', $id)
            ->first();

        return response()->json($data);
    }

    public function updateCargo(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                for ($i = 0; $i < count($request->id); $i++) {
                    $validate = DB::table('cross_stock_ledger')
                        ->where('id', $request->id_stock[$i])
                        ->first();

                    if ($request->qty[$i] > $validate->on_actual) {
                        DB::rollBack();
                        $message = ['error' => 'ID CARGO: ' . $validate->id_cargo . '-SKU:' . $validate->sku . ' stock only: ' . $validate->on_actual . ' Your request:' . $request->qty[$i]];
                        return $message;
                    } else {
                        DB::table('cross_outbound_detail')
                            ->where('id', $request->id[$i])
                            ->update([
                                'qty'           => $request->qty[$i],
                            ]);
                    }
                }
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

    public function deleteCargo($id)
    {
        $id_stock = DB::table('cross_outbound_detail')
            ->where('id', $id)
            ->first()->id_stock;

        if (!is_null($id_stock)) {
            $stock = DB::table('cross_stock_ledger')
                ->where('id', $id_stock)
                ->first();

            DB::table('cross_stock_ledger')
                ->where('id', $id_stock)
                ->update([
                    'on_booking' => 0,
                    'on_actual'  => $stock->on_booking == 0 ? $stock->on_hand : $stock->on_booking + $stock->on_actual
                ]);
        }

        DB::table('cross_outbound_detail')
            ->where('id', $id)
            ->delete();


        Session::flash('success', 'Data Successfully Deleted');
        return back();
    }

    public function confirmation($type, $id)
    {
        $exception = DB::transaction(function () use ($type, $id) {
            try {
                $status = 0;
                if ($type == 'order-detail') {
                    $status = 3;
                } elseif ($type == 'picking') {
                    $status = 4;
                } else {
                    // $this->addToStockAndTransaction($data);
                    $status = 0;
                }

                DB::table('cross_outbound_header')
                    ->where('id', $id)
                    ->update([
                        'status' => $status
                    ]);

                DB::table('cross_outbound_detail')
                    ->where('id_header', $id)
                    ->update([
                        'status'       => $status,
                    ]);

                DB::commit();

                $message = ['pesan' => 'Data has been successfully processed'];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function postPicking(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                for ($i = 0; $i < count($request->id); $i++) {
                    $id_outbound = explode('-', $request->id[$i])[0];
                    $id_stock    = explode('-', $request->id[$i])[1];

                    $stock       = DB::table('cross_stock_ledger')
                        ->where('id', $id_stock)
                        ->first();

                    $id_header   = DB::table('cross_outbound_detail')
                        ->where('id', $id_outbound)
                        ->first()->id_header;

                    DB::table('cross_outbound_detail')
                        ->where('id', $id_outbound)
                        ->update([
                            'picking_flag' => 'Yes',
                            'picking_at'   => date('Y-m-d H:i:s'),
                            'picking_by'   => Auth::user()->username,
                            'status'       => 4
                        ]);

                    DB::table('cross_stock_ledger')
                        ->where('id', $id_stock)
                        ->update([
                            'on_booking' => $request->qty[$i],
                            'on_actual'  => $stock->on_actual - $request->qty[$i],
                        ]);

                    DB::table('cross_outbound_detail')
                        ->where('id', $id_outbound)
                        ->update([
                            'status' => 4,
                        ]);

                    DB::table('cross_outbound_header')
                        ->where('id', $id_header)
                        ->update([
                            'status' => 4,
                        ]);
                }

                DB::commit();

                $message = ['pesan' => 'Data has been successfully processed'];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function scanByPass(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                for ($i = 0; $i < count($request->id); $i++) {
                    DB::table('cross_outbound_detail')
                        ->where('id', $request->id[$i])
                        ->update([
                            'scan_flag' => 'Yes',
                            'scan_at'   => date('Y-m-d H:i:s'),
                            'scan_by'   => Auth::user()->username,
                            'status'       => 5
                        ]);
                }

                DB::commit();

                $message = ['pesan' => 'Data has been successfully processed'];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function postDespatch(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                DB::table('cross_outbound_despatch')
                    ->insert([
                        'id_header'    => $request->id_header,
                        'carrier_name' => $request->carrier_name,
                        'etd'          => $request->etd,
                        'vehicle_no'   => $request->vehicle_no,
                        'awb_no'       => $request->awb_no,
                        'driver_name'  => $request->driver_name,
                        'awb_date'     => $request->awb_date,
                        'container_no' => $request->container_no,
                        'ref_number'   => $request->ref_number,
                        'send_date_doc' => $request->send_date_doc,
                        'store_name'    => $request->store_name,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'created_by'    => Auth::user()->username
                    ]);

                DB::table('cross_outbound_header')
                    ->where('id', $request->id_header)
                    ->update([
                        'status'    => 5,
                    ]);

                DB::commit();

                $message = ['pesan' => 'Data has been successfully processed'];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function updateDespatch(Request $request)
    {
        DB::table('cross_outbound_despatch')
            ->where('id_header', $request->id_header)
            ->update([
                'carrier_name' => $request->carrier_name,
                'etd'          => $request->etd,
                'vehicle_no'   => $request->vehicle_no,
                'awb_no'       => $request->awb_no,
                'driver_name'  => $request->driver_name,
                'awb_date'     => $request->awb_date,
                'container_no' => $request->container_no,
                'ref_number'   => $request->ref_number,
                'send_date_doc' => $request->send_date_doc,
                'store_name'    => $request->store_name,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->username,
            ]);

        Session::flash('success', 'Data has been saved successfully..');
        return back();
    }

    public function confirmOutbound(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                for ($i = 0; $i < count($request->id); $i++) {
                    $id_stock    = explode('-', $request->id[$i])[1];

                    $stock = DB::table('cross_stock_ledger')
                        ->where('id', $id_stock)
                        ->first();

                    DB::table('cross_stock_ledger')
                        ->where('id', $id_stock)
                        ->update([
                            'on_hand'    => abs($stock->on_hand - $stock->on_booking),
                            'on_booking' => 0,
                        ]);
                }

                DB::table('cross_outbound_detail')
                    ->where('id_header', $request->id_header)
                    ->update([
                        'status' => 0,
                        'confirmed_at' => date('Y-m-d H:i:s')
                    ]);

                DB::table('cross_outbound_despatch')
                    ->where('id_header', $request->id_header)
                    ->update([
                        'status' => 0
                    ]);

                DB::table('cross_outbound_header')
                    ->where('id', $request->id_header)
                    ->update([
                        'status' => 0
                    ]);

                $array = $this->joinTableCargo($request->id_header);
                $this->AddToTransaction($array);

                DB::commit();

                $message = ['pesan' => 'Data has been successfully processed'];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    private function AddToTransaction($array)
    {
        foreach ($array as $key => $value) {
            //add to stock
            DB::table('cross_stock_transaction')
                ->insert([
                    'id_branch'     => $value->id_branch,
                    'id_outbound'   => $value->id_header,
                    'location_code' => $value->location_code,
                    'unit'          => 'Harcode',
                    'id_inbound' => $value->id_header,
                    'type_job'   => 'out',
                    'qty'        => $value->qty,
                    'id_cargo'   => $value->stock->id_cargo,
                    'sku'        => $value->stock->sku,
                    'p'          => $value->stock->p,
                    'l'          => $value->stock->l,
                    't'          => $value->stock->t,
                    'w'             => $value->stock->w,
                    'cbm_per_unit'  => $value->stock->cbm_per_unit,
                    'cbm_total'     => $value->stock->cbm_total,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->username
                ]);
        }
    }

    private function joinTableCargo()
    {
        $data = DB::table('cross_outbound_header as header')
            ->select(
                'header.*',
                'detail.id as id_detail',
                'detail.id_header',
                'detail.id_stock',
                'detail.qty',
                'detail.scan_flag',
                'detail.picking_flag',
            )
            ->join('cross_outbound_detail as detail', 'detail.id_header', '=', 'header.id')
            ->get();
        $data->map(function ($value) {
            $value->stock = DB::table('cross_stock_ledger')->where('id', $value->id_stock)->first();
        });

        return $data;
    }

    public function report($type, $id)
    {
        $data           = $this->joinTableCargo()->where('id_header', $id);
        $customer       = $this->getCustomer();
        $warehouse      = $this->getWarehouse();
        $despatch       = $this->getDespatch($id)->where('id_header', $data->first()->id_header)->first();
        $v_despatch  = $data->first();

        if ($type == 'picking') {
            $tittle = 'Picking Report ';
        } elseif ($type == 'despatch') {
            $tittle = '';
        }

        return view('new.CrossDock.Report.Outbound.' . $type, compact('data', 'tittle', 'customer', 'despatch', 'warehouse', 'v_despatch'));
    }
}
