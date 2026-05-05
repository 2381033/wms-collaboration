<?php

namespace App\Http\Controllers\NewUpdated\Export\BeaCukai;

use App\Exports\inboundPackingExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

use App\Models\Transaction\Inbound\Detail as inboundDetails;
use App\Models\Transaction\Inbound\Job as inboundJob;
use App\Models\Transaction\Export\InboundHeader as ExportInboundHeader;

class BeaCukaiController extends Controller
{
    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');
        return $data;
    }

    public function index()
    {
        $uom = DB::table('rt_uom')->where('active', 'Yes')->get();
        $negara = DB::table('mt_country')->get();
        return view("new.Export.BeaCukai.index", compact('uom', 'negara'));
    }

    public function getPEB(Request $request)
    {
        if ($request->has('q')) {
            $cari = $request->q;
            $data = DB::table('ex_inbound_header')
                ->select('id', 'peb_no', 'shipper_id')
                ->where('branch_id', $this->myBranch())
                ->where('peb_no', 'LIKE', '%' . $cari . '%')
                ->whereYear('created_at', '>', 2022)
                ->get();
            $data = $data->map(function ($value) {
                $value->shipper_name = DB::table('mt_shipper')
                    ->select('shipper_name')
                    ->where('branch_id', $this->myBranch())
                    ->where('id', $value->shipper_id)
                    ->value('shipper_name');
                return $value;
            });
            return response()->json($data);
        }
    }

    public function getShipper(Request $request)
    {
        if ($request->has('q')) {
            $cari = $request->q;
            $data = DB::table('mt_shipper')
                ->select('shipper_name')
                ->where('branch_id', $this->myBranch())
                ->where('shipper_name', 'LIKE', '%' . $cari . '%')
                ->get();
            return response()->json($data);
        }
    }

    public function DetailPEB($id)
    {
        $input_flag = false;
        $data_bc = DB::table("ex_bea_cukai")
            ->where('id_header', $id)
            ->first();
        $jobHeader = DB::table("ex_inbound_header as a")
            ->select('a.*', "b.forwarder_name", "c.consignee_name", "d.shipper_name")
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->join("mt_consignee as c", "a.consignee_id", "c.id")
            ->join("mt_shipper as d", "a.shipper_id", "d.id")
            ->where("a.id", $id)
            ->first();
        if (is_null($data_bc)) {
            $input_flag = true;
        } else {
            $input_flag = false;
        }

        return response()->json([
            'data' => [
                'data_bc' => $data_bc,
                'jobHeader' => $jobHeader,
                'input_flag' => $input_flag
            ]
        ]);
    }

    public function detailBC($id)
    {
        $data = DB::table("ex_bea_cukai_detail")
            ->where('id_header_bc', $id)
            ->get();
        $data = $data->map(function ($value) {
            $value->header = DB::table("ex_bea_cukai")
                ->where('id', $value->id_header_bc)
                ->first();
            return $value;
        });
        return response()->json([
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $rules = array(
            'peb_no' => 'required',
            'peb_date' => 'required',
            'npe_no' => 'required',
            'npe_date' => 'required',
            'pkbe_no' => 'required',
            'pkbe_date' => 'required',
            'valuta' => 'required',
            'negara_tujuan' => 'required',
            'eksportir' => 'required',
            'asal_barang' => 'required',
        );
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' => 'validate']);
        }
        $exception = DB::transaction(function () use ($request) {
            try {
                $header = $this->getHeader($request->id_header);
                DB::table('ex_bea_cukai')->insert([
                    'branch_id' => $this->myBranch(),
                    'id_header' => $header->id,
                    'peb_no' => $header->peb_no,
                    'peb_date' => $request->peb_date,
                    'npe_no' => $request->npe_no,
                    'npe_date' => $request->npe_date,
                    'pkbe_no' => $request->pkbe_no,
                    'pkbe_date' => $request->pkbe_date,
                    'eksportir' => Str::Upper($request->eksportir),
                    'qty_receiving' => $request->qty_receiving,
                    'asal_barang' => Str::Upper($request->asal_barang),
                    'forwarder_id' => Str::Upper($header->forwarder_id),
                    'receiving_date' => Str::Upper($header->created_at),
                    'valuta' => Str::Upper($request->valuta),
                    'negara_tujuan' => Str::Upper($request->negara_tujuan),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->username,
                ]);
                DB::commit();
                $message = ['message' => 'success'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });

        return response()->json($exception);
    }

    public function updateBC(Request $request)
    {
        $rules = array(
            'peb_no' => 'required',
            'peb_date' => 'required',
            'npe_no' => 'required',
            'npe_date' => 'required',
            'pkbe_no' => 'required',
            'pkbe_date' => 'required',
            'valuta' => 'required',
            'negara_tujuan' => 'required',
            'eksportir' => 'required',
            'asal_barang' => 'required',
        );
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' => 'validate']);
        }
        $exception = DB::transaction(function () use ($request) {
            try {
                DB::table('ex_bea_cukai')
                    ->where('id', $request->id)
                    ->update([
                        'peb_date' => $request->peb_date,
                        'npe_no' => $request->npe_no,
                        'npe_date' => $request->npe_date,
                        'pkbe_no' => $request->pkbe_no,
                        'pkbe_date' => $request->pkbe_date,
                        'eksportir' => Str::Upper($request->eksportir),
                        'qty_receiving' => $request->qty_receiving,
                        'valuta' => Str::Upper($request->valuta),
                        'asal_barang' => Str::Upper($request->asal_barang),
                        'negara_tujuan' => Str::Upper($request->negara_tujuan),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->username,
                    ]);
                DB::commit();
                $message = ['message' => 'success'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });

        return response()->json($exception);
    }


    private function getHeader($id)
    {
          $data = DB::table("ex_inbound_header")
                ->where('id', $id)
                ->first();
          return $data;
    }
    
    public function storeDetail(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $id_header = DB::table('ex_bea_cukai')
                    ->where('id', $request->id_header_bc)
                    ->value('id_header');
                $receiving = DB::table('ex_inbound_header')
                    ->where('id', $id_header)
                    ->value('qty_actual');

                $jumlah_kemasan = array_sum($request->jumlah_kemasan);
                    DB::table('ex_bea_cukai_detail')
                        ->where('id_header_bc', $request->id_header_bc)
                        ->delete();
                    for ($i = 0; $i < count($request->jumlah_jenis_barang); $i++) {
                        DB::table('ex_bea_cukai_detail')->insert([
                            'id_header_bc' => $request->id_header_bc,
                            'jumlah_jenis_barang' => $request->jumlah_jenis_barang[$i],
                            'satuan_jenis_barang' => Str::Upper($request->satuan_jenis_barang[$i]),
                            'jumlah_kemasan' => $request->jumlah_kemasan[$i],
                            'satuan_kemasan' => Str::Upper($request->satuan_kemasan[$i]),
                            'nilai_barang' => $request->nilai_barang[$i],
                            'no_peti_kemas' => Str::Upper($request->no_peti_kemas[$i]),
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->username,
                        ]);
                    }
                    DB::commit();
                    $message = ['message' => 'success'];
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
        $exception = DB::transaction(function () use ($id) {
            try {
                $id_header = DB::table("ex_bea_cukai_detail")
                    ->select('id_header_bc')
                    ->where('id', $id)
                    ->value('id_header_bc');

                DB::table('ex_bea_cukai_detail')->where('id', $id)->delete();
                DB::commit();
                $data = DB::table("ex_bea_cukai_detail")
                    ->where('id_header_bc', $id_header)
                    ->get();
                $data = $data->map(function ($value) {
                    $value->header = DB::table("ex_bea_cukai")
                        ->where('id', $value->id_header_bc)
                        ->first();
                    return $value;
                });
                $message = ['message' => 'success', 'data' => $data];
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
