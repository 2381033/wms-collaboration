<?php

namespace App\Http\Controllers\NewUpdated\CrossdockNew\Inbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InboundImports;
use Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\PermintaanEditInboundEmail;
use App\Mail\KonfirmasiEditInbound;
use Illuminate\Support\Facades\Crypt;


class InboundController extends Controller
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

    private function getLocation()
    {
        $location = DB::table('cross_mt_location')
            ->whereIn('id_branch', $this->myBranch())
            ->get();

        return $location;
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
        $job = DB::table('cross_inbound_header')
            ->whereIn('id_branch', $this->myBranch())
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->get();

        $substr = $job->max('job_no') ?? 0;
        if ($job->count() > 0) {
            $increment = substr($substr, 6, 3) + $job->count() > 0 ? $job->count() + 1 : 0 + 1;
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
        $vehicle        = $this->getVehicle();
        $vehicleSize    = $this->getVehicleSize();
        $branch         = $this->getBranch();
        $now            = date('Y-m-d');

        return view('new.CrossDock.Inbound.index', compact('job_no', 'now', 'customer', 'warehouse', 'vehicleSize', 'vehicle', 'branch'));
    }

    private function getCargoList($id)
    {
        $data = DB::table('cross_inbound_detail')
            ->where('id_header', $id)
            ->get();

        return $data;
    }

    private function getHeader($id)
    {
        $data = DB::table('cross_inbound_header')
            ->where('id', $id)
            ->first();

        return $data;
    }

    private function getUom()
    {
        $data = DB::table('rt_uom')
            ->orderBy('code', 'ASC')
            ->get();

        return $data;
    }

    public function showJob($id)
    {
        $id = Crypt::decryptString($id);

        $header         = $this->getHeader($id);
        $customer       = $this->getCustomer();
        $warehouse      = $this->getWarehouse();
        $vehicle        = $this->getVehicle();
        $vehicleSize    = $this->getVehicleSize();
        $branch         = $this->getBranch();
        $cargo          = $this->getCargoList($id);
        $location       = $this->getLocation();
        $uom            = $this->getUom();
        $batch          = $this->getListMappingan()->whereIn('id_detail', $cargo->pluck('id')->toArray());
        $batch->map(function ($value) {
            $value->detail = DB::table('cross_inbound_detail')
                ->where('id', $value->id_detail)
                ->first();
        });

        $menu_header    = false;
        $menu_cargo     = false;
        $menu_mapping   = false;
        $menu_putaway   = false;
        $menu_confirm   = false;

        if ($header->status == 1) {
            $menu_header = true;
        } elseif ($header->status == 2) {
            $menu_cargo = true;
        } elseif ($header->status == 3) {
            $menu_mapping = true;
        } elseif ($header->status == 4) {
            $menu_putaway = true;
        } else {
            $menu_confirm = true;
        }

        return view('new.CrossDock.Inbound.show', compact('customer', 'warehouse', 'vehicleSize', 'vehicle', 'branch', 'header', 'cargo', 'menu_header', 'menu_cargo', 'menu_confirm', 'menu_mapping', 'menu_putaway', 'batch', 'location', 'uom'));
    }

    public function showJobFrontend($id)
    {
        return redirect('crossDock/inbound/showJob/' . Crypt::encryptString($id));
    }

    public function showJobMe()
    {
        $data = DB::table('cross_inbound_header')
            ->where('created_by', Auth::user()->username)
            ->orderBy('id', 'DESC')
            ->first();

        return $data;
    }

    public function getListMappingan()
    {
        $data = DB::table('cross_inbound_batch')->get();

        return $data;
    }

    public function storeHeader(Request $request)
    {
        $request->validate([
            'id_warehouse' => 'required',
            'container_number' => 'required',
            'vehicle_number' => 'required',
            'vehicle' => 'required',
            'size' => 'required',
            'driver_name' => 'required',
            'remarks' => 'required',
            'id_customer' => 'required',
            'transporter_name' => 'required',
            'job_no' => 'required',
            'id_branch' => 'required',
        ]);

        DB::table('cross_inbound_header')->insert([
            'id_warehouse'     => $request->id_warehouse,
            'container_number' => $request->container_number,
            'vehicle_number'   => $request->vehicle_number,
            'vehicle'          => $request->vehicle,
            'size'             => $request->size,
            'driver_name'      => $request->driver_name,
            'remarks'          => $request->remarks,
            'id_customer'      => $request->id_customer,
            'transporter_name' => $request->transporter_name,
            'job_no'           => $request->job_no,
            'id_branch'        => $request->id_branch,
            'created_at'       => date('Y-m-d H:i:s'),
            'created_by'       => Auth::user()->username,
            'mode_edit'        => 'N',
            'status'           => 2
        ]);

        Session::flash('success', 'Data has been saved successfully..');

        return redirect('crossDock/inbound/showJob/' . Crypt::encryptString($this->showJobMe()->id));
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'excel' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('excel');
        Excel::import(new InboundImports($request->id_header), $file);

        return back();
    }

    public function storeCargo(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                // dd($request->all());
                $validate_db = DB::table('cross_inbound_detail')
                    ->where('id_cargo', $request->cargo_id)
                    ->where('id_header', $request->id_header)
                    ->count();
                if ($validate_db > 0) {
                    DB::rollBack();
                    $message = ['message' => 'duplicate'];
                    return $message;
                } else {
                    $cbm_per_unit = $request->p * $request->l * $request->t / 1000000;

                    DB::table('cross_inbound_detail')
                        ->insert([
                            'id_header'     => $request->id_header,
                            'unit'          => $request->uom,
                            'description'   => $request->description,
                            'id_cargo'      => $request->cargo_id,
                            'p'             => $request->p,
                            'l'             => $request->l,
                            't'             => $request->t,
                            'w'             => $request->w,
                            'cbm_per_unit'  => $cbm_per_unit,
                            'qty'           => $request->qty,
                            'cbm_total'     => $cbm_per_unit * $request->qty,
                            'date_in'       => date('Y-m-d'),
                            'created_at'    => date('Y-m-d H:i:s'),
                            'created_by'    => Auth::user()->username,
                            'status'        => 2
                        ]);
                    DB::commit();

                    $message = ['message' => 'Data Successfully Saved'];

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

    public function deleteDetail($id)
    {
        DB::table('cross_inbound_detail')
            ->where('id', $id)
            ->delete();

        Session::flash('success', 'Data Berhasil Di Hapus..');
        return back();
    }

    public function editCargo($id)
    {
        $data = DB::table('cross_inbound_detail')
            ->where('id', $id)
            ->first();

        return response()->json($data);
    }

    public function updateCargo(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $validate_db = DB::table('cross_inbound_detail')
                    ->where('id_cargo', $request->id_cargo)
                    ->where('id_header', $request->id_header)
                    ->where('id', '!=', $request->id_detail)
                    ->count();
                if ($validate_db > 0) {
                    DB::rollBack();
                    $message = ['message' => 'duplicate'];
                    return $message;
                } else {
                    DB::table('cross_inbound_detail')
                        ->where('id', $request->id_detail)
                        ->update([
                            'id_cargo'      => $request->id_cargo,
                            'description'   => $request->description,
                            'p'             => $request->p,
                            'l'             => $request->l,
                            't'             => $request->t,
                            'w'             => $request->w,
                            'qty'           => $request->qty,
                        ]);
                    DB::commit();

                    $message = ['success' => 'Data Successfully Saved'];
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

    public function deleteCargo($id)
    {
        DB::table('cross_inbound_detail')
            ->where('id', $id)
            ->delete();

        Session::flash('success', 'Data Successfully Deleted');
        return back();
    }

    public function getMappingPallet($id)
    {
        $data = DB::table('cross_inbound_detail')
            ->where('id', $id)
            ->first();

        return response()->json($data);
    }

    public function postMappingPallet(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $validate = $this->getCargoList($request->id_header)
                    ->where('id', $request->id_detail)
                    ->first();
                $qty_mapping = array_sum($request->qty);

                if ($qty_mapping > $validate->qty or $qty_mapping < $validate->qty) {
                    DB::rollBack();
                    $message = ['limit' => 'Error request you have exceeded qty'];

                    return $message;
                } else {
                    //hapus dulu
                    DB::table('cross_inbound_batch')
                        ->where('id_detail', $request->id_detail)
                        ->delete();

                    $counting     = DB::table('cross_inbound_batch')
                        ->whereDate('created_at', date('Y-m-d'))
                        ->count();

                    $sku =  $counting == 0 ? 0 + 1 : $counting + 1;

                    for ($i = 0; $i < count($request->qty); $i++) {
                        $generate = date('dmy') . rand(100, 999) . $sku;

                        DB::table('cross_inbound_batch')
                            ->insert([
                                'qrcode'        => Str::random(30),
                                'pallet_ke'     => $i + 1,
                                'sku'           => $generate,
                                'serial_id'     => rand(100, 999) . date('dmy'),
                                'id_detail'     => $request->id_detail,
                                'qty_pallet'    => $request->qty[$i],
                                'created_at'    => date('Y-m-d H:i:s'),
                                'created_by'    => Auth::user()->username,
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

    public function postPutaway(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $header = $this->getHeader($request->id_header);

                for ($i = 0; $i < count($request->id); $i++) {
                    $location = DB::table('cross_mt_location')
                        ->where('id_branch', $header->id_branch)
                        ->where('id_warehouse', $header->id_warehouse)
                        ->where('location_code', $request->location_code[$i])
                        ->first();

                    if (!$location) {
                        DB::table('cross_mt_location')
                            ->insert([
                                'id_branch' => $header->id_branch,
                                'id_warehouse' => $header->id_warehouse,
                                'location_code' => $request->location_code[$i],
                                'location_name' => $request->location_code[$i],
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => Auth::user()->username,
                            ]);
                    }

                    DB::table('cross_inbound_batch')
                        ->where('id', $request->id[$i])
                        ->update([
                            'location_code' => $request->location_code[$i],
                        ]);
                }

                DB::table('cross_inbound_header')
                    ->where('id', $request->id_header)
                    ->update([
                        'status' => 5
                    ]);

                DB::table('cross_inbound_detail')
                    ->where('id_header', $request->id_header)
                    ->update([
                        'status' => 5
                    ]);

                DB::table('cross_inbound_batch')
                    ->whereIn('id', $request->id)
                    ->update([
                        'status' => 5
                    ]);

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

    public function confirm($type, $id)
    {
        $exception = DB::transaction(function () use ($type, $id) {
            try {
                $data = $this->getCargoList($id);
                $id_detail = $data->pluck('id')->toArray();
                $data = $this->joinTableCargo()->whereIn('id_detail', $id_detail);

                $status = 0;
                if ($type == 'cargo') {
                    $status = 3;
                } elseif ($type == 'mapping') {
                    $status = 4;
                } else {
                    $this->addToStockAndTransaction($data);

                    DB::table('cross_inbound_header')
                        ->where('id', $id)
                        ->update([
                            'confirmed_flag' => 'confirmed',
                            'mode_edit'      => 'N'
                        ]);

                    $status = 0;
                }

                DB::table('cross_inbound_header')
                    ->where('id', $id)
                    ->update([
                        'status' => $status
                    ]);

                DB::table('cross_inbound_detail')
                    ->where('id_header', $id)
                    ->update([
                        'status' => $status
                    ]);

                DB::table('cross_inbound_batch')
                    ->whereIn('id_detail', $id_detail)
                    ->update([
                        'status' => $status
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

    private function addToStockAndTransaction($array)
    {
        foreach ($array as $key => $value) {
            //add to stock
            DB::table('cross_stock_ledger')
                ->insert([
                    'serial_id'     => $value->serial_id,
                    'job_no'        => $value->job_no,
                    'id_branch'     => $value->id_branch,
                    'unit'          => $value->unit,
                    'id_warehouse'  => $value->id_warehouse,
                    'id_customer'   => $value->id_customer,
                    'id_inbound'    => $value->id_header,
                    'on_hand'       => $value->qty_pallet,
                    'on_actual'     => $value->qty_pallet,
                    'description'   => $value->description,
                    'id_cargo'      => $value->id_cargo,
                    'sku'           => $value->sku,
                    'location_code' => $value->location_code,
                    'p'             => $value->p,
                    'l'             => $value->l,
                    't'             => $value->t,
                    'w'             => $value->w,
                    'cbm_per_unit'  => $value->cbm_per_unit,
                    'cbm_total'     => $value->cbm_total,
                    'qrcode'        => $value->qrcode,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'created_by'    => Auth::user()->username
                ]);

            DB::table('cross_stock_transaction')
                ->insert([
                    'serial_id'     => $value->serial_id,
                    'job_no'        => $value->job_no,
                    'id_warehouse'  => $value->id_warehouse,
                    'id_branch'     => $value->id_branch,
                    'id_inbound'    => $value->id_header,
                    'type_job'      => 'in',
                    'qty'           => $value->qty_pallet,
                    'location_code' => $value->location_code,
                    'id_cargo'      => $value->id_cargo,
                    'sku'           => $value->sku,
                    'p'             => $value->p,
                    'l'             => $value->l,
                    't'             => $value->t,
                    'w'             => $value->w,
                    'unit'          => $value->unit,
                    'cbm_per_unit'  => $value->cbm_per_unit,
                    'cbm_total'     => $value->cbm_total,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->username
                ]);
        }
    }

    private function getStockStorage($id_warehouse)
    {
        $data = DB::table('cross_stock_storage')
            ->where('id_warehouse', $id_warehouse)
            ->orderBy('id', 'DESC')
            ->value('stock');

        return $data;
    }
    private function addToStorage($array)
    {
        foreach ($array as $value) {
            $stock = $this->getStockStorage($value->id_warehouse);
            $cbm_start = $value->p * $value->l * $value->t * $value->qty_pallet / 1000000;
            DB::table('cross_stock_storage')
                ->insert([
                    'id_branch'     => $value->id_branch,
                    'id_warehouse'  => $value->id_warehouse,
                    'id_customer'   => $value->id_customer,
                    'inbound'       => $cbm_start,
                    'qty'           => $value->qty_pallet,
                    'p'             => $value->p,
                    'l'             => $value->l,
                    't'             => $value->t,
                    'w'             => $value->w,
                    'stock'         => $stock == null ? $cbm_start : $stock + $cbm_start,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'created_by'    => Auth::user()->username
                ]);
        }
    }

    public function sendPermintaanEdit($id)
    {
        $header = DB::table('cross_inbound_header')
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
        DB::table('cross_inbound_header')
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
        $data = DB::table('cross_inbound_header')->where('request_updated', 'Y')->get();

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
            DB::table('cross_inbound_header')
                ->where('id', $request->id_header)
                ->update([
                    'confirmed_flag' => 'N',
                    'request_updated' => 'N',
                    'mode_edit' => 'Y'
                ]);

            DB::table('cross_inbound_detail')
                ->where('id_header', $request->id_header)
                ->update([
                    'confirmed_at' => null,
                ]);
        } else {
            DB::table('cross_inbound_header')
                ->where('id', $request->id_header)
                ->update([
                    'request_updated' => 'N',
                    'mode_edit' => 'N'
                ]);
        }
        $header  = DB::table('cross_inbound_header')
            ->join('users', 'users.username', '=', 'cross_inbound_header.created_by')
            ->where('cross_inbound_header.id', $request->id_header)
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

    public function mappingPalletImport(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
        $file = $request->file('file');
        Excel::import(new InboundImports($request->id_cargo), $file);
        return back();
    }

    private function joinTableCargo()
    {
        $data = DB::table('cross_inbound_header as header')
            ->select(
                'header.*',
                'detail.*',
                'batch.qrcode',
                'batch.pallet_ke',
                'batch.id_detail',
                'batch.qty_pallet',
                'batch.location_code',
                'batch.sku',
                'batch.serial_id',
            )
            ->join('cross_inbound_detail as detail', 'detail.id_header', '=', 'header.id')
            ->join('cross_inbound_batch as batch', 'detail.id', '=', 'batch.id_detail')
            ->get();

        return $data;
    }

    private function whereCustomer($id)
    {
        $data = DB::table('cross_mt_customer')->where('id', $id)->value('name');
        return $data;
    }

    private function whereWarehouse($id)
    {
        $data = DB::table('cross_mt_warehouse')->where('id', $id)->value('name');
        return $data;
    }

    public function report($type, $id)
    {
        $data      = $this->joinTableCargo()->where('id_header', $id);
        $customer  = $this->whereCustomer($data->first()->id_customer) ?? '-';
        $warehouse  = $this->whereWarehouse($data->first()->id_warehouse) ?? '-';

        $groupBy = $data->groupBy('id');
        $tittle = '';
        if ($type == 'mapping-detail') {
            $tittle = 'Good Receipt Report (Detail) ';
        } elseif ($type == 'mapping-summary') {
            $tittle = 'Good Receipt Report (Summary) ';
            $data = $this->getCargoList($id);
        } elseif ($type == 'pallet-tag') {
            $tittle = 'Pallet Tag Report';
        } elseif ($type == 'putaway') {
            $tittle = 'Putaway Report';
        } elseif ($type == 'icr-detail') {
            $tittle = 'Inbound Confirmation Report (Detail)';
        } else {
            $tittle = 'Inbound Confirmation Report (Summary)';
            $data = $this->getCargoList($id);
            $data->map(function ($value) {
                $value->batch = DB::table('cross_inbound_batch')->where('id_detail', $value->id)->first();
            });
        }
        return view('new.CrossDock.Report.Inbound.' . $type, compact('data', 'groupBy', 'tittle', 'customer', 'warehouse'));
    }

    public function submitUnloading(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {

                DB::table('cross_inbound_header')
                    ->where('id', $request->id_header)
                    ->update([
                        'shipment_arrival_date' => $request->shipment_arrival_date,
                        'unloading_start'       => $request->unloading_start,
                        'unloading_finish'      => $request->unloading_finish,
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
}
