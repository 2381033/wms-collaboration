<?php

namespace App\Http\Controllers\Transaction\Inbound;


use App\Http\Controllers\Controller;
use App\Models\Transaction\Inbound\Scan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ScanCartonEmail as emailCarton;
use Session;

class ScanCtnController extends Controller
{
    public function startScan()
    {
        $shipper = DB::table('mt_shipper')
            ->whereIn('branch_id', $this->myBranch(Auth::user()->id))
            ->where('active', 'Yes')
            ->orderBy('shipper_name', 'ASC')
            ->get();
        $customer = DB::table('mt_forwarder')
            ->whereIn('branch_id', $this->myBranch(Auth::user()->id))
            ->where('active', 'Yes')
            ->orderBy('forwarder_name', 'ASC')
            ->get();

        return view('transaction.inbound.scan', compact('shipper', 'customer'));
    }

    public function OutstandView()
    {
        return view('transaction.inbound.outstanding');
    }

    public function showListOutstanding(Request $request)
    {
        $data = $this->getListOutstanding($request->start, $request->end, $request->status_code, $request->po);
        return $data;
    }

    private function getListOutstanding($start, $end, $status, $po)
    {
        $query = DB::table('ex_scan_carton')
            ->whereBetween('scan_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->where('confirmed_flag', $status)
            ->get();

        if (!is_null($po)) {
            $query = $query->where('po_number', $po);
        }

        $actual = [];
        $data   = [];
        foreach ($query->groupBy('po_number') as $key => $value) {
            $actual[$key] = $value->count();
            $data[] = [
                'shipper' => $value[0]->shipper,
                'customer' => $value[0]->customer,
                'po_number' => $value[0]->po_number,
                'checker' => $value[0]->scan_by,
                'job_date' => $value[0]->scan_at,
                'qtyBooking' => $value[0]->qty,
                'confirmed_flag' => $value[0]->confirmed_flag,
            ];
        }
        return response()->json([
            'data' => [
                'data' => $data,
                'actual' => $actual,
            ]
        ]);
    }


    public function submit(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $request->validate([
                    'po' => 'required|string',
                    'qty' => 'required|integer',
                    'Scanctn' => 'required|string',
                ]);
                $barcodeCarton = $this->getAll($request->po)->pluck('barcode_carton')->toArray();
                if (in_array($request->Scanctn, $barcodeCarton)) {
                    $message = ["data" => 'duplicate'];
                    DB::rollBack();
                } else {
                    $master = $this->getAll($request->po);
                    if ($master->count() > 0) {
                        Scan::create([
                            'shipper' => $master[0]->shipper,
                            'customer' => $master[0]->customer,
                            'partial_flag' => $master[0]->partial_flag,
                            'po_number' => $request->input('po'),
                            'qty' => $request->input('qty'),
                            'barcode_carton' => $request->input('Scanctn'),
                            'scan_by' => Auth::user()->username,
                            'scan_at' => Carbon::now()->toDateTimeString(),
                        ]);
                    } else {
                        Scan::create([
                            'shipper' => $request->shipper,
                            'customer' =>  $request->forwarder,
                            // 'partial_flag' => $master[0]->partial_flag,
                            'po_number' => $request->input('po'),
                            'qty' => $request->input('qty'),
                            'barcode_carton' => $request->input('Scanctn'),
                            'scan_by' => Auth::user()->username,
                            'scan_at' => Carbon::now()->toDateTimeString(),
                        ]);
                    }

                    $data = $this->getAll($request->input('po'));
                    $message = ["data" => $data];
                    DB::commit();
                }

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ["error" => [$e->getMessage()]];
                return $message;
            }
        });

        return response()->json($exception);
    }

    private function getAll($po_number)
    {
        $data = DB::table('ex_scan_carton')->where('po_number', $po_number)
            ->whereDate('scan_at', date('Y-m-d'))
            ->where('scan_by', Auth::user()->username)
            ->where('confirmed_flag', 'No')
            // ->where('partial_flag', 'No')
            ->get();

        // DB::table('ex_scan_carton')
        //     ->where('po_number', $po_number)
        //     ->update([
        //         'qty' => $this->countNow($po_number),
        //     ]);

        return $data;
    }

    public function editQtyActual($po_number, $qty)
    {
        DB::table('ex_scan_carton')
            ->where('po_number', $po_number)
            ->where('confirmed_flag', 'No')
            ->where('partial_flag', 'No')
            ->update([
                'qty'   => $qty,
                'confirmed_flag'   => 'Yes',
                'confirmed_at'   => date('Y-m-d H:i:s'),
                'confirmed_by'   => Auth::user()->username,
            ]);

        return back();
    }

    public function tagPartial($po_number)
    {
        DB::transaction(function () use ($po_number) {
            try {
                $master = $this->getAll($po_number);
                $qty = $master->first()->qty;
                $row = $master->count();
                $jobDate = Carbon::parse($master->first()->scan_at ?? date('Y-m-d'))->format('d-m-Y') ?? '??ErrorDate??';

                DB::table('ex_scan_carton')->where('po_number', $po_number)
                    ->whereDate('scan_at', date('Y-m-d'))
                    ->where('scan_by', Auth::user()->username)
                    ->where('confirmed_flag', 'No')
                    ->where('partial_flag', 'No')
                    ->update([
                        'partial_flag' => 'Yes',
                    ]);
                $this->sendEmail($master->first()->shipper, $master->first()->customer, $qty, $row, Auth::user()->name, $jobDate, $master->first()->po_number, 'partial');
                DB::commit();
                Session::flash('success', 'Job has been tag a partial job..');
                return back();
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('error', $e->getMessage());
                return back();
            }
        });
        return back();
    }

    public function partial()
    {
        return view('transaction.inbound.scan_partial');
    }

    public function deleteCtn($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $scan = Scan::find($id);
                $po_number = $scan->po_number;
                $scan->delete();
                $data = $this->getAll($po_number);

                $message = ["data" => $data];
                DB::commit();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ["error" => [$e->getMessage()]];
                return $message;
            }
        });

        return response()->json($exception);
    }

    public function updateWhenFinish($po_number)
    {
        DB::transaction(function () use ($po_number) {
            try {
                $master = $this->getAll($po_number);
                $qty = $master->first()->qty;
                $row = $master->count();
                $jobDate = Carbon::parse($master->first()->scan_at ?? date('Y-m-d'))->format('d-m-Y') ?? '??ErrorDate??';
                if ($row != $qty) {
                    $this->sendEmail($master->first()->shipper, $master->first()->customer, $qty, $row, Auth::user()->name, $jobDate, $master->first()->po_number, 'normal');
                    $data =  DB::table('ex_scan_carton')->where('po_number', $po_number)
                        ->whereDate('scan_at', date('Y-m-d'))
                        ->where('scan_by', Auth::user()->username)
                        ->update([
                            'finish_flag' => 'Yes',
                            'partial_flag' => 'No',
                        ]);
                } else {
                    $data = DB::table('ex_scan_carton')->where('po_number', $po_number)
                        ->whereDate('scan_at', date('Y-m-d'))
                        ->where('scan_by', Auth::user()->username)
                        ->update([
                            'finish_flag' => 'Yes',
                            'confirmed_flag' => 'Yes',
                            'confirmed_at' => date('Y-m-d H:i:s'),
                            'confirmed_by' => Auth::user()->username,
                            'partial_flag' => 'No',
                        ]);
                }
                Session::flash('success', 'Good Joob!');
                DB::commit();
            } catch (\Exception $e) {
                Session::flash('error', $e->getMessage());
                DB::rollBack();
            }
        });
        return back();
    }

    private function myBranch($user_id)
    {
        $branch = DB::table('sm_user_branch')
            ->where('user_id', $user_id)
            ->get()->pluck('branch_id')->toArray();

        return $branch;
    }

    private function sendEmail($shipper, $customer, $booking, $actual, $checker, $jobDate, $po, $type)
    {
        $sendData = DB::table('ex_email')
            ->where("description", "Scan Carton Export")
            ->where('active', 'Yes')
            ->whereIn('branch_id', $this->myBranch(Auth::user()->id))
            ->get();
        $pluck = $sendData->pluck('customer', 'id')->toArray();
        $custArr = [];
        $idCust = 0;
        $active = false;
        foreach ($pluck as $key => $value) {
            $idCust = $key;
            $custArr[] = explode(',', $value);
        }
        if (count($custArr) > 0) {
            $filtered = array_filter($custArr[0], function ($item) use ($customer) {
                return stripos($item, $customer) !== false;
            });
            if (count($filtered) > 0) {
                $email_cc = [];
                $email_bcc = [];
                foreach ($sendData->where('id', $idCust) as $key => $value) {
                    $list_to = ($value->email_to);
                    $list_cc = explode(",", $value->email_cc);
                    $list_bcc = explode(",", $value->email_bcc);
                    for ($i = 0; $i < count($list_cc); $i++) {
                        if (!empty($list_cc[$i]) && $list_cc[$i] !== "") {
                            $email_cc[] = $list_cc[$i];
                        }
                    }
                    for ($i = 0; $i < count($list_bcc); $i++) {
                        if (!empty($list_bcc[$i]) && $list_bcc[$i] !== "") {
                            $email_bcc[] = $list_bcc[$i];
                        }
                    }
                }
                Mail::to($list_to)
                    ->cc($email_cc)
                    ->bcc($email_bcc)
                    ->send(new emailCarton($shipper, $customer, $booking, $actual, $checker, $jobDate, $po, $type));
            }
        }
    }

    public function resumeScan($po_number)
    {
        $exception = DB::transaction(function () use ($po_number) {
            try {
                $data = DB::table('ex_scan_carton')->where('po_number', $po_number)
                    ->whereDate('scan_at', date('Y-m-d'))
                    ->where('scan_by', Auth::user()->username)
                    ->where('confirmed_flag', 'No')
                    ->where('partial_flag', 'Yes')
                    ->get();
                $message = ["data" => $data];
                DB::commit();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ["error" => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getPODetails(Request $request)
    {
        $poNumber = $request->input('po_number');
        $data = DB::table('ex_scan_carton')
            ->where('po_number', $poNumber)
            ->get();
        return response()->json([
            'data' => $data
        ]);
    }

    public function outstandUpdate(Request $request)
    {
        $po_number = $request->input('po_number');
        $exception = DB::transaction(function () use ($po_number) {
            try {

                // $item = DB::table('ex_scan_carton')->where('id', $id)->first();
                // if (!$item) {
                //     return response()->json(['error' => 'Item not found'], 404);
                // }

                // Update the `confirmed_flag` and other related fields
                $data = DB::table('ex_scan_carton')->where('po_number', $po_number)
                    ->update([
                        'confirmed_flag' => 'Yes',
                        'confirmed_at' => Carbon::now()->toDateTimeString(),
                        'confirmed_by' => Auth::user()->username,
                    ]);

                $message = ["data" => $data];
                DB::commit();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ["error" => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function deleteItem($id, $po, $start, $end, $status)
    {
        $exception = DB::transaction(function () use ($id, $po, $start, $end, $status) {
            try {
                $deleted = DB::table('ex_scan_carton')
                    ->where('id', $id)
                    ->delete();
                $data = $this->getListOutstanding($start, $end, $status, $po);
                if ($deleted) {
                    DB::commit();
                    return $data;
                } else {
                    throw new \Exception('Item not found');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => $e->getMessage()], 500);
            }
        });

        return $exception;
    }

    // private function countDelete($id)
    // {
    //     return DB::table('ex_scan_carton')
    //         ->where('id', $id)
    //         ->count();
    // }

    private function countNow($poNumber)
    {
        return DB::table('ex_scan_carton')
            ->where('po_number', $poNumber)
            ->count();
    }

    public function addRow(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $barcodeCarton = $this->getAll($request->po_number)->pluck('barcode_carton')->toArray();
                if (in_array($request->carton_id, $barcodeCarton)) {
                    return response()->json([
                        'message' => 'duplicate'
                    ]);
                }
                if ($request->po_number != $request->po_number_new) {
                    return response()->json([
                        'message' => 'not_same'
                    ]);
                } else {
                    $poNumber = $request->input('po_number');
                    $cartonId = $request->input('carton_id');
                    $scanBy = $request->input('scan_by');

                    $getShipper = DB::table('ex_scan_carton')->where('po_number', $poNumber)->value('shipper');
                    $getForwarder = DB::table('ex_scan_carton')->where('po_number', $poNumber)->value('customer');
                    DB::table('ex_scan_carton')->where('po_number', $poNumber)->insert([
                        'shipper' => $getShipper,
                        'customer' => $getForwarder,
                        'po_number' => $poNumber,
                        'qty' => $this->countNow($poNumber),
                        'barcode_carton' => $cartonId,
                        'scan_by' => $scanBy,
                        'scan_at' => Carbon::now()->toDateTimeString(),
                        'finish_flag' => 'No',
                        'confirmed_flag' => 'No',
                        'confirmed_at' => null,
                        'confirmed_by' => null,
                    ]);

                    $data = $this->getListOutstanding($request->start_date, $request->end_date, $request->status, $request->po_number);
                    return $data;
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ]);
            }
        });
        return $exception;
    }
}
