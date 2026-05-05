<?php

namespace App\Http\Controllers\NewUpdated;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Session;
use Illuminate\Support\Carbon;
use App\Exports\CheckpointDriverExport;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;
use ZipArchive;
use Illuminate\Support\Arr;

class MonitoringCheckpointController extends Controller
{
    public function index()
    {
        $onProgress = DB::table('cp_driver_job')
            ->where('confirmed_flag', 'No')
            ->where('status_job', '!=', 'deleted')
            ->where('confirmed_flag', 'No')
            ->where('branch_id', $this->myBranch())
            ->count();
        $today = \Carbon\Carbon::now(); //Current Date and Time
        $start =    \Carbon\Carbon::parse($today)->firstOfMonth()->toDateString();
        $end =    \Carbon\Carbon::parse($today)->endOfMonth()->toDateString();
        $mobil = $this->getTypeArmada();

        return view("new.MonitoringCheckpoint.dashboard", compact('onProgress', 'start', 'end', 'mobil'));
    }

    public function updateDisplay()
    {
        $allJob = DB::table('cp_driver_job')
            ->where('confirmed_flag', 'No')
            ->where('status_job', '!=', 'deleted')
            ->where('branch_id', $this->myBranch())
            ->get();
        // dd($allJob);
        $onProgress = $allJob->where('confirmed_flag', 'No')->count();

        return response()->json([
            'allJob' => $allJob,
            'onProgress' => $onProgress
        ]);
    }

    public function getDisplay($token)
    {
        $detail = DB::table('cp_driver_detail')
            ->orderBy('id', 'ASC')
            ->where('token', $token)
            ->get();

        $header = DB::table('cp_driver_job')
            ->where('token', $token)
            ->first();

        $driver = $this->detailUser($header->driver);
        $header->start_job =  Carbon::parse($header->created_at)->format('H:i');
        $header->f_start_back_to_garage = Carbon::parse($header->start_back_to_garage)->format('H:i');
        $header->f_finish_back_to_garage = Carbon::parse($header->finish_back_to_garage)->format('H:i');

        $detail = $detail->map(function ($value) use ($header) {
            $value->f_to_loc_muat = Carbon::parse($header->start_from_garage)->format('H:i');
            $value->f_gatein_loc_muat = Carbon::parse($value->gate_in_loc_muat)->format('H:i');
            $value->f_gateout_loc_muat = Carbon::parse($value->gate_out_loc_muat)->format('H:i');
            $value->f_gatein_loc_bongkar = Carbon::parse($value->gate_in_loc_bongkar)->format('H:i');
            $value->f_gateout_loc_bongkar = Carbon::parse($value->gate_out_loc_bongkar)->format('H:i');
            return $value;
        });

        return response()->json([
            'header' => $header,
            'driver' => $driver,
            'detail' => $detail
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new CheckpointDriverExport($request->startDate, $request->endDate, $request->no_mobil),  "Checkpoint-Driver-Report-" . $request->no_mobil . ".xlsx");
    }

    public function planner()
    {
        $armada = $this->getTypeArmada();
        $driver = $this->getDriver();
        $job = $this->getJob();
        $job->map(function ($value) {
            $value->driver_name = $this->getDriver()->where('id', $value->driver)->first() ?? '-';
            $value->detail = $this->detailJob($value->token);
            return $value;
        });
        return view("new.MonitoringCheckpoint.planner", compact('armada', 'driver', 'job'));
    }

    private function detailJob($token)
    {
        $data = DB::table('cp_driver_detail')
            ->where('token', $token)
            ->get();
        return $data;
    }

    private function getTypeArmada()
    {
        $data = DB::table('cp_driver_armada')
            ->orderBy('armada', 'asc')
            ->where('branch_id', $this->myBranch())
            ->where('active', 'Yes')
            ->get();
        return $data;
    }

    private function getDriver()
    {
        $userID         = DB::table('sm_user_branch')
            ->where('branch_id', $this->myBranch())
            ->get()->pluck('user_id')->toArray();
        $authID = DB::table('auth_group')
            ->where('name', 'Driver')
            ->value('id');
        $data = DB::table('users')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->where('active', 'Yes')
            ->where('auth_group_id', $authID)
            ->whereIn('id', $userID)
            ->get();
        return $data;
    }

    private function getJob()
    {
        $data = DB::table('cp_driver_job')
            ->whereDate('created_at', date('Y-m-d'))
            ->where('branch_id', $this->myBranch())
            ->get();
        return $data;
    }

    private function whereJob($token)
    {
        $data = DB::table('cp_driver_job')
            ->where('token', $token)
            ->get();
        return $data;
    }

    public function submitPlanner(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $validator = Validator::make($request->all(), [
                    'no_order' => 'required',
                    'no_mobil' => 'required',
                    'nama_customer' => 'required',
                    'driver' => 'required',
                    'lokasi_muat' => 'required',
                    'lokasi_bongkar' => 'required',
                    'jenis_armada' => 'required',
                    'revenue' => 'required',
                    'cost' => 'required',
                ]);
                if ($validator->fails()) {
                    $message = [
                        'message' => 'required',
                    ];
                } else if (is_null($request->lokasi_muat) || is_null($request->lokasi_bongkar)) {
                    $message = [
                        'message' => 'required',
                    ];
                } else {
                    $job_no = $this->getJobNo();
                    $lokasi_muat = explode(",", $request->lokasi_muat);
                    $lokasi_bongkar = explode(",", $request->lokasi_bongkar);
                    $token = Str::random(5) . date("dmyhis");

                    DB::table('cp_driver_job')
                        ->insert([
                            'token' => $token,
                            'job_no' => $job_no,
                            'no_order' => Str::upper($request->no_order),
                            'jenis_armada' => Str::upper($request->jenis_armada),
                            'nama_customer' =>  Str::upper($request->nama_customer),
                            'no_mobil' => Str::upper($request->no_mobil),
                            'branch_id' => $this->myBranch(),
                            'driver' => $request->driver,
                            'created_at' => $this->logicWaktu(),
                            'created_by' => Auth::user()->username,
                            'user_id' => Auth::user()->id,
                            'jumlah_loc_muat' => count($lokasi_muat),
                            'jumlah_loc_bongkar' => count($lokasi_bongkar),
                            'status_job' => 'on_garage'
                        ]);

                    DB::table('cp_driver_revenue_cost')
                        ->insert([
                            'token' => $token,
                            'job_no' => $job_no,
                            'revenue' => intval(str_replace('.', '', $request->revenue)),
                            'cost' => intval(str_replace('.', '', $request->cost)),
                            'status' => 1
                        ]);

                    foreach ($lokasi_muat as $val) {
                        DB::table('cp_driver_detail')
                            ->insert([
                                'token' => $token,
                                'job_no' => $job_no,
                                'lokasi_muat' => Str::upper($val),
                            ]);
                    }
                    foreach ($lokasi_bongkar as $value) {
                        DB::table('cp_driver_detail')
                            ->insert([
                                'token' => $token,
                                'job_no' => $job_no,
                                'lokasi_bongkar' => Str::upper($value),
                            ]);
                    }
                    DB::commit();

                    $message = [
                        'message' => 'Data Successfully Saved',
                    ];
                }
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }


    private function getJobNo()
    {
        $job = DB::table('cp_driver_job')
            ->where('branch_id', $this->myBranch())
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();
        $increment  = $job + 1;
        $job_no = 'D' . date('ymd')  . '00' .  $increment;
        return $job_no;
    }

    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');

        return $data;
    }
    private function logicWaktu()
    {
        $branch = $this->myBranch();

        if ($branch == 5) {
            return date('Y-m-d H:i:s', strtotime('+1 hour'));
        } else {
            return date('Y-m-d H:i:s');
        }
    }

    private function detailUser($id_user)
    {
        $data = DB::table('users')
            ->where('id', $id_user)
            ->first();
        return $data;
    }

    public function searchJenisArmada($no_mobil)
    {
        $data = $this->getTypeArmada()->where('no_mobil', $no_mobil)->first();
        return response()->json(['data' => $data]);
    }

    public function deleteJob($token)
    {
        $exception = DB::transaction(function () use ($token) {
            try {
                DB::table('cp_driver_job')
                    ->where('token', $token)
                    ->update([
                        'created_at' => '1970-01-01 00:00:00',
                        'status_job' => 'deleted',
                    ]);

                DB::table('cp_driver_detail')
                    ->where('token', $token)
                    ->delete();
                DB::commit();
                $message = [
                    'message' => 'success',
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

    public function databasePerjalanan()
    {
        return view("new.MonitoringCheckpoint.database.perjalanan");
    }

    public function getListDatabasePerjalanan($start, $end, $status)
    {
        $data = DB::table('cp_driver_job')
            ->whereBetween(\DB::raw('DATE(created_at)'), [$start, $end])
            ->where('branch_id', $this->myBranch())
            ->where('confirmed_flag', $status)
            ->get();
        $data->map(function ($value) {
            $value->revenue = number_format($this->getRevenueCost($value->token)->first()->revenue, 0, ",", ".");
            $value->cost = number_format($this->getRevenueCost($value->token)->first()->cost, 0, ",", ".");
        });

        return datatables()->of($data)->make(true);
    }

    private function getRevenueCost($token)
    {
        $data = DB::table('cp_driver_revenue_cost')
            ->orderBy('id', 'DESC')
            ->where('token', $token)
            ->get();
        return $data;
    }

    public function historyPerjalanan($token)
    {
        $header = $this->whereJob($token)->first();
        $detail = $this->detailJob($token);
        $revenue = $this->getRevenueCost($token);
        return view("new.MonitoringCheckpoint.database.historyPerjalanan", compact('header', 'detail', 'revenue'));
    }

    public function detailRevenueCost($token)
    {
        $data = $this->getRevenueCost($token);
        $data->map(function ($value) {
            $value->formatRevenue = number_format($value->revenue, 0, ",", ".");
            $value->formatCost = number_format($value->cost, 0, ",", ".");
        });
        $header = $this->whereJob($token)->first();
        return response()->json([
            'data' => $data,
            'header' => $header,
        ]);
    }

    public function submitAdditionalRevenueCost(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job_no = $this->whereJob($request->token)->first();
                DB::table('cp_driver_revenue_cost')
                    ->insert([
                        'job_no' => $job_no->job_no,
                        'revenue' => intval(str_replace('.', '', $request->revenue)),
                        'cost' => intval(str_replace('.', '', $request->cost)),
                        'token' => $request->token,
                        'remarks' => $request->remarks,
                    ]);

                DB::commit();
                $message = [
                    'message' => 'success',
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

    public function downloadFotoPerjalanan($token)
    {
        $header = $this->whereJob($token)->first();
        $detail = $this->detailJob($token);
        $zip = new ZipArchive;
        $zipFileName = 'Foto No Order-' . $header->no_order . '-.zip';
        if ($zip->open(public_path($zipFileName), ZipArchive::CREATE) === TRUE) {
            $file_gate_in_loc_muat = [];
            $file_gate_out_loc_muat = [];
            $file_gate_in_loc_bongkar = [];
            $file_gate_out_loc_bongkar = [];
            foreach ($detail->whereNotNull('lokasi_muat') as $key => $value) {
                $file_gate_in_loc_muat[] = public_path('foto/checkpoint-driver/lokasi-muat/' . $value->file_gate_in_loc_muat);
                $file_gate_out_loc_muat[] = public_path('foto/checkpoint-driver/lokasi-muat/' . $value->file_gate_out_loc_muat);
            }
            foreach ($detail->whereNotNull('lokasi_bongkar') as $key => $value) {
                $file_gate_in_loc_bongkar[] =  public_path('foto/checkpoint-driver/lokasi-bongkar/' . $value->file_gate_in_loc_bongkar);
                $file_gate_out_loc_bongkar[] = public_path('foto/checkpoint-driver/lokasi-bongkar/' . $value->file_gate_out_loc_bongkar);
            }

            $filesToZip = [
                public_path('foto/checkpoint-driver/lokasi-garage/' . $header->foto_km),
                public_path('foto/checkpoint-driver/lokasi-garage/' . $header->foto_km_finish),
                public_path('foto/checkpoint-driver/surat-jalan/' . $header->file_surat_jalan),
            ];
            $filezip = Arr::collapse([$filesToZip, $file_gate_in_loc_muat, $file_gate_out_loc_muat, $file_gate_in_loc_bongkar, $file_gate_out_loc_bongkar]);
            //dump foto zip jika gagal
            foreach ($filezip as $file) {
                $dump[] = is_file($file) ? 'true' : 'false' . '-->' . basename($file);
            }
            foreach ($filezip as $file) {
                $zip->addFile($file, basename($file));
            }

            $zip->close();

            return response()->download(public_path($zipFileName))->deleteFileAfterSend(true);
        } else {
            return "Failed to create the zip file.";
        }
    }

    public function updateOrderNo(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                DB::table('cp_driver_job')
                    ->where('token', $request->token)
                    ->update([
                        'no_order' => $request->no_order,
                    ]);

                DB::commit();
                $message = [
                    'message' => 'success',
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
}
