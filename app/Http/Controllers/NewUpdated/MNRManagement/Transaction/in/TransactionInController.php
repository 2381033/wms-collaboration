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
        $data = DB::table('mr_transaction_in as ti')
            ->leftJoin('mr_master_vendors as v', 'v.id', '=', 'ti.vendor_id')
            ->leftJoin('mt_branch as b', 'b.id', '=', 'ti.branch_id')
            ->leftJoin('mr_master_spareparts as s', 's.id', '=', 'ti.sparepart_id')
            ->select(
                'ti.no_po',
                'ti.tanggal_po',
                'v.vendor_name',
                'b.branch_name',
                'ti.job_number',
                's.name as sparepart_name',
                'ti.images',
                'ti.remarks',
                'ti.status'
            )
            ->orderBy('ti.created_at', 'desc')
            ->get();

        return view('new.MNRManagement.transaction.transactionin', compact('data'));
    }

    public function create()
    {
        $branch = DB::table('mt_branch')->get();
        $vendors = DB::table('mr_master_vendors')->get();
        $spareparts = DB::table('mr_master_spareparts')->get();
        // dd($jobno);
        return view('new.MNRManagement.transaction.create', compact('branch', 'vendors', 'spareparts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_po'        => 'required',
            'tanggal_po'   => 'required',
            'vendor_id'    => 'required',
            'branch_id'    => 'required',
            'sparepart_id' => 'required',
            'remarks'      => 'required',
            'images'       => 'required',
        ]);

        $jobNumber = $this->generateJobNo();

        $imageName = null;
        if ($request->hasFile('images')) {
            $file      = $request->file('images');
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('transaction_in', $imageName, 'public');
        }

        DB::table('mr_transaction_in')->insert([
            'no_po'        => $request->no_po,
            'tanggal_po'   => $request->tanggal_po,
            'vendor_id'    => $request->vendor_id,
            'branch_id'    => $request->branch_id,
            'job_number'   => $jobNumber,
            'sparepart_id' => $request->sparepart_id,
            'images'       => $imageName,
            'remarks'      => $request->remarks,
            'status'       => 'open',
            'created_by'   => Auth::user()->name,
            'updated_by'   => Auth::user()->name,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()
            ->route('transaction.in')
            ->with('success', "Transaksi berhasil disimpan! Job No: {$jobNumber}");
    }

    private function generateJobNo()
    {

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

    public function show($job_number)
    {
        $data = DB::table('mr_transaction_in as ti')
            ->leftJoin('mr_master_vendors as v', 'v.id', '=', 'ti.vendor_id')
            ->leftJoin('mt_branch as b', 'b.id', '=', 'ti.branch_id')
            ->leftJoin('mr_master_spareparts as s', 's.id', '=', 'ti.sparepart_id')
            ->select(
                'ti.job_number',
                'ti.no_po',
                'ti.tanggal_po',
                'ti.remarks',
                'ti.images',
                'ti.status',
                'v.vendor_name',
                'b.branch_name',
                's.name as sparepart_name'
            )
            ->where('ti.job_number', $job_number)
            ->first();

        if (!$data) {
            abort(404);
        }

        return view('new.MNRManagement.transaction.detail', compact('data'));
    }

    public function updateStatus(Request $request, $job_number)
    {
        $current = DB::table('mr_transaction_in')
            ->where('job_number', $job_number)
            ->value('status');

        $newStatus = $current === 'open' ? 'closed' : 'open';

        DB::table('mr_transaction_in')
            ->where('job_number', $job_number)
            ->update([
                'status'     => $newStatus,
                'updated_by' => Auth::user()->name,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('transaction.in.show', $job_number)
            ->with('success', "Status berhasil diubah ke " . strtoupper($newStatus));
    }
}
