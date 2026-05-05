<?php

namespace App\Http\Controllers\NewUpdated\Export\OB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class JobController extends Controller
{

    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');
        return $data;
    }

    private function getJobNo()
    {
        $job = DB::table('ex_ob_header')
            ->where('branch_id', $this->myBranch())
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->get();

        $substr = $job->max('job_no') ?? 0;
        if ($job->count() > 0) {
            $increment = substr($substr, 6, 3) + $job->count() > 0 ? $job->count() + 1 : 0 + 1;
        } else {
            $increment = 1;
        }

        $job_no = 'OB' . date('ym') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');
        return $job_no;
    }

    public function index()
    {
        return view('new.OBExport.index');
    }

    public function searchData($startDate, $endDate, $statusJob)
    {
        $data = DB::table('ex_ob_header')
            ->whereBetween(\DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->where('confirmed_flag', $statusJob)
            ->get();

        return datatables()->of($data)->make(true);
    }

    public function create()
    {
        $mobil = DB::table('ex_gate_in_cargo')
            ->whereDate('created_at', Carbon::today())
            ->get();
        return view('new.OBExport.create', compact('mobil'));
    }

    public function autocompletePeb(Request $request)
    {
        $term = $request->get('term');

        $data = DB::table('ex_stock_ledger')
            ->select('peb_no')
            ->where('branch_id', $this->myBranch())
            ->where('status_flag', 'Inbound')
            ->whereNotNull('peb_no')
            ->whereNotNull('location_code')
            ->where('peb_no', 'like', "%{$term}%")
            ->groupBy('peb_no')
            ->limit(10)
            ->get();

        $results = [];
        foreach ($data as $row) {
            $results[] = ['label' => $row->peb_no, 'value' => $row->peb_no];
        }

        return response()->json($results);
    }

    public function autocompleteAju(Request $request)
    {
        $term = $request->get('term');

        $data = DB::table('ex_stock_ledger')
            ->select('aju_no')
            ->where('branch_id', $this->myBranch())
            ->where('status_flag', 'Inbound')
            ->whereNotNull('aju_no')
            ->whereNotNull('location_code')
            ->where('aju_no', 'like', "%{$term}%")
            ->limit(10)
            ->groupBy('aju_no')
            ->get();

        $results = [];
        foreach ($data as $row) {
            $results[] = ['label' => $row->aju_no, 'value' => $row->aju_no];
        }

        return response()->json($results);
    }

    public function getDetail(Request $request)
    {
        $peb = $request->get('peb');
        $aju = $request->get('aju');

        $query = DB::table('ex_stock_ledger as a')
            ->leftJoin('mt_forwarder as b', 'a.forwarder_id', '=', 'b.id')
            ->leftJoin('mt_shipper as c', 'a.shipper_id', '=', 'c.id')
            ->where('a.status_flag', 'Inbound')
            ->where('a.branch_id', $this->myBranch());

        if ($peb) {
            $query->where('a.peb_no', $peb);
        }

        if ($aju) {
            $query->where('a.aju_no', $aju);
        }

        $data = $query
            ->select(
                'b.forwarder_name',
                'c.shipper_name',
                'total_pallet',
                // 'vgm',
                DB::raw('SUM(a.qty_cargo) as total_qty')
            )
            ->groupBy('b.forwarder_name', 'c.shipper_name')
            ->first();

        if (!$data) {
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => true,
            'forwarder' => $data->forwarder_name,
            'shipper' => $data->shipper_name,
            'qty' => $data->total_qty,
            'total_pallet' => $data->total_pallet,
        ]);
    }

    public function show($id)
    {
        $header = DB::table('ex_ob_header as a')
            ->select('a.*', 'b.forwarder_name', 'c.shipper_name')
            ->join('mt_forwarder as b', 'a.forwarder_id', '=', 'b.id')
            ->join('mt_shipper as c', 'a.shipper_id', '=', 'c.id')
            ->where('a.id', $id)
            ->first();
        $detail = DB::table('ex_ob_detail')
            ->where('job_no', $header->job_no)
            ->get();
        $auth_group_id = DB::table('auth_group')->get();
        $users = DB::table('users as a')
            ->join('sm_user_branch as b', 'a.id', '=', 'b.user_id')
            ->where('b.branch_id', $this->myBranch())
            ->where('a.active', 'Yes')
            ->get();
        $checker = $auth_group_id->where('name', 'Checker Export')->first()->id;
        $checker =  $users->where("auth_group_id", $checker);

        $stapel = $auth_group_id->where('name', 'Stapel')->first()->id;
        $stapel = $users->where("auth_group_id", $stapel);
        return view('new.OBExport.show', compact('header', 'detail', 'checker', 'stapel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'peb_number'   => 'required',
            'aju_number'   => 'required',
            'no_mobil'     => 'required',
            'pic'          => 'required',
            'destination'  => 'required',
            'remarks'      => 'required',
        ]);

        try {
            DB::beginTransaction();
            $ledgers = DB::table('ex_stock_ledger')
                ->where('status_flag', 'Inbound')
                ->where(function ($q) use ($request) {
                    if (!empty($request->peb_number)) {
                        $q->orWhere('peb_no', $request->peb_number);
                    }
                    if (!empty($request->aju_number)) {
                        $q->orWhere('aju_no', $request->aju_number);
                    }
                })
                ->get();

            if ($ledgers->isEmpty()) {
                return back()->with('error', 'Tidak ada data ledger Inbound untuk PEB/AJU tersebut.');
            }
            $first = $ledgers->first();
            $job_no = $this->getJobNo();

            DB::table('ex_ob_header')->insert([
                'branch_id'    => $this->myBranch(),
                'job_no'        => $job_no,
                'job_date'      => date('Y-m-d'),
                'peb_no'        => $first->peb_no,
                'aju_no'        => $first->aju_no,
                'po_number'     => $first->po_number,
                'forwarder_id'  => $first->forwarder_id,
                'consignee_id'  => $first->consignee_id,
                'shipper_id'    => $first->shipper_id,
                'vehicle_no'    => $request->no_mobil,
                'pic_name'      => $request->pic,
                'destination'   => $request->destination,
                'remarks'       => $request->remarks,
                'created_by'    => auth()->user()->username,
                'created_at'    => now(),
            ]);

            $details = [];

            foreach ($ledgers as $row) {
                $details[] = [
                    'branch_id'      => $row->branch_id,
                    'job_no'         => $job_no,
                    'job_date'       => date('Y-m-d'),
                    'vehicle_no'     => $request->no_mobil,
                    'forwarder_id'   => $row->forwarder_id,
                    'consignee_id'   => $row->consignee_id,
                    'shipper_id'     => $row->shipper_id,
                    'destination'    => $request->destination,
                    'peb_no'         => $row->peb_no,
                    'aju_no'         => $row->aju_no,
                    'pic_name'       => $request->pic,
                    'qty_cargo'      => $row->qty_cargo,
                    'cbm'            => $row->cbm,
                    'weight'         => $row->weight,
                    'total_pallet'   => $row->total_pallet,
                    'serial_no'      => $row->serial_no,
                    'location_id'    => $row->location_id,
                    'location_code'  => $row->location_code,
                    'pallet_id'      => $row->pallet_id,
                    'quantity'       => $row->quantity,
                    'user_id'        => auth()->user()->username,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ];
            }
            DB::table('ex_ob_detail')->insert($details);
            DB::commit();
            $id = DB::table('ex_ob_header')
                ->where('created_by', auth()->user()->username)
                ->orderBy('id', 'DESC')
                ->value('id');
            return redirect('/export/ob/show/' . $id)->with('success', 'Header dan detail berhasil disimpan');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('storeHeader error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function chooseStapel($job_id, $username)
    {
        try {
            DB::beginTransaction();
            DB::table('ex_ob_header')
                ->where('id', $job_id)
                ->update([
                    'stapel_name' => $username,
                ]);
            DB::commit();
            return back()->with('success', 'Data Stapel Berhasil Dipilih.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('storeHeader error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
    public function chooseChecker($job_id, $username)
    {
        try {
            DB::beginTransaction();
            DB::table('ex_ob_header')
                ->where('id', $job_id)
                ->update([
                    'checker_name' => $username,
                ]);
            DB::commit();
            return back()->with('success', 'Data Checker Berhasil Dipilih.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('storeHeader error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function showImages($job_no)
    {
        $exception = DB::transaction(function () use ($job_no) {
            try {
                $data = DB::table('ex_ob_image')
                    ->select('file', 'id')
                    ->where('job_no', $job_no)
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
                $data = DB::table('ex_ob_image')
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

    public function confirmationJob($job_no)
    {
        DB::beginTransaction();
        try {
            $image = DB::table('ex_ob_image')
                ->select('file')
                ->where('job_no', $job_no)
                ->count();
            if ($image <= 4) {
                return back()->with('error', 'Foto cargo belum memenuhi syarat..');
            } else {
                $this->updateLedger($job_no);
                $this->addToTransaction($job_no);
                DB::table('ex_ob_header')
                    ->where('job_no', $job_no)
                    ->update([
                        'confirmed_flag' => 'Confirmed',
                        'updated_at' => now(),
                        'updated_by' => auth()->user()->username,
                    ]);
                DB::commit();
                return back()->with('success', 'Job berhasil dikonfirmasi.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            $message = ["error" => $e->getMessage()];
            return $message;
        }
        return back();
    }

    public function backtoChecker($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                DB::table("ex_ob_header")
                    ->where("id", $id)
                    ->update([
                        'checker_confirmed_at' => null,
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

    private function updateLedger($job_no)
    {
        $data = DB::table('ex_ob_detail')
            ->where('job_no', $job_no)
            ->get();

        foreach ($data as $value) {
            DB::table('ex_stock_ledger')
                ->where('branch_id', $value->branch_id)
                ->where('serial_no', $value->serial_no)
                ->update([
                    'status_flag' => 'Book',
                ]);
        }
    }
    private function addToTransaction($job_no)
    {
        $data = DB::table('ex_ob_detail')
            ->where('job_no', $job_no)
            ->get();

        foreach ($data as $value) {
            $transactionRows[] = [
                'job_type' => 'out',
                'branch_id' => $value->branch_id,
                'job_no' => $value->job_no,
                'po_number' => explode('-', $value->serial_no)[0] ?? null,
                'vehicle_no' => $value->vehicle_no,
                'forwarder_id' => $value->forwarder_id,
                'shipper_id' => $value->shipper_id,
                'consignee_id' => $value->consignee_id,
                'destination' => $value->destination ?? null,
                'peb_no' => $value->peb_no,
                'aju_no' => $value->aju_no ?? null,
                'serial_no' => $value->serial_no,
                'pallet_id' => $value->pallet_id,
                'quantity' => $value->quantity,
                'cbm' => $value->cbm ?? 0,
                'weight' => $value->weight ?? 0,
                'total_pallet' => $value->total_pallet ?? 0,
                'qty_cargo' => $value->qty_cargo,
                'user_id' => $value->user_id,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }
        if (!empty($transactionRows)) {
            DB::table('ex_stock_transaction')->insert($transactionRows);
        }
    }

    public function tally_sheet($id)
    {
        $header = DB::table("ex_ob_header as a")
            ->select('a.*', "b.forwarder_name", "c.consignee_name", "d.shipper_name")
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->join("mt_consignee as c", "a.consignee_id", "c.id")
            ->join("mt_shipper as d", "a.shipper_id", "d.id")
            ->where("a.id", $id)
            ->first();

        $foto_cargo = DB::table('ex_ob_image')
            ->select('file')
            ->where('job_no', $header->job_no)
            ->get();
        $detail = DB::table("ex_ob_detail as a")
            ->where('job_no', $header->job_no)
            ->get();
        $qtyTotal = $detail->sum('quantity');

        $cbm_total = [];
        $vgm_total = [];
        foreach ($detail as $key => $value) {
            //     $cbm_total[] = $value->sum('cbm');;
            //     $vgm_total[] = $value->sum('weight');
            $value->serial_no_formatted = str_replace('/', '-', $value->serial_no);
            //     $cbm[] = $value->cbm;
        }
        $cbm_total = number_format($detail->sum('cbm'), 3, '.', '');
        $weight_total = number_format($detail->sum('weight'), 3, '.', '');
        $vgm_total = $detail->sum('vgm');
        $data = [
            "header" => $header,
            "detail" => $detail,
            'cbm_total' => $cbm_total,
            'vgm_total' => $vgm_total,
            'foto_cargo' => $foto_cargo,
            'qtyTotal' => $qtyTotal,
        ];
        return view("new.OBExport.tally_sheet", compact('data'));
    }
}
