<?php

namespace App\Http\Controllers\NewUpdated;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Session;
use DataTables;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use App\Exports\ScanCargoExport;


class ScanCargoEksporController extends Controller
{
    private function getHeader($job_no)
    {
        $data = DB::table('ex_scan_cargo_header')
            ->where('job_no', $job_no)
            ->where('branch_id', $this->myBranch())
            ->first();

        return $data;
    }

    private function getDetail($job_no)
    {
        $data = DB::table('ex_scan_cargo_detail')
            ->orderBy('id', 'ASC')
            ->where('job_no', $job_no)
            ->get();

        return $data;
    }

    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');
        return $data;
    }

    private function getJobNo()
    {
        $branch = $this->myBranch();

        $job = DB::table('ex_scan_cargo_header')
            ->where('branch_id', $branch)
            ->whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->count();

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = $job + 1;
        }
        $job_no =  $branch . date('Y') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');

        return $job_no;
    }

    public function index()
    {
        return view("new.ScanCargoEkspor.dashboard");
    }

    public function storeHeader(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job_no = $this->getJobNo();
                    DB::table('ex_scan_cargo_header')->insert([
                        'branch_id'  => $this->myBranch(),
                        'job_no'  => $this->getJobNo(),
                        'po_no'  => $request->po_no,
                        'qty'    => $request->qty,
                        'remarks' => $request->remarks,
                        'created_by'   => Auth::user()->username,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

                    DB::commit();
                    $message = [
                        'message' => 'Data Successfully Saved',
                        'data' => $job_no
                    ];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function detailJob($job_no){
        $job_no = Crypt::decryptString($job_no);

        return view('new.ScanCargoEkspor.Scan.index', compact('job_no'));
    }

    private function extractTime($date){
            return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('dM H:i');
    }

    public function getListJob($job_no)
    {
        $job_no = Crypt::decryptString($job_no);
        $header = $this->getHeader($job_no);
        $list = $this->getDetail($job_no);
        $lastUpdated = DB::table('ex_scan_cargo_detail')
            ->orderBy('id', 'DESC')
            ->where('job_no', $job_no)
            ->where('id_header', $header->id ?? 0)
            ->value('created_at');
            
        if(!is_null($lastUpdated)){
            $lastUpdated = $this->extractTime($lastUpdated);
        }

        $btn_confirm = false;
        if(!is_null($header->qty)){
            if($header->qty == $list->count()){
            $btn_confirm = true;
            }
        }
        else{
            $btn_confirm = false;
        }

        return response()->json([
            'data' => [
                'header'=> $header,
                'list'=> $list,
                'btn_confirm'=> $btn_confirm,
                'lastUpdated'=> $lastUpdated
            ]
        ]);
    }

    public function encryptJob($job_no){
        return redirect('export/ScanCargoEkspor/detailJob/'. Crypt::encryptString($job_no));
    }

    public function ajaxEncryptJob($job_no){
        return redirect('export/ScanCargoEkspor/getListJob/'. Crypt::encryptString($job_no));
    }

    public function validasiCargo($barcode, $job_no)
    {
        $exception = DB::transaction(function () use ($barcode, $job_no) {
            try {
                $validasi = $this->getDetail($job_no)->where('barcode', $barcode)->count();
                
                if($validasi > 0){
                    $message = ([
                        'message' => 'double',
                    ]);
                    return $message;
                }else{
                    $header = $this->getHeader($job_no);
                    $list = $this->getDetail($job_no);
                    $lastUpdated = DB::table('ex_scan_cargo_detail')
                    ->orderBy('id', 'DESC')
                    ->where('job_no', $job_no)
                    ->where('id_header', $header->id ?? 0)
                    ->value('created_at');
                    
                    if(!is_null($lastUpdated)){
                        $lastUpdated = $this->extractTime($lastUpdated);
                    }
            
                    $btn_confirm = false;
                    if(!is_null($header->qty)){
                        if($header->qty == $list->count()){
                        $btn_confirm = true;
                        }
                    }
                    else{
                        $btn_confirm = false;
                    }
                
                    $this->addCargo($barcode, $job_no);
                    DB::commit();
                    $message = ([
                        'message' => 'ok',
                        'data' => [
                            'header'=> $header,
                            'list'=> $list,
                            'btn_confirm'=> $btn_confirm,
                            'lastUpdated'=> $lastUpdated
                        ]
                    ]);
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

    private function addCargo($barcode, $job_no)
    {
        $header = $this->getHeader($job_no);
        DB::table('ex_scan_cargo_detail')
        ->insert([
            'id_header' => $header->id,
            'job_no' => $header->job_no,
            'barcode' => $barcode,
            'scan_at' => date('Y-m-d H:i:s'),
            'scan_by' => Auth::user()->username,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => Auth::user()->username,
        ]);
    }

    public function konfirmJob($job_no){
        $exception = DB::transaction(function () use ($job_no) {
            try {
                DB::table('ex_scan_cargo_header')
                ->where('job_no', $job_no)
                ->where('branch_id', $this->myBranch())
                ->update([
                    'confirmed_flag' => 'Yes',
                    'confirmed_at' => date('Y-m-d H:i:s'),
                    'confirmed_by' => Auth::user()->username,
                ]);
                    DB::commit();
                    $message = ([
                        'message' => 'ok',
                    ]);
                    return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getListJobTable($startDate, $endDate, $statusJob)
    {
        $data = DB::table('ex_scan_cargo_header')
        ->orderBy('id', 'ASC')
        ->where('branch_id', $this->myBranch())
        ->whereBetween(\DB::raw('DATE(created_at)'), [$startDate, $endDate])
        ->where('confirmed_flag', $statusJob)
        ->get();
       
        return datatables()->of($data)->make(true);
    }

    public function exportExcel($job_no){
        return Excel::download(new ScanCargoExport($job_no), $job_no. "-Report.xlsx");
    }
}
