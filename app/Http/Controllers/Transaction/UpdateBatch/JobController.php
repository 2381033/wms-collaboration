<?php

namespace App\Http\Controllers\Transaction\UpdateBatch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function myBranch()
    {
        $branch = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->get()
            ->pluck('branch_id')
            ->toArray();
        return $branch;
    }

    public function getList()
    {
        $data = DB::table('iv_stock_ledger')
            ->whereIn('branch_id', $this->myBranch())
            ->where('qtya', '>', 0)
            ->whereNotNull('exp_date')
            ->get();
        return datatables()->of($data)->make(true);
    }

    public function getData($id)
    {
        $data = DB::table('iv_stock_ledger')
            ->where('id', $id)
            ->first();

        return response()->json($data);
    }

    public function submit(Request $request)
    {
        $serial_no = DB::table('iv_stock_ledger')
            ->where('id', $request->id)
            ->value('serial_no');

        if (!is_null($request->batch)) {
            $this->updateBatch($request->all(), $serial_no);
        } else {
            $this->updateExpired($request->all(), $serial_no);
        }
        return response()->json('ok');
    }

    private function updateBatch($params, $serial_no)
    {
        //update stock_ledger
        DB::table('iv_stock_ledger')
            ->where('id', $params['id'])
            ->update([
                'lot_no' => $params['batch'],
                'exp_date' => $params['exp_date']
            ]);

        //update stock_transaction
        DB::table('iv_stock_transaction')
            ->where('serial_no', $serial_no)
            ->update([
                'lot_no' => $params['batch'],
                'exp_date' => $params['exp_date']
            ]);

        //update inbound_batch
        DB::table('iv_outbound_batch')
            ->where('serial_no', $serial_no)
            ->update([
                'lot_no' => $params['batch'],
                'exp_date' => $params['exp_date']
            ]);

        //update inbound_batch
        DB::table('iv_inbound_batch')
            ->where('serial_no', $serial_no)
            ->update([
                'lot_no' => $params['batch'],
                'exp_date' => $params['exp_date']
            ]);
    }
    private function updateExpired($params, $serial_no)
    {
        //update stock_ledger
        DB::table('iv_stock_ledger')
            ->where('id', $params['id'])
            ->update([
                'exp_date' => $params['exp_date']
            ]);

        //update stock_transaction
        DB::table('iv_stock_transaction')
            ->where('serial_no', $serial_no)
            ->update([
                'exp_date' => $params['exp_date']
            ]);

        //update inbound_batch
        DB::table('iv_outbound_batch')
            ->where('serial_no', $serial_no)
            ->update([
                'exp_date' => $params['exp_date']
            ]);

        //update inbound_batch
        DB::table('iv_inbound_batch')
            ->where('serial_no', $serial_no)
            ->update([
                'exp_date' => $params['exp_date']
            ]);
    }

    public function index()
    {
        return view('transaction.updatebatch.index');
    }
}
