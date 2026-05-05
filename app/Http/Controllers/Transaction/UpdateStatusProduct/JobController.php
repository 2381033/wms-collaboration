<?php

namespace App\Http\Controllers\Transaction\UpdateStatusProduct;

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
            ->select('id', 'product_code', 'product_id', 'principal_id')
            ->whereIn('branch_id', $this->myBranch())
            ->where('qtya', '>', 0)
            ->orderBy('principal_id', 'DESC')
            ->groupBy('product_id')
            ->get();
        $data->map(function ($value) {
            $value->principal = DB::table('iv_principal')->where('id', $value->principal_id)->value('principal_name') ?? '';
            return $value;
        });
        return datatables()->of($data)->make(true);
    }

    public function showData($product_id)
    {
        $data = DB::table('iv_stock_ledger')
            ->where('product_id', $product_id)
            ->where('qtya', '>', 0)
            ->orderBy('location_code', 'ASC')
            ->get();

        return response()->json($data);
    }

    public function editData($id)
    {
        $data = DB::table('iv_stock_ledger')
            ->where('id', $id)
            ->where('qtya', '>', 0)
            ->first();

        return response()->json($data);
    }

    public function submit(Request $request)
    {
        //update stock_ledger
        DB::table('iv_stock_ledger')
            ->where('id', $request->id_ledger)
            ->update([
                'status' => $request->status,
            ]);

        return response()->json('ok');
    }

    public function index()
    {
        return view('transaction.updatestatus.index');
    }
}
