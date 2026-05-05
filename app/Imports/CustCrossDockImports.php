<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustCrossDockImports implements ToCollection, WithHeadingRow, WithValidation
{
    public function rules(): array
    {
        return [
            'branch' => 'required',
            'customer_name' => 'required',
        ];
    }

    public function __construct()
    {
    }

    public function collection(Collection $rows)
    {

        foreach ($rows as $val) {
            $branch = DB::table('mt_branch')
                ->where('branch_name', $val['branch'])
                ->first();
            if ($branch == null) {
                Session::flash('error', 'Branch ' . $val['branch'] . ' tidak di kenali..');
                return back();
            }

            $validate_cust = DB::table('cross_mt_customer')
                ->where('id_branch', $branch->id)
                ->where('name', $val['customer_name'])
                ->count();
            if ($validate_cust > 0) {
                Session::flash('error', 'Customer ' . $val['customer_name'] . ' Sudah ada di branch ' . $branch->branch_name);
                return back();
            }
        }

        foreach ($rows as $val) {
            $id_branch = DB::table('mt_branch')
                ->where('branch_name', $val['branch'])
                ->value('id');
            $data[] = [
                'id_branch' => $id_branch,
                'name'      => $val['customer_name'],
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => Auth::user()->username
            ];
        }
        $branch = DB::table('cross_mt_customer')->insert($data);

        Session::flash('success', 'Data has been imported successfully');
        return back();
    }
}
