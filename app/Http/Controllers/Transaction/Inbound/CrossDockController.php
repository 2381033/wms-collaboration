<?php

namespace App\Http\Controllers\Transaction\Inbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrossDockController extends Controller
{    
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $list_data = \App\Models\Transaction\Inbound\Batch::from('iv_inbound_batch as a')
                            ->select('a.*', 'b.product_name', "d.class_name")
                            ->join('iv_product as b', 'a.product_id', 'b.id')
                            ->join('iv_inbound_job as c', 'a.inbound_id', 'c.id')
                            ->join('iv_job_class as d', 'c.class_id', 'd.id')
                            ->where('a.company_id', $company_id)
                            ->where('a.inbound_id', $request->inbound_id)
                            ->get();

            return datatables()->of($list_data)
            ->editColumn('exp_date', function ($data) 
            {
                $exp_date = "";
                if (isset($data->exp_date)) {
                    $exp_date = date('d/m/Y', strtotime($data->exp_date) );
                }
                return $exp_date;
            })
            ->editColumn('mfg_date', function ($data) 
            {
                $mfg_date = "";
                if (isset($data->mfg_date)) {
                    $mfg_date = date('d/m/Y', strtotime($data->mfg_date) );
                }
                return $mfg_date;
            })
            ->addColumn('check', function ($data) {
                $check = "";
                if ( $data->crossdock_flag == "No" ) { 
                    $check = '<input type="checkbox" required="required" name="cross_id[]" class="cross-check" id="' . $data->id . '" value="' . $data->id . '">';
                } 
                return $check;
            })
            ->rawColumns(['check'])
            ->addIndexColumn()       
            ->make(true);
        }
    }

    public function store(Request $request) {
        $messsages = array(
            'customer_id.required'=>'Customer name field is required.',
            'order_no.required'=>'Order number field is required.',      
            'order_date.required'=>'Order date field is required.',      
            'due_date.required'=>'Due date field is required.',         
        );
    
        $rules = array(    
            'customer_id' => 'required',
            'order_no' => 'required',
            'order_date' => 'required',
            'due_date' => 'required',
        );
        
        $validator = \Validator::make($request->all(), $rules,$messsages);
        
        if ($validator->fails()) {    
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            $company_id = Auth::user()->company_id;
            $username = Auth::user()->username;
            $inbound_id = $request->inbound_id;
            $job_status = $request->job_status;
            $outbound_id = $request->outbound_id;
            $order_id = $request->order_id;
            $order_no = $request->order_no;
            $customer_id = $request->customer_id;
                    
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->order_date);
            $order_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->due_date);
            $due_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $job_date = \Carbon\Carbon::today();

            $data = $request->cross_id;

            try {
                $inbound_job = \App\Models\Transaction\Outbound\Job::find($inbound_id);

                if ( $job_status == "E" ) {
                    $job = \App\Models\Transaction\Outbound\Job::find($outbound_id);
                    $order = \App\Models\Transaction\Outbound\Order::find($order_id);

                    $job_no = $job->job_no;
                } else {
                    $job_no = $this->getJob();

                    $job = new \App\Models\Transaction\Outbound\Job();

                    $job->company_id = $company_id;
                    $job->principal_id = $inbound_job->principal_id;
                    $job->job_no = $job_no;
                    $job->job_date = $job_date;
                    $job->class_id = 3;
                    $job->mode_id = 1;
                    $job->description = "Cross Dock Inbound $inbound_job->job_no" ;
                    $job->etd = $order_date;
                    $job->remarks = $job_date;
                    $job->user_id = $username;
                    $job->entry_date = \Carbon\Carbon::now();

                    $job->save();

                    $order = new \App\Models\Transaction\Outbound\Order();
                    
                    $order->company_id = $company_id;
                    $order->principal_id = $inbound_job->principal_id;
                    $order->outbound_id = $job->id;
                    $order->job_no = $job_no;
                    $order->customer_id = $customer_id;
                    $order->order_no = $order_no;
                    $order->po_number = $order_no;
                    $order->order_date = $order_date;
                    $order->due_date = $due_date;
                    $order->user_id = $username;

                    $order->save();
                }

                foreach ($data as $id) {
                    $batchin = \App\Models\Transaction\Inbound\Batch::find($id);
                    
                    $stock = \App\Models\Transaction\Stock\Ledger::where("company_id", $company_id)
                                    ->where("principal_id", $batchin->principal_id)
                                    ->where("serial_no", $batchin->serial_no)
                                    ->where("qtya", ">", 0)
                                    ->first();

                    $pqty = ($stock->qtya  - ($stock->qtya % $stock->uppp)) / $stock->uppp;
                    $mqty = (($stock->qtya % $stock->uppp) - (($stock->qtya % $stock->uppp) % $stock->muppp)) / $stock->muppp;
                    $bqty = $stock->qtya % $stock->uppp % $stock->muppp;
                    $qty = $stock->qtya;

                    $detail = new \App\Models\Transaction\Outbound\Detail;
        
                    $detail->company_id = $company_id;
                    $detail->outbound_id = $job->id;
                    $detail->order_id = $order->id;
                    $detail->principal_id = $inbound_job->principal_id;
                    $detail->customer_id = $customer_id;
                    $detail->job_no = $job_no;
                    $detail->order_no = $order_no;
                    $detail->product_id = $stock->product_id;
                    $detail->product_code = $stock->product_code;
                    $detail->lot_no = $stock->lot_no;
                    $detail->site_id = $stock->site_id;
                    $detail->area_id = $stock->area_id;
                    $detail->location_from_id = $stock->location_id;
                    $detail->location_from = $stock->location_code;
                    $detail->location_to_id = $stock->location_id;
                    $detail->location_to = $stock->location_code;
                    $detail->puom = $stock->puom;
                    $detail->muom = $stock->muom;
                    $detail->buom = $stock->buom;
                    $detail->uppp = $stock->uppp;
                    $detail->muppp = $stock->muppp;
                    $detail->pqty = $pqty;
                    $detail->mqty = $mqty;
                    $detail->bqty = $bqty;
                    $detail->qty = $qty;
                    $detail->user_id = $username;
                    $detail->picking_flag = "Yes";
                    $detail->picking_by = $username;
                    $detail->picking_date = \Carbon\Carbon::now();

                    $detail->save();
                    
                    $outbound_batch = [];

                    $outbound_batch[] = [
                        "outbound_id" => $job->id,
                        "picking_id" => $detail->id,
                        "serial_id" => $stock->id,
                        "company_id" => $company_id,
                        "principal_id" => $inbound_job->principal_id,
                        "customer_id" => $customer_id,
                        "order_no" => $order_no,
                        "serial_no" => $stock->serial_no,
                        "job_no" => $job_no,
                        "product_id" => $stock->product_id,
                        "product_code" => $stock->product_code,
                        "po_number" => $stock->po_number,
                        "lot_no" => $stock->lot_no,
                        "document_ref" => $stock->document_ref,
                        "reference_no" => $stock->document_ref,
                        "mfg_date" => $stock->mfg_date,
                        "exp_date" => $stock->exp_date,
                        "site_id" => $stock->site_id,
                        "area_id" => $stock->area_id,
                        "location_id" => $stock->location_id,
                        "location_code" => $stock->location_code,
                        "puom" => $stock->puom,
                        "muom" => $stock->muom,
                        "buom" => $stock->buom,
                        "uppp" => $stock->uppp,
                        "muppp" => $stock->muppp,
                        "pqty" => $pqty,
                        "mqty" => $mqty,
                        "bqty" => $bqty,
                        "qty" => $qty,
                        "pallet_qty" => $stock->pallet_qty,
                        "base_unit" => $stock->base_unit,
                        "created_at" => \Carbon\Carbon::now()
                    ];

                    \App\Models\Transaction\Outbound\Batch::insert($outbound_batch);

                    $stock->qtya = $stock->qtya - $qty;
                    $stock->qtyp = $stock->qtyp + $qty;
                    $stock->save();

                    $batchin->crossdock_flag = "Yes"; 
                    $batchin->save();
                }

                $order->confirmed_flag = 'Yes';
                $order->save();
                
                $job->allocated_flag = 'Yes';
                $job->allocated_by = $username;
                $job->allocated_date = \Carbon\Carbon::now();
                $job->save();

                DB::commit();
                
                $message = ['success'=>'Data Successfully Saved'];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>$e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    private function getJob() {        
        $company_id = Auth::user()->company_id;
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = \App\Models\Transaction\Outbound\Job::where('company_id', $company_id)
                ->whereYear('job_date', $year)
                ->whereMonth('job_date', $month)
                ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }

        $job_no = '2' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $job_no;
    }
}