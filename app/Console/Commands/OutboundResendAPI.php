<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Outbound\Batch as outboundBatch;
use App\Models\Transaction\Outbound\Detail as outboundDetails;
use App\Models\Transaction\Outbound\Job as outboundJob;

class OutboundResendAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outboundApi:resend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend Wrong API Outbound.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $job_no = '22024030380';
        $datasend = array();
        $jsondatasend = '';
        $dataBatchId = array();
        $batch_list = DB::table("iv_outbound_batch as a")
            ->where("job_no", "$job_no")
            ->orderBy("a.id", "asc")
            ->get();
        foreach ($batch_list as $key => $value) {
            array_push($dataBatchId, $value->id);
        }

        foreach ($dataBatchId as $id) {
            $batch = outboundBatch::find($id);

            $logs = DB::table("iv_epm_api_logs")
                ->where("activity", "OUTBOUND")
                ->where("activity_id", $batch->outbound_id)
                ->where("send_status", "Y");
            $logcount = $logs->count();
            if ($batch->outbound_id > 0 && $logcount > 0) {
                $logdata = $logs->first();
                $job = outboundJob::find($batch->outbound_id);
                // $log_details = DB::table("iv_epm_api_log_details")
                //     ->where("header_id", $logdata->id)
                //     ->where("product_code", $batch->product_code)
                //     ->first();
                $order = DB::table("iv_outbound_order")
                    ->where("outbound_id", $batch->outbound_id)
                    ->where("order_no", $batch->order_no)
                    ->first();
                $qty = ($batch->pqty * $batch->muppp);
                $details = outboundDetails::Find($batch->picking_id);
                $customer_code = DB::table('iv_customer')->where('id', $details->customer_id)->first();
                // $qty = $batch->pqty * $batch->muppp;
                $location_code = explode('.', $batch->location_code);
                $row = $location_code[0];
                $bin = $location_code[1];
                $lvl = $location_code[2];
                $data = [
                    // "order_no" => $order->order_no,
                    // "po_no" => $order->po_number,
                    // "site_id" => "MD1",
                    // "customer_id" => "",
                    // "product_code" => $details->product_code,
                    // "lot_no" => $details->lot_no,
                    // "mqty" => $qty,
                    // "muom" => $details->muom,
                    // "location_code_row" => $row,
                    // "location_code_bin" => $bin,
                    // "location_code_level" => $lvl,
                    // "picking_flag": "string",
                    // "picked_date": date("d-m-Y h:i:m", strtotime($batch->created_at)),
                    // "picking_by": "2020-01-01 23:59:59",
                    // "confirmed_date": "2020-01-01 23:59:59",
                    // "create_at" => $job->created_at,
                    // "description" => $job->description,
                    "loc_bin" => $bin,
                    "loc_row" => $row,
                    "item_code" => $details->product_code,
                    "loc_level" => $lvl,
                    "quantity" => $qty,
                    "uom" => $batch->muom,
                    "picked_by" => 1,
                    "iso_number" => $order->po_number,
                    "lot_number" => $details->lot_no,
                    "picked_date" => date("d-m-Y H:i:m", strtotime($details->picking_date)),
                    "picked_status" => $details->picking_flag,
                    "shipment_date" => date("d-m-Y H:i:m", strtotime($details->confirmed_date)),
                    "delivery_number" => $order->order_no,
                    "to_org_code" => 'MDN',
                    "from_org_code" => "MD1"
                ];
                array_push($datasend, json_encode($data));
                if (strlen($jsondatasend) > 1) {
                    $jsondatasend .= "," . json_encode($data);
                } else {
                    $jsondatasend .= json_encode($data);
                }
            }
        }
        dd($jsondatasend);
        if (strlen($jsondatasend) > 1) {
            $client = new guzzle();
            $urlapi = 'https://egate.enseval.com/api/Principal/MiniDC/outbound';
            try {
                $response = $client->request('POST', $urlapi, [
                    'headers' => [
                        'accept' => '/',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'dGVzdDp0ZXN0'
                    ],
                    'body' => "[" . $jsondatasend . "]"
                ]);
                DB::beginTransaction();
                $saveresponse = DB::table('iv_epm_response_api')->insert([
                    'activity' => 'OUTBOUND',
                    'activity_id' => $batch->outbound_id,
                    'job_no' => $job->job_no,
                    'status' => $response->getStatusCode(),
                    'body' => "[" . $jsondatasend . "]",
                    'error' => $response->getBody()->getContents(),
                    'created_date' => \Carbon\Carbon::now()
                ]);
                if ($saveresponse) {
                    DB::commit();
                } else {
                    DB::rollback();
                }
            } catch (BadResponseException $ex) {
                $response = $ex->getResponse();
                $jsonBody = (string) $response->getBody();
                DB::beginTransaction();
                $saveresponse = DB::table('iv_epm_response_api')->insert([
                    'activity' => 'OUTBOUND',
                    'activity_id' => $batch->outbound_id,
                    'job_no' => $job->job_no,
                    'status' => $response->getStatusCode(),
                    'body' => "[" . $jsondatasend . "]",
                    'error' => $jsonBody,
                    'created_date' => \Carbon\Carbon::now()
                ]);
                if ($saveresponse) {
                    DB::commit();
                } else {
                    DB::rollback();
                }
            }
            return 'sukses';
        } else {
            return 'sukses';
        }
        dd($dataBatchId);

        return 0;
    }
}
