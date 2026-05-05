<?php

namespace App\Http\Controllers\NewUpdated\CrossdockNew\Outbound;

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


class OutboundController extends Controller
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

    private function getVehicle()
    {
        $vehicle = DB::table('cross_mt_vehicle')
            ->get();

        return $vehicle;
    }

    private function getVehicleSize()
    {
        $vehicleSize = DB::table('cross_mt_vehicle_size')
            ->get();

        return $vehicleSize;
    }

    private function getJobNo()
    {
        $job = DB::table('cross_outbound_header')
            ->whereIn('id_branch', $this->myBranch())
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->get();

        $substr = $job->max('job_no') ?? 0;
        if ($job->count() > 0) {
            $increment = substr($substr, 6, 3) + $job->count() > 0 ? $job->count() + 1  : 0 + 1;
        } else {
            $increment = 1;
        }

        $job_no = 4 . date('ym') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');
        return $job_no;
    }

    public function index()
    {
        $job_no         = $this->getJobNo();
        $customer       = $this->getCustomer();
        $warehouse      = $this->getWarehouse();
        $branch         = $this->getBranch();
        $now            = date('Y-m-d');

        return view('new.CrossDock.Outbound.index', compact('job_no', 'now', 'customer', 'warehouse',  'branch'));
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

    private function getHeader($id)
    {
        $data = DB::table('cross_outbound_header')
            ->where('id', $id)
            ->first();

        return $data;
    }

    private function getDespatch($id)
    {
        $data = DB::table('cross_outbound_despatch')
            ->where('id_header', $id)
            ->get();

        return $data;
    }

    public function showJob($id)
    {
        $id = Crypt::decryptString($id);
        $header         = $this->getHeader($id);
        $customer       = $this->getCustomer();
        $warehouse      = $this->getWarehouse();
        $branch         = $this->getBranch();
        $cargo          = $this->getCargoList($id);
        $vehicle        = $this->getVehicle();
        $vehicleSize    = $this->getVehicleSize();

        $cargo->map(function ($value) {
            $value->stock = DB::table('cross_stock_ledger')
                ->where('id', $value->id_stock)
                ->first();
        });
        $despatch = $this->getDespatch($id);
        $scanning = $cargo->where('scan_flag', 'Yes')->count();
        $btn_confirm = false;
        if ($cargo->count() == $scanning) {
            $btn_confirm = true;
        }
        $menu_header    = false;
        $order_detail   = false;
        $menu_confirm   = false;
        $menu_picking   = false;
        $menu_scan      = false;
        if ($header->status == 2) {
            $order_detail = true;
        } elseif ($header->status == 3) {
            $menu_picking = true;
        } else if ($header->status == 4) {
            $menu_scan = true;
        } else {
            $menu_confirm = true;
        }

        return view('new.CrossDock.Outbound.show', compact('customer', 'warehouse', 'branch', 'header', 'cargo', 'menu_header',  'order_detail', 'menu_picking', 'despatch', 'menu_scan', 'menu_confirm', 'btn_confirm', 'vehicleSize', 'vehicle'));
    }

    public function editDespatch($id)
    {
        $data = $this->getDespatch($id)->first();

        return response()->json($data);
    }

    public function showJobFrontend($id)
    {
        return redirect('crossDock/outbound/showJob/' . Crypt::encryptString($id));
    }

    public function showJobMe()
    {
        $data = DB::table('cross_outbound_header')
            ->where('created_by', Auth::user()->username)
            ->orderBy('id', 'DESC')
            ->first();

        return $data;
    }

    public function storeHeader(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'id_customer' => 'required',
            'job_no' => 'required',
            'id_branch' => 'required',
            'po_no' => 'required',
            'do_no' => 'required',
        ]);

        DB::table('cross_outbound_header')->insert([
            'id_warehouse'     => $request->id_warehouse,
            'description'      => $request->description,
            'id_customer'      => $request->id_customer,
            'job_no'           => $request->job_no,
            'id_branch'        => $request->id_branch,
            'po_no'            => $request->po_no,
            'do_no'            => $request->do_no,
            'created_at'       => date('Y-m-d H:i:s'),
            'created_by'       => Auth::user()->username,
            'mode_edit'        => 'N',
            'status'           => 2
        ]);

        Session::flash('success', 'Data has been saved successfully..');

        return redirect('crossDock/outbound/showJob/' . Crypt::encryptString($this->showJobMe()->id));
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
                            'serial_id'     => $value->serial_id,
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
                        'store_address' => $request->store_address,
                        'carrier_name' => $request->carrier_name,
                        'etd'          => $request->etd,
                        'vehicle_type' => $request->vehicle,
                        'vehicle_size' => $request->size,
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
                'store_address' => $request->store_address,
                'carrier_name' => $request->carrier_name,
                'etd'          => $request->etd,
                'vehicle_no'   => $request->vehicle_no,
                // 'vehicle_type' => $request->vehicle,
                // 'vehicle_size' => $request->size,
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
                        'status' => 0,
                        'confirmed_flag' => 'confirmed',
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

    public function submitLoading(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                DB::table('cross_outbound_header')
                    ->where('id', $request->id_header)
                    ->update([
                        'shipment_arrival_date' => $request->shipment_arrival_date,
                        'loading_start' => $request->unloading_start,
                        'loading_finish' => $request->unloading_finish,
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

    private function AddToTransaction($array)
    {
        foreach ($array as $key => $value) {
            //add to stock
            DB::table('cross_stock_transaction')
                ->insert([
                    'job_no'     => $value->stock->job_no,
                    'id_branch'     => $value->stock->id_branch,
                    'id_warehouse'     => $value->stock->id_warehouse,
                    'id_outbound'   => $value->id_header,
                    'location_code' => $value->stock->location_code,
                    'description'    => $value->stock->description,
                    'unit'          => $value->stock->unit,
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

    public function sendPermintaanEdit($id)
    {
        $header = DB::table('cross_outbound_header')
            ->where('id', $id)
            ->first();
        $user       = DB::table('users')->where('username', $header->created_by)->first();
        $principal  = DB::table('cross_mt_customer')->where('id', $header->id_customer)->first();
        $branch     = DB::table('mt_branch')->where('id', $header->id_branch)->first();

        $email = DB::table('cross_mt_email')
            ->where('status', 1)
            ->where('kategori', 'approval')
            ->where('id_branch', $header->id_branch)
            ->get();

        $to = [];
        $cc = [];
        $bcc = [];
        foreach ($email as $key => $value) {
            $to[] = explode(";", $value->to);
            $cc[] = explode(";", $value->cc);
            $bcc[] = explode(";", $value->bcc);
        }
        $to = $to[0];
        $cc = $cc[0];
        $bcc = $bcc[0];
        DB::table('cross_outbound_header')
            ->where('id', $id)
            ->update([
                'request_updated' => 'Y',
            ]);


        $data = [
            'header'    => $header,
            'user'      => $user,
            'principal' => $principal,
            'branch' => $branch,
        ];

        Mail::to($to)
            ->cc($cc)
            ->bcc($bcc)
            ->send(new PermintaanEditInboundEmail($data));

        Session::flash('success', 'Permintaan Berhasil Di kirim ke IT Helpdesk..');
        return back();
    }

    public function listEditInbound()
    {
        $data = DB::table('cross_outbound_header')->where('request_updated', 'Y')->get();

        $data->map(function ($value, $key) {
            $value->principal = DB::table('cross_mt_customer')->where('id', $value->id_customer)->first()->name;
            $value->pengaju   = DB::table('users')->where('username', $value->created_by)->first()->name;
        });

        return view('inbound.list_edit_inbound', compact('data'));
    }

    public function confirmEditInbound(Request $request)
    {
        // dd($request->all());
        if ($request->aksi == 0) {
            DB::table('cross_outbound_header')
                ->where('id', $request->id_header)
                ->update([
                    'confirmed_flag' => 'N',
                    'request_updated' => 'N',
                    'mode_edit' => 'Y'
                ]);

            DB::table('cross_outbound_detail')
                ->where('id_header', $request->id_header)
                ->update([
                    'confirmed_at' => null,
                ]);
        } else {
            DB::table('cross_outbound_header')
                ->where('id', $request->id_header)
                ->update([
                    'request_updated' => 'N',
                    'mode_edit' => 'N'
                ]);
        }
        $header  = DB::table('cross_outbound_header')
            ->join('users', 'users.username', '=', 'cross_outbound_header.created_by')
            ->where('cross_outbound_header.id', $request->id_header)
            ->first();
        $principal  = DB::table('cross_mt_customer')->where('id', $header->id_customer)->first();
        $branch     = DB::table('mt_branch')->where('id', $header->id_branch)->first();

        $email = DB::table('cross_mt_email')
            ->where('status', 1)
            ->where('kategori', 'approval')
            ->where('id_branch', $header->id_branch)
            ->get();

        $to = $header->email;
        $cc = [];
        $bcc = [];
        foreach ($email as $key => $value) {
            $cc[] = explode(";", $value->cc);
            $bcc[] = explode(";", $value->bcc);
        }
        $cc = $cc[0];
        $bcc = $bcc[0];
        $data =
            [
                'responder' => Auth::user()->name,
                'header' => $header,
                'reason' => isset($request->reason) ? $request->reason : 'oke',
                'principal' => $principal,
                'branch' => $branch
            ];

        Mail::to($to)
            ->cc($cc)
            ->bcc($bcc)
            ->send(new KonfirmasiEditInbound($data));

        Session::flash('success', 'Permintaan Berhasil di konfirmasi..');
        return back();
    }

    private function joinTableCargo()
    {
        $data = DB::table('cross_outbound_header as header')
            ->join('cross_outbound_detail as detail', 'detail.id_header', '=', 'header.id')
            ->get();
        // dd($data);
        $data->map(function ($value) {
            $value->stock = DB::table('cross_stock_ledger')->where('id', $value->id_stock)->first();
            $value->id_cargo = $value->stock->id_cargo ?? 0;
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
        $groupBy = $data->groupBy('id_cargo');
        foreach ($groupBy as $key => $value) {
            foreach ($value->where('id_cargo', $key) as $k => $v) {
                $w[$key][] = $v->qty * $v->stock->w;
                $cbm[$key][] = $v->qty * $v->stock->cbm_per_unit;
            }
            $qty[$key] =  array_sum($data->where('id_cargo', $key)->pluck('qty')->toArray());
            $w_sum[$key][] =  array_sum($w[$key]);
            $cbm_sum[$key][] =  array_sum($cbm[$key]);
        }

        if ($type == 'picking') {
            $tittle = 'Picking Report ';
        } elseif ($type == 'despatch') {
            $tittle = '';
        } elseif ($type == 'scan') {
            $tittle = 'Scan Checker Report';
        }

        return view('new.CrossDock.Report.Outbound.' . $type, compact('data', 'groupBy', 'tittle', 'customer', 'despatch', 'warehouse', 'v_despatch', 'w_sum', 'cbm_sum'));
    }
}
