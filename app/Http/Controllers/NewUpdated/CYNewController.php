<?php

namespace App\Http\Controllers\NewUpdated;

use App\Exports\CYNew\StockCY;
use App\Exports\CYNew\TransactionCY;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use App\Exports\ScanCargoExport;
use Illuminate\Support\Str;
use ZipArchive;

class CYNewController extends Controller
{
    public function getListBongkar($start, $end, $status)
    {
        $data = DB::table('cy_new_bongkar')
            ->where('branch_id', $this->myBranch())
            ->whereBetween('date', [$start . ' 00:00:00', $end . ' 00:00:00'])
            ->where('confirmed_flag', $status)
            ->orderBy('id', 'DESC')
            ->get();

        return datatables()->of($data)
            ->editColumn('date', function ($data) {
                $date = Carbon::parse($data->date)->format('d-M-Y H:i');
                return $date;
            })
            ->rawColumns(["date"])
            ->addIndexColumn()
            ->make(true);
    }

    public function getListMuat($start, $end, $status)
    {
        $data = DB::table('cy_new_muat')
            ->where('branch_id', $this->myBranch())
            ->whereBetween('date', [$start . ' 00:00:00', $end . ' 00:00:00'])
            ->where('confirmed_flag', $status)
            ->orderBy('id', 'DESC')
            ->get();

        $data = $data->map(function ($value) {
            $value->master = $this->objectBongkar($value->id_bongkar);
            return $value;
        });
        // dd($data);
        return datatables()->of($data)->addIndexColumn()->make(true);
    }

    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');
        return $data;
    }

    private function getJobNoBongkar()
    {
        $branch = $this->myBranch();
        $job = DB::table('cy_new_bongkar')
            ->where('branch_id', $branch)
            ->whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->count();

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = $job + 1;
        }
        $job_no =  'B' . $branch . date('Y') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');

        return $job_no;
    }

    private function getJobNoMuat()
    {
        $branch = $this->myBranch();
        $job = DB::table('cy_new_muat')
            ->where('branch_id', $branch)
            ->whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->count();

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = $job + 1;
        }
        $job_no =  'M' . $branch . date('Y') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');

        return $job_no;
    }

    public function index()
    {
        $start = Carbon::now()->format('Y-m-01');
        $end = Carbon::now()->format('Y-m-t');
        $size = $this->getSize();
        $cust = $this->getCustomer();
        $principal = $this->getPrincipal();
        $container_type = $this->getContainerType();
        return view("new.cy-new.index", compact('start', 'end', 'size', 'cust', 'container_type', 'principal'));
    }

    private function getSize()
    {
        $data = DB::table('iv_container_size')
            ->orderBy('size_name', 'ASC')
            ->whereIn('size_name', ['20 Feet', '40 Feet', '40 Highcube', '45 Feet'])
            ->get();
        return $data;
    }

    private function getContainerType()
    {
        $data = DB::table('iv_container_type')
            ->orderBy('type_name', 'ASC')
            ->whereIn('type_name', ['Dry', 'Open Top', 'Flat Rack', 'Reefer', 'ISO Tank'])
            ->get();
        return $data;
    }

    private function getCustomer()
    {
        $data = DB::table('cy_new_customer')->get();
        return $data;
    }

    private function getPrincipal()
    {
        $data = DB::table('cy_new_principal')->get();
        return $data;
    }

    public function storeBongkar(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                if (isset($request->add_customer)) {
                    $this->insertCustomer($request->add_customer);
                }
                if (isset($request->add_principal)) {
                    $this->insertPrincipal($request->add_principal);
                }
                if (isset($request->id_update)) {
                    $barcode =  DB::table('cy_new_bongkar')->where('id', $request->id_update)->value('barcode');
                    DB::table('cy_new_bongkar')->where('id', $request->id_update)->update([
                        'container_type' => Str::Upper($request->container_type),
                        'date'          => $request->date_bongkar,
                        'customer'      => isset($request->add_customer) ? Str::Upper($request->add_customer) : Str::Upper($request->customer),
                        'cargo_owner'      => isset($request->add_principal) ? Str::Upper($request->add_principal) : Str::Upper($request->cargo_owner),
                        'container_no'  => Str::Upper($request->container_no),
                        'seal_no'       => Str::Upper($request->seal_no),
                        'size'          => Str::Upper($request->size),
                        'stock_flag'    => 'No',
                    ]);
                } else {
                    $barcode = Str::random(10) . '-' . rand(1, 999) . date('y-m-d');
                    DB::table('cy_new_bongkar')->insert([
                        'branch_id'     => $this->myBranch(),
                        'job_no'        => $this->getJobNoBongkar(),
                        'barcode'       => $barcode,
                        'cargo_owner'   => Str::Upper($request->cargo_owner),
                        'container_type' => Str::Upper($request->container_type),
                        'date'          => $request->date_bongkar,
                        'seal_no'       => Str::Upper($request->seal_no),
                        'customer'      => isset($request->add_customer) ? Str::Upper($request->add_customer) : Str::Upper($request->customer),
                        'cargo_owner'      => isset($request->add_principal) ? Str::Upper($request->add_principal) : Str::Upper($request->cargo_owner),
                        'container_no'  => Str::Upper($request->container_no),
                        'size'          => Str::Upper($request->size),
                        'created_by'   => Auth::user()->username,
                        'created_at' => date('Y-m-d H:i:s'),
                        'stock_flag'    => 'No',
                    ]);
                }
                DB::commit();
                $data = [
                    'barcode' => $barcode,
                ];

                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function showBongkar($barcode)
    {
        $exception = DB::transaction(function () use ($barcode) {
            try {
                $data = $this->detailJobBongkar($barcode);
                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function showMuat($barcode)
    {
        $exception = DB::transaction(function () use ($barcode) {
            try {
                $data = $this->detailJobMuat($barcode);
                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function deleteBongkar($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                DB::table('cy_new_bongkar')->where('id', $id)->delete();
                DB::commit();
                $message = ['success'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function deleteMuat($id, $id_bongkar)
    {
        $exception = DB::transaction(function () use ($id, $id_bongkar) {
            try {
                $this->updateFlagStock('Yes', $id_bongkar);

                DB::table('cy_new_muat')->where('id', $id)->delete();

                DB::commit();
                $message = ['success'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function truckNumberBongkar($truck_number, $id)
    {
        $exception = DB::transaction(function () use ($truck_number, $id) {
            try {
                DB::table('cy_new_bongkar')
                    ->where('id', $id)
                    ->update(
                        ['truck_number' => Str::Upper($truck_number)]
                    );
                DB::commit();
                $message = ['success'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function truckNumberMuat($truck_number, $id)
    {
        $exception = DB::transaction(function () use ($truck_number, $id) {
            try {
                DB::table('cy_new_muat')
                    ->where('id', $id)
                    ->update(
                        ['truck_number' => Str::Upper($truck_number)]
                    );
                DB::commit();
                $message = ['success'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function gateInBongkar($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                DB::table('cy_new_bongkar')
                    ->where('id', $id)
                    ->update(
                        [
                            'gate_in' => date('Y-m-d H:i:s'),
                            'gate_in_by' => Auth::user()->username,
                        ]
                    );
                DB::commit();
                $message = ['success'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function addTransaction($id_bongkar, $condition, $remarks, $job_type,  $id_muat = null)
    {
        DB::beginTransaction();
        try {
            $bongkar = DB::table('cy_new_bongkar')
                ->where('id', $id_bongkar)
                ->first();

            $muat = DB::table('cy_new_muat')
                ->where('id', $id_muat)
                ->first();

            DB::table('cy_new_transaction')
                ->insert(
                    [
                        'branch_id'     => $bongkar->branch_id,
                        'barcode'       => !is_null($id_muat) ? $muat->barcode : $bongkar->barcode,
                        'job_type'      => $job_type,
                        'job_no'        => !is_null($id_muat) ? $muat->job_no : $bongkar->job_no,
                        'cargo_owner'   => Str::Upper($bongkar->cargo_owner),
                        'container_type' => Str::Upper($bongkar->container_type),
                        'date'          => !is_null($id_muat) ? $muat->date : $bongkar->date,
                        'seal_no'       => Str::Upper($bongkar->seal_no),
                        'customer'      => Str::Upper($bongkar->customer),
                        'cargo_owner'   => Str::Upper($bongkar->cargo_owner),
                        'container_no'  => Str::Upper($bongkar->container_no),
                        'size'          => Str::Upper($bongkar->size),
                        'container_receipt' => $bongkar->container_receipt,
                        'container_loading' => !is_null($id_muat) ? $muat->container_loading : null,
                        'gate_in'       => !is_null($id_muat) ? $muat->gate_in : $bongkar->gate_in,
                        'gate_in_by'    =>  !is_null($id_muat) ? $muat->gate_in_by : $bongkar->gate_in_by,
                        'gate_out'      => !is_null($id_muat) ? date('Y-m-d H:i:s') : $bongkar->gate_out,
                        'gate_out_by'   => !is_null($id_muat) ? Auth::user()->username : $bongkar->gate_out_by,
                        'yardman'       => !is_null($id_muat) ? $muat->container_loading_by : $bongkar->container_receipt_by,
                        'created_by'    => Auth::user()->username,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'condition'     => Str::Upper($condition),
                        'remarks'       => Str::Upper($remarks)
                    ]
                );

            DB::commit();
            return 'ok';
        } catch (\Exception $e) {
            DB::rollback();
            $message = ["error" => $e->getMessage()];
            return $message;
        }
    }

    public function gateOutBongkar(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $validate = DB::table('cy_new_bongkar')
                    ->where('id', $request->id_bongkar)
                    ->where('yardman_flag', 'Yes')
                    ->count();
                if ($validate == 0) {
                    DB::rollBack();
                    $message = 'foto_not_found';
                } else {
                    DB::table('cy_new_bongkar')
                        ->where('id', $request->id_bongkar)
                        ->update(
                            [
                                'gate_out'          => date('Y-m-d H:i:s'),
                                'gate_out_by'       => Auth::user()->username,
                                'condition'         => Str::Upper($request->condition),
                                'remarks'           => isset($request->remarks) ? Str::Upper($request->remarks) : Str::Upper($request->condition),
                                'confirmed_flag'    => 'Confirmed',
                                'stock_flag'        => 'Yes'
                            ]
                        );
                    $condition =  Str::Upper($request->condition);
                    $remarks  = isset($request->remarks) ? Str::Upper($request->remarks) : Str::Upper($request->condition);
                    $this->addTransaction($request->id_bongkar, $condition, $remarks, 'bongkar');
                    DB::commit();
                    $message = 'success';
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

    public function gateOutMuat(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $validate = DB::table('cy_new_muat')
                    ->where('id', $request->id_muat)
                    ->where('yardman_flag', 'Yes')
                    ->first();
                if (is_null($validate)) {
                    DB::rollBack();
                    $message = 'foto_not_found';
                } else {
                    DB::table('cy_new_muat')
                        ->where('id', $request->id_muat)
                        ->update(
                            [
                                'gate_out'          => date('Y-m-d H:i:s'),
                                'gate_out_by'       => Auth::user()->username,
                                'condition'         => Str::Upper($request->condition),
                                'remarks'           => isset($request->remarks) ? Str::Upper($request->remarks) : Str::Upper($request->condition),
                                'confirmed_flag'    => 'Confirmed',
                            ]
                        );
                    $this->updateFlagStock('Out', $validate->id_bongkar);
                    $condition =  Str::Upper($request->condition);
                    $remarks  = isset($request->remarks) ? Str::Upper($request->remarks) : Str::Upper($request->condition);
                    $this->addTransaction($validate->id_bongkar, $condition, $remarks, 'muat', $request->id_muat);
                    DB::commit();
                    $message = 'success';
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

    public function storeMuat(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $id_bongkar = DB::table('cy_new_bongkar')
                    ->where('container_no', $request->container_no)
                    ->where('stock_flag', 'Yes')
                    ->value('id');
                $barcode = Str::random(10) . '-' . rand(1, 999) . date('y-m-d');
                DB::table('cy_new_muat')->insert([
                    'branch_id'     => $this->myBranch(),
                    'job_no'        => $this->getJobNoMuat(),
                    'id_bongkar'    => $id_bongkar,
                    'barcode'       => $barcode,
                    'date'          => $request->date_muat,
                    'destination'   => Str::Upper($request->destination),
                    'created_by'   => Auth::user()->username,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $this->updateFlagStock('Book', $id_bongkar);
                DB::commit();
                $data = [
                    'barcode' => $barcode,
                ];

                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function updateFlagStock($flag, $id)
    {
        DB::table('cy_new_bongkar')
            ->where('id', $id)
            ->update([
                'stock_flag' => $flag
            ]);
    }

    private function insertCustomer($params)
    {
        DB::table('cy_new_customer')->insert([
            'branch_id' => $this->myBranch(),
            'customer_name' => $params,
            'created_by'   => Auth::user()->username,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function insertPrincipal($params)
    {
        DB::table('cy_new_principal')->insert([
            'branch_id' => $this->myBranch(),
            'principal_name' => $params,
            'created_by'   => Auth::user()->username,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function printBongkar($barcode, $kitirFlag = null)
    {
        $data = $this->detailJobBongkar($barcode);
        return view("new.cy-new.barcode_bongkar", compact('data'));
    }

    public function printMuat($barcode)
    {
        $data = $this->detailJobMuat($barcode)->first();
        return view("new.cy-new.barcode_muat", compact('data'));
    }

    public function detailJobBongkar($barcode)
    {
        $data = DB::table('cy_new_bongkar')
            ->where('barcode', $barcode)
            ->first();
        return $data;
    }

    public function detailJobMuat($barcode)
    {
        $data = DB::table('cy_new_muat')
            ->where('barcode', $barcode)
            ->get();
        $data = $data->map(function ($value) {
            $value->master = $this->objectBongkar($value->id_bongkar);
            return $value;
        });
        return $data;
    }

    public function gateInMuat($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                DB::table('cy_new_muat')
                    ->where('id', $id)
                    ->update(
                        [
                            'gate_in' => date('Y-m-d H:i:s'),
                            'gate_in_by' => Auth::user()->username,
                        ]
                    );
                DB::commit();
                $message = ['success'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function objectBongkar($id)
    {
        $data = DB::table("cy_new_bongkar")
            ->where('id', $id)
            ->first();
        return $data;
    }

    public function validasiMuat($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $data = $this->objectBongkar($id);
                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getStock(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = DB::table('cy_new_bongkar')
                ->where('container_no', 'LIKE', "%{$query}%")
                ->where('stock_flag', 'Yes')
                ->get();
            $output = '<ul class="dropdown-menu" style="display:block; position:relative; width: 100%;">';
            foreach ($data as $row) {
                $output .= '<li><a href="#" style="font-size: 20px; margin-left: 10px;" class="text-dark mt-3 mb-3"><b>' . $row->container_no . '</b></a></li><hr>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }


    public function downloadExcel(Request $request)
    {
        return Excel::download(new StockCY($request->cargo_owner, $request->container_no), "MKT-CY-STOCK_" . date('d-m-Y') . ".xlsx");
    }

    public function downloadTransaction(Request $request)
    {
        return Excel::download(new TransactionCY($request->job_type, $request->cargo_owner, $request->container_no, $request->start, $request->end), "MKT-CY-TRANSACTION_" . $request->start . 'sd' . ' ' . $request->end  . ".xlsx");
    }

    public function searchImages(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $data = DB::table('cy_new_transaction')
                    ->whereBetween('date', [$request->start . ' 00:00:00', $request->end . ' 00:00:00'])
                    ->where('job_type', $request->job_type)
                    ->get();
                return $data;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function downloadFoto($barcode, $type, $container_no)
    {
        $data = DB::table('cy_new_' . $type . '_pictures')
            ->where('barcode', $barcode)
            ->get();
        $zip = new ZipArchive;
        $zipFileName = 'Foto-' . Str::upper($type) . '-' . Str::upper($container_no) . '.zip';
        if ($zip->open(public_path($zipFileName), ZipArchive::CREATE) === TRUE) {
            $file = [];
            foreach ($data as $key => $value) {
                $file[] = public_path('foto/cy-new/' . $type . '/' . $value->filename);
            }
            foreach ($file as $val) {
                $zip->addFile($val, basename($val));
            }
            $zip->close();

            return response()->download(public_path($zipFileName))->deleteFileAfterSend(true);
        } else {
            return "Failed to create the zip file.";
        }
    }
}
