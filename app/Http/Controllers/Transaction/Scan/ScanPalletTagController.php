<?php

namespace App\Http\Controllers\Transaction\Scan;

use App\Http\Controllers\Controller;
use App\Models\Transaction\Stock\Ledger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Str;
use Session;
use Illuminate\Support\Facades\Crypt;

class ScanPalletTagController extends Controller
{
    public function index()
    {
        return view("transaction.scan.index");
    }

    public function indexLocation()
    {
        return view("transaction.scan.location");
    }

    public function doScan($qr)
    {
        $parameter = explode('<', $qr);
        if (count($parameter) > 1) {
            $barcode    = $parameter[0];
            $lot_no     = $parameter[1];
            $location   = $parameter[2];

            $detail = DB::table('iv_inbound_detail')
                ->where('qrcode', $barcode)
                ->get()->pluck('id')->toArray();

            $batch = DB::table('iv_inbound_batch')
                ->whereIn('packing_id', $detail)
                ->get();
            $job_no = $batch->pluck('job_no')->toArray(); 
            $prod = $batch->pluck('product_code')->toArray(); 

            $stock = DB::table('iv_stock_ledger')
                ->where('qtys', '>', '0')
                ->whereIn('job_no', $job_no)
                ->where('location_code', $location)
                ->get();
            if (!empty($lot_no)) {
                $stock =  $stock->where('lot_no', $lot_no);
            } else {
                $stock = $stock;
            }

            $transaction = DB::table('iv_stock_transaction')
                ->orderBy('id', 'ASC')
                ->whereIn('job_no', $job_no)
                ->whereIn('product_code', $prod)
                ->where('location_code', $location)
                ->get();

            $transaction = $transaction->map(function ($value) {
                $value->tanggal = Carbon::parse($value->created_at)->format('d M Y ');
                return $value;
            });

            return response()->json([
                'status' => 'ok',
                'data'   => [
                    'stock' => $stock,
                    'transaction' => $transaction
                ]
            ]);
        } else {
            $detail = DB::table('iv_inbound_detail')
                ->where('qrcode', $qr)
                ->get()
                ->pluck('id')
                ->toArray();

            $batch = DB::table('iv_inbound_batch')
                ->whereIn('packing_id', $detail)
                ->get();
            $job_no = $batch->pluck('job_no')->toArray(); 
            $prod = $batch->pluck('product_code')->toArray(); 

            $stock = DB::table('iv_stock_ledger')
                ->whereIn('job_no', $job_no)
                ->whereIn('product_code', $prod)
                ->where('qtys', '>', '0')
                ->get();

            $transaction = DB::table('iv_stock_transaction')
                ->orderBy('id', 'ASC')
                ->whereIn('job_no', $job_no)
                ->whereIn('product_code', $prod)
                ->get();

            $transaction = $transaction->map(function ($value) {
                $value->tanggal = Carbon::parse($value->created_at)->format('d M Y ');
                return $value;
            });

            return response()->json([
                'status' => 'ok',
                'data'   => [
                    'stock' => $stock,
                    'transaction' => $transaction
                ]
            ]);
        }
    }

    public function getBlokLocation()
    {
        $exception = DB::transaction(function () {
            try {
                $site = DB::table('users_site')
                    ->where('user_id', Auth::user()->id)
                    ->get()
                    ->pluck('site_id')
                    ->toArray();

                $area = DB::table('iv_site_area')
                    ->whereIn('site_id', $site)
                    ->whereNotIn('id', [4, 5, 28])
                    ->get()
                    ->pluck('id')
                    ->toArray();

                $data = DB::table('iv_location')
                    ->orderBy('location_code', 'ASC')
                    ->whereIn('site_id', $site)
                    ->whereIn('area_id', $area)
                    ->groupBy('location_code')
                    ->get()->pluck('location_code')->toArray();
                $list = [];
                foreach ($data as $key => $value) {
                    $list[] = $this->multiexplode(array('.', '-'), $value)[0];
                }
                $message = ['data' => array_unique($list)];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['message' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getSkuOnBlok($blok)
    {
        $exception = DB::transaction(function () use ($blok) {
            try {
                $data = DB::table('iv_stock_ledger')
                    ->orderBy('product_code', 'ASC')
                    ->where('location_code', 'LIKE', $blok . '%')
                    ->where('qtys', '>', 0)
                    ->get();
                $message = ['data' => $data];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['message' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    private function multiexplode($delimiters, $string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    }

    public function doScanLocation($params)
    {
        $exception = DB::transaction(function () use ($params) {
            try {
                $site = DB::table('users_site')
                    ->where('user_id', Auth::user()->id)
                    ->get()
                    ->pluck('site_id')
                    ->toArray();

                $location = DB::table('iv_location')
                    ->orderBy('location_code', 'ASC')
                    ->whereIn('site_id', $site)
                    ->where('location_code', $params)
                    ->count();
                if ($location > 0) {
                    $data = DB::table('iv_stock_ledger')
                        ->orderBy('product_code', 'ASC')
                        ->where('location_code', $params)
                        ->whereIn('site_id', $site)
                        ->where('qtys', '>', 0)
                        ->get();
                    if (count($data) > 0) {
                        $message = ['data' => $data];
                    } else {
                        $message = ['data' => 'null'];
                    }
                } else {
                    $message = ['data' => 'location'];
                }
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['message' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }
}
