<?php

namespace App\Http\Controllers\NewUpdated\OccupancyDaillyDC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;

class OccupancyDaillyDCController extends Controller
{
    public function index()
    {
        $principal      = Auth::user()->principal;
        $branch         = Auth::user()->branch;
        return view('new.OccupancyDailyDC.index', compact('principal', 'branch'));
    }

    public function search(Request $request)
    {
        try {
            $data = DB::table('iv_occupancy_daily')
                ->selectRaw('
                    DATE(transaction_date) as date,
                    SUM(`in`) as `in`,
                    SUM(`out`) as `out`,
                    MAX(qty) as qty
                ')
                ->where('principal_id', $request->principal_id)
                ->whereMonth('transaction_date', $request->month)
                ->whereYear('transaction_date', date('Y'))
                ->groupBy(DB::raw('DATE(transaction_date)'))
                ->orderBy('date')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function unfreeze($branch, $principal)
    {

        DB::beginTransaction();
        try {
            DB::table('iv_stock_ledger')
                ->where('branch_id', $branch)
                ->where('principal_id', $principal)
                ->where('qtys', '>', 0)
                ->update([
                    'freeze_flag' => 'No',
                    'freeze_by' => null,
                    'freeze_date' => null,
                    'freeze_reason' => null,
                ]);
            $principal = MasterPrincipal::find($principal);
            $mapEmail = [
                1 => 'operational.mkt.jkt@samudera.id',
                2 => 'operational.mkt.srg@samudera.id',
                3 => 'operational.mkt.sub@samudera.id',
                4 => 'operational.mkt.blw@samudera.id',
            ];
            $mailto = $mapEmail[$branch] ?? 'operational.mkt.mks@samudera.id';
            Mail::to($mailto)->send(new UnfreezeStockDCMail($principal));
            DB::commit();

            Session::flash('success', 'Data has been deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            $message = ["error" => [$e->getMessage()]];
            return $message;
        }
        return back();
    }
}
