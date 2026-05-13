<?php

namespace App\Http\Controllers\NewUpdated\MNRManagement\Transaction\in;

use App\Http\Controllers\Controller;
use App\Models\Transaction\Stock\Ledger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Session;
use DataTables;
use Illuminate\Support\Carbon;

class TransactionInController extends Controller
{


    public function index()
    {

        return view('new.MNRManagement.transaction.transactionin');
    }

    public function create()
    {
        $branch=DB::table('mt_branch')->get();
        $vendors=DB::table('mr_master_vendors')->get();
        $spareparts=DB::table('mr_master_spareparts')->get();
        // dd($jobno);
        return view('new.MNRManagement.transaction.create', compact('branch', 'vendors', 'spareparts'));
    }

    private function generateJobNo(){

            $job = DB::table('mr_transaction_in')
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->max("job_number");
                

            if (is_null($job)) {
                $increment = 1;
            } else {
             
                $increment = substr($job, 7, 4) + 1;
            }
         

            $job_no = '1' . date('Y') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');
            return $job_no;
    }
}
