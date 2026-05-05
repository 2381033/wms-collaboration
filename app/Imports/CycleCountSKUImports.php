<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CycleCountSKUImports implements ToCollection, WithHeadingRow
{
    protected $site_id = null;

    public function __construct($site_id)
    {
        $this->site_id = $site_id;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $val) {
            $stock = $this->getStockBySKU($this->site_id, $val['product_code']);
            if ($stock->count() == 0) {
                Session::flash('error', 'Product :  ' . $val['product_code'] . ' stock not available..');
                return back();
            }
        }
        $job_no = $this->getJobNo();
        $job[] = [
            'site_id' => $this->site_id,
            'branch_id' => $this->myBranch(),
            'principal_id' => $stock->first()->principal_id,
            'job_no'  => $job_no,
            'type'  => 'sku',
            'description' => 'Cycle Count ' . date('d-m-Y'),
            'created_by'   => Auth::user()->username,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        DB::table('iv_cyclecount_job')->insert($job);

        foreach ($rows as $val) {
            $stock = $this->getStockBySKU($this->site_id, $val['product_code']);
            foreach ($stock as $value) {
                DB::table('iv_cyclecount_detail')->insert([
                    'job_no' => $job_no,
                    'branch_id' => $value->branch_id,
                    'principal_id' => $value->principal_id,
                    'id_ledger' => $value->id,
                    'product_id' => $value->product_id,
                    'product_code' => $value->product_code,
                    'site_id' => $value->site_id,
                    'area_id' => $value->area_id,
                    'location_id' => $value->location_id,
                    'location_code' => $value->location_code,
                    'puom' => $value->puom,
                    'muom' => $value->muom,
                    'uppp' => $value->uppp,
                    'muppp' => $value->muppp,
                    'pqty' => $value->pqty,
                    'mqty' => $value->mqty,
                    'created_by'   => Auth::user()->username,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        Session::flash('success', 'Data has been imported successfully');
        return back();
    }

    private function getJobNo()
    {
        $branch = $this->myBranch();

        $job = DB::table('iv_cyclecount_job')
            ->where('branch_id', $branch)
            ->whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->count();

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = $job + 1;
        }
        $job_no = 'CC' . $branch . date('Y') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');

        return $job_no;
    }

    private function getStockBySKU($site_id, $product_code)
    {
        $data = DB::table('iv_stock_ledger')
            ->orderBy('product_code', 'ASC')
            ->where('qtya', '>', 0)
            ->where('site_id', $site_id)
            ->where('product_code', $product_code)
            ->where('branch_id', $this->myBranch())
            ->get();
        return $data;
    }

    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');
        return $data;
    }
}
