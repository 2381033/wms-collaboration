<?php

namespace App\Http\Controllers\NewUpdated\FreezeStockDC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\FreezeStockDCMail as FreezeStockDCMail;
use App\Mail\UnfreezeStockDCMail;
use App\Models\Master\Principal as MasterPrincipal;
use Illuminate\Support\Facades\Session;

class FreezeStockDCController extends Controller
{
    public function index()
    {
        $principal      = Auth::user()->principal;
        $branch         = Auth::user()->branch;
        $data = DB::table('iv_freeze_job as a')
            ->select('a.*', 'b.principal_name', 'c.branch_name')
            ->join('iv_principal as b', 'a.principal_id', '=', 'b.id')
            ->join('mt_branch as c', 'a.branch_id', '=', 'c.id')
            ->where('created_by', Auth::user()->username)
            ->where('status_flag', 'Run')
            ->get();
        return view('new.FreezeStockDC.index', compact('principal', 'branch', 'data'));
    }
    public function store(Request $request)
    {
        $rules = array(
            'principal_id' => 'required',
            'branch_id' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            try {
                DB::table('iv_freeze_job')
                    ->insert([
                        'branch_id' => $request->branch_id,
                        'principal_id' => $request->principal_id,
                        'freeze_activity' => $request->activity,
                        'status_flag' => 'Run',
                        'created_by' => Auth::user()->username,
                        'created_at' => date('Y-m-d H:i:s'),
                        'description' => $request->body_email,
                    ]);
                $principal = MasterPrincipal::find($request->principal_id);
                $mapEmail = [
                    1 => 'operational.mkt.jkt@samudera.id',
                    2 => 'operational.mkt.srg@samudera.id',
                    3 => 'operational.mkt.sub@samudera.id',
                    4 => 'operational.mkt.blw@samudera.id',
                ];
                $mailto = $mapEmail[$request->branch_id] ?? 'operational.mkt.mks@samudera.id';
                Mail::to($mailto)->send(new FreezeStockDCMail($principal, $request->body_email, $request->activity));
                DB::commit();
                $message = ["message" => "success"];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function unFreeze(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                DB::table('iv_freeze_job')
                    ->where('id', $request->id_unf)
                    ->update([
                        'status_flag' => 'Closed',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->username,
                    ]);
                $principal = MasterPrincipal::find($request->principal_id_unf);
                $mapEmail = [
                    1 => 'operational.mkt.jkt@samudera.id',
                    2 => 'operational.mkt.srg@samudera.id',
                    3 => 'operational.mkt.sub@samudera.id',
                    4 => 'operational.mkt.blw@samudera.id',
                ];
                $mailto = $mapEmail[$request->branch_id] ?? 'operational.mkt.mks@samudera.id';
                Mail::to($mailto)->send(new UnfreezeStockDCMail($principal, $request->mail_body));
                DB::commit();
                $message = ["message" => "success"];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ["error" => [$e->getMessage()]];
                return $message;
            }
        });

        return response()->json($exception);
    }
}
