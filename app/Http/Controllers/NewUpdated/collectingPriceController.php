<?php

namespace App\Http\Controllers\NewUpdated;

use App\Http\Controllers\Controller;
use App\Models\Transaction\Stock\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Session;
use DataTables;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Crypt;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class collectingPriceController extends Controller
{
    public function index()
    {
        $principal = DB::table('iv_principal')->where('active', 'Yes')->get();
        $price     = DB::table('mt_collecting_price')->get();
        $price->map(function ($value) {
            $value->principal = DB::table('iv_principal')
                ->where('active', 'Yes')
                ->where('id', $value->id_principal)
                ->first()->principal_name ?? '-';
        });

        return view("new.collectingPrice.index", compact('principal', 'price'));
    }

    public function submit(Request $request)
    {
        $validasi = DB::table('mt_collecting_price')->where('id_principal', $request->principal)->count();
        if ($validasi > 0) {
            Session::flash('error', 'Princial already exists');
            return back();
        } else {
            DB::table('mt_collecting_price')->insert([
                'id_principal' => $request->principal,
                'handling_in'  => $request->handling_in,
                'handling_out'  => $request->handling_out,
                'cbm_day'  => $request->cbm_day,
                'sqm_permonth'  => $request->sqm_per_month,
            ]);
            Session::flash('success', 'Data has been submitted');
            return back();
        }
    }

    public function delete($id)
    {
        DB::table('mt_collecting_price')
            ->where('id', $id)
            ->delete();

        Session::flash('success', 'Data has been deleted');
        return back();
    }
}
