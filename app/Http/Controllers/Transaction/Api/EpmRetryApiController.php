<?php

namespace App\Http\Controllers\Transaction\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Transaction\Api\EpmResponseApi as ApiEpmResponseApi;

use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as guzzle;
use GuzzleHttp\Exception\BadResponseException;
use App\Models\Transaction\Inbound\Batch as inboundBatch;
use App\Models\Transaction\Inbound\Job as inboundJob;

class EpmRetryApiController extends Controller
{
    public function index(Request $request)
    {
        $details = ApiEpmResponseApi::where('status', '!=', '200')->get();
        if ($request->ajax()) {
            return datatables()->of($details)
                ->addColumn('status_tooltip', function ($data) {
                    $tags = "";
                    if ($data->status == 200) {
                        $waktu_kirim = "";
                        if ($data->updated_at) {
                            $waktu_kirim = $data->updated_at;
                        } else {
                            $waktu_kirim = $data->created_at;
                        }
                        $tooltip = "data send on $waktu_kirim";
                        $tags = "<a href='#' data-toggle='tooltip' data-placement='top' title='$tooltip'>Success</a>";
                    } else {
                        $waktu_kirim = "";
                        $error_text = preg_replace('/((\w+\W*){15}(\w+))(.*)/', '${1}', $data->error);
                        if ($data->updated_at) {
                            $waktu_kirim = $data->updated_at;
                        } else {
                            $waktu_kirim = $data->created_at;
                        }
                        $tooltip = "Last data Send on $waktu_kirim With error $error_text";
                        $error_status = $data->status;
                        $tags = "<a href='#' data-toggle='tooltip' data-placement='top' title='$tooltip'>Failed ($error_status)</a>";
                    }
                    return $tags;
                })
                ->addColumn('action', function ($data) {
                    $button = "";
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="View" class="edit btn btn-info btn-sm edit-data"><i class="far fa-sharp fa-solid fa-clipboard"></i> VIEW</a>';
                    return $button;
                })
                ->rawColumns(['action','status_tooltip'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('transaction.api.epm-retry');
        // return view('settings.email');
    }

    public function resend(Request $request)
    {
        $id = $request->id;
        $data = ApiEpmResponseApi::find($id);
        $urlapi = '';
        // dd($datasend, json_encode($datasend), $jsondatasend, json_encode($jsondatasend),"[".$jsondatasend."]");
        $client = new guzzle();
        if ($data->activity == 'INBOUND') {
            $urlapi = 'https://egate.enseval.com/api/Principal/MiniDC/MKTReceiving';
        } else if ($data->activity == 'STOCKTRANSFER') {
            $urlapi = 'https://egate.enseval.com/api/Principal/MiniDC/MKTMovement2';
        } else if ($data->activity == 'OUTBOUND') {
            $urlapi = 'https://egate.enseval.com/api/Principal/MiniDC/outbound';
        }
        $statusresponse = '';
        $messageresponse = '';
        try {
            $response = $client->request('POST', $urlapi, [
                'headers' => [
                    'accept' => '/',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'dGVzdDp0ZXN0'
                ],
                'body' => $data->body
            ]);
            $statusresponse = $response->getStatusCode();
            $messageresponse = $response->getBody()->getContents();
            DB::beginTransaction();
            $forupdate = ApiEpmResponseApi::find($id);

            $forupdate->status = $response->getStatusCode();
            $forupdate->error = $response->getBody()->getContents();
            $forupdate->save();
            DB::commit();
        } catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = (string) $response->getBody();
            $statusresponse = $response->getStatusCode();
            $messageresponse = $jsonBody;
            DB::beginTransaction();

            $forupdate = ApiEpmResponseApi::find($id);

            $forupdate->status = $response->getStatusCode();
            $forupdate->error = $jsonBody;
            $forupdate->save();
            DB::commit();
        }
        return response()->json(['status' => $statusresponse, 'messages' => $messageresponse]);
    }

    public function edit($id)
    {
        $data  = ApiEpmResponseApi::find($id);
        if ($data->activity == 'INBOUND') {
            $dataresponse = $this->inboundEdit($data);
        } else if ($data->activity == 'STOCKTRANSFER') {
            $dataresponse = $this->movementEdit($data);
        } else if ($data->activity == 'OUTBOUND') {
            $dataresponse = $this->outboundEdit($data);
        }
        return response()->json($dataresponse);
    }

    public function destroy(Request $request)
    {
        try {
            $data = ApiEpmResponseApi::where('id', $request->id)->delete();

            $data = ['success' => 'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex) {
            $data = ['error' => 'Cannot be deleted, this data is already used.'];
        }

        return response()->json($data);
    }

    private function inboundEdit($data)
    {
        $jsonBody = json_decode($data->body);
        $header = array();
        $skulist = array();
        $detail = '';
        $batch = '';
        if (is_array($jsonBody)) {
            if (sizeof($jsonBody) > 0) {
                $detail .= '<table style="border-collapse: collapse; width: 100%;"><tr><th style="border: 1px solid #dddddd; padding: 5px;">Item Code</th><th style="border: 1px solid #dddddd; padding: 5px;">lot Number</th><th style="border: 1px solid #dddddd; padding: 5px;">Quantity</th><th style="border: 1px solid #dddddd; padding: 5px;">UOM</th></tr>';
                $batch .= '<table style="border-collapse: collapse; width: 100%;"><tr><th style="border: 1px solid #dddddd; padding: 5px;">Item Code</th><th style="border: 1px solid #dddddd; padding: 5px;">Location</th><th style="border: 1px solid #dddddd; padding: 5px;">Reject QTY</th><th style="border: 1px solid #dddddd; padding: 5px;">Reject QTY</th><th style="border: 1px solid #dddddd; padding: 5px;">UOM</th></tr>';
                foreach ($jsonBody as $key => $value) {
                    array_push($skulist, $value->item_code);
                    $header = array(
                        'shipment_number' => $value->shipment_number,
                        'receipt_date' => $value->receipt_date,
                        'shipping_org' => $value->shipping_org,
                        'shipment_qty' => $value->shipment_qty,
                        'destination_org' => $value->destination_org,
                        'shipment_uom' => $value->shipment_uom,
                    );
                    $cekinarray = in_array($value->item_code, $skulist);
                    if ($cekinarray) {
                        $detail .= "<tr><td style='border: 1px solid #dddddd; padding: 5px;'>$value->item_code</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->lot_number</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->shipment_qty</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->shipment_uom</td></tr>";
                    }
                    $batch .= "<tr><td style='border: 1px solid #dddddd; padding: 5px;'>$value->item_code</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->to_row.$value->to_bin.$value->to_level</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->receipt_qty</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->reject_qty</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->receipt_uom</td></tr>";
                }
                $detail .= '</table>';
                $batch .= '</table>';
            }
        } else {
            $header = array(
                'shipment_number' => $jsonBody->shipment_number,
                'receipt_date' => $jsonBody->receipt_date,
                'shipping_org' => $jsonBody->shipping_org,
                'shipment_qty' => $jsonBody->shipment_qty,
                'destination_org' => $jsonBody->destination_org,
                'shipment_uom' => $jsonBody->shipment_uom,
            );

            $detail .= '<table style="border-collapse: collapse; width: 100%;"><tr><th style="border: 1px solid #dddddd; padding: 5px;">Item Code</th><th style="border: 1px solid #dddddd; padding: 5px;">lot Number</th><th style="border: 1px solid #dddddd; padding: 5px;">Quantity</th><th style="border: 1px solid #dddddd; padding: 5px;">UOM</th></tr>';
            $batch .= '<table style="border-collapse: collapse; width: 100%;"><tr><th style="border: 1px solid #dddddd; padding: 5px;">Item Code</th><th style="border: 1px solid #dddddd; padding: 5px;">Location</th><th style="border: 1px solid #dddddd; padding: 5px;">Reject QTY</th><th style="border: 1px solid #dddddd; padding: 5px;">Reject QTY</th><th style="border: 1px solid #dddddd; padding: 5px;">UOM</th></tr>';
            $detail .= "<tr><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->item_code</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->lot_number</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->shipment_qty</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->shipment_uom</td></tr>";
            $batch .= "<tr><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->item_code</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->to_row.$jsonBody->to_bin.$jsonBody->to_level</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->receipt_qty</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->reject_qty</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->receipt_uom</td></tr>";
            $detail .= '</table>';
            $batch .= '</table>';
        }

        $data->bodyHeader = $header;
        $data->bodyDetail = $detail;
        $data->bodyBatch = $batch;
        $data->partData = "part_inbound";
        return $data;
    }

    private function movementEdit($data)
    {
        $jsonBody = json_decode($data->body);
        // dd($jsonBody);
        $header = array();
        $skulist = array();
        $detail = '';
        $batch = '';
        if (is_array($jsonBody)) {
            if (sizeof($jsonBody) > 0) {
                $detail .= '<table style="border-collapse: collapse; width: 100%;"><tr><th style="border: 1px solid #dddddd; padding: 5px;">Item Code</th><th style="border: 1px solid #dddddd; padding: 5px;">QTY</th><th style="border: 1px solid #dddddd; padding: 5px;">UOM</th><th style="border: 1px solid #dddddd; padding: 5px;">Lot Number</th><th style="border: 1px solid #dddddd; padding: 5px;">From</th><th style="border: 1px solid #dddddd; padding: 5px;">To</th></tr>';
                foreach ($jsonBody as $key => $value) {
                    array_push($skulist, $value->ITEM_CODE);
                    $header = array(
                        'SM_MOVEMENT_ID' => $value->MOVEMENT_ID,
                        'SM_ORG_CODE' => $value->ORG_CODE,
                        'SM_TRANSACTION_DATE' => $value->TRANSACTION_DATE
                    );
                    $detail .= "<tr><td style='border: 1px solid #dddddd; padding: 5px;'>$value->ITEM_CODE</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->QUANTITY</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->UOM</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->LOT_NUMBER</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->FROM_ROW.$value->FROM_BIN.$value->FROM_LEVEL($value->FROM_SUBINVENTORY)</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->TO_ROW.$value->TO_BIN.$value->TO_LEVEL($value->TO_SUBINVENTORY)</td></tr>";
                }
                $detail .= '</table>';
            }
        } else {
            $header = array(
                'SM_MOVEMENT_ID' => $jsonBody->MOVEMENT_ID,
                'SM_ORG_CODE' => $jsonBody->ORG_CODE,
                'SM_TRANSACTION_DATE' => $jsonBody->TRANSACTION_DATE
            );

            $detail .= '<table style="border-collapse: collapse; width: 100%;"><tr><th style="border: 1px solid #dddddd; padding: 5px;">Item Code</th><th style="border: 1px solid #dddddd; padding: 5px;">UOM</th><th style="border: 1px solid #dddddd; padding: 5px;">Lot Number</th><th style="border: 1px solid #dddddd; padding: 5px;">From</th><th style="border: 1px solid #dddddd; padding: 5px;">To</th></tr>';
            $detail .= "<tr><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->ITEM_CODE</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->UOM</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->LOT_NUMBER</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->FROM_ROW.$jsonBody->FROM_BIN.$jsonBody->FROM_LEVEL($jsonBody->FROM_SUBINVENTORY)</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->TO_ROW.$jsonBody->TO_BIN.$jsonBody->TO_LEVEL($jsonBody->TO_SUBINVENTORY)</td></tr>";
            $detail .= '</table>';
        }

        $data->bodyHeader = $header;
        $data->bodyDetail = $detail;
        $data->bodyBatch = $batch;
        $data->partData = "part_movement";
        return $data;
    }

    private function outboundEdit($data)
    {
        $jsonBody = json_decode($data->body);
        // dd($data);
        $header = array();
        $skulist = array();
        $detail = '';
        $batch = '';
        if (is_array($jsonBody)) {
            if (sizeof($jsonBody) > 0) {
                // $detail .= '<table style="border-collapse: collapse; width: 100%;"><tr><th style="border: 1px solid #dddddd; padding: 5px;">Item Code</th><th style="border: 1px solid #dddddd; padding: 5px;">lot Number</th><th style="border: 1px solid #dddddd; padding: 5px;">Quantity</th><th style="border: 1px solid #dddddd; padding: 5px;">UOM</th></tr>';
                $batch .= '<table style="border-collapse: collapse; width: 100%;"><tr><th style="border: 1px solid #dddddd; padding: 5px;">Item Code</th><th style="border: 1px solid #dddddd; padding: 5px;">lot Number</th><th style="border: 1px solid #dddddd; padding: 5px;">FROM</th><th style="border: 1px solid #dddddd; padding: 5px;">Picked Date</th></tr>';
                foreach ($jsonBody as $key => $value) {
                    array_push($skulist, $value->item_code);
                    $header = array(
                        'out_delivery_number' => $value->delivery_number,
                        'out_shipment_date' => $value->shipment_date,
                        'out_iso_number' => $value->iso_number,
                    );
                    // $cekinarray = in_array($value->item_code, $skulist);
                    // if ($cekinarray) {
                    //     $detail .= "<tr><td style='border: 1px solid #dddddd; padding: 5px;'>$value->item_code</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->lot_number</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->shipment_qty</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->shipment_uom</td></tr>";
                    // }
                    $batch .= "<tr><td style='border: 1px solid #dddddd; padding: 5px;'>$value->item_code</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->lot_number</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->loc_row.$value->loc_bin.$value->loc_level</td><td style='border: 1px solid #dddddd; padding: 5px;'>$value->picked_date</td></tr>";
                }
                // $detail .= '</table>';
                $batch .= '</table>';
            }
        } else {
            $header = array(
                'out_delivery_number' => $jsonBody->delivery_number,
                'out_shipment_date' => $jsonBody->shipment_date,
                'out_iso_number' => $jsonBody->iso_number,
            );

            // $detail .= '<table style="border-collapse: collapse; width: 100%;"><tr><th style="border: 1px solid #dddddd; padding: 5px;">Item Code</th><th style="border: 1px solid #dddddd; padding: 5px;">lot Number</th><th style="border: 1px solid #dddddd; padding: 5px;">Quantity</th><th style="border: 1px solid #dddddd; padding: 5px;">UOM</th></tr>';
            $batch .= '<table style="border-collapse: collapse; width: 100%;"><tr><th style="border: 1px solid #dddddd; padding: 5px;">Item Code</th><th style="border: 1px solid #dddddd; padding: 5px;">Location</th><th style="border: 1px solid #dddddd; padding: 5px;">Reject QTY</th><th style="border: 1px solid #dddddd; padding: 5px;">Reject QTY</th><th style="border: 1px solid #dddddd; padding: 5px;">UOM</th></tr>';
            // $detail .= "<tr><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->item_code</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->lot_number</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->shipment_qty</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->shipment_uom</td></tr>";
            $batch .= "<tr><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->item_code</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->lot_number</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->loc_row.$jsonBody->loc_bin.$jsonBody->loc_level</td><td style='border: 1px solid #dddddd; padding: 5px;'>$jsonBody->picked_date</td></tr>";
            // $detail .= '</table>';
            $batch .= '</table>';
        }

        $data->bodyHeader = $header;
        $data->bodyDetail = $detail;
        $data->bodyBatch = $batch;
        $data->partData = "part_outbound";
        return $data;
    }
}
