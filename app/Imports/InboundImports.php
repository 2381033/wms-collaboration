<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;
use Illuminate\Support\Facades\Session;
// use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithValidation;

class InboundImports implements ToCollection, WithHeadingRow
{
    // public function rules(): array
    // {
    //     return [
    //         'description' => 'required',
    //     ];
    // }

    protected $id_header;
    public function __construct($id_header)
    {
        $this->id_header = $id_header;
    }

    // public function sheets(): array
    // {
    //     return [
    //         0 => new InboundImports($this->id_header),
    //     ];
    // }

    public function collection(Collection $rows)
    {
        $id_cargo = [];
        $uom = [];
        $rules = DB::table('rt_uom')->get()->pluck('code')->toArray();
        foreach ($rows as $val) {
            $id_cargo[] = $val['id_cargo'];
            $uom[] = $val['uom'];
        }
        //validasi row yang sama dalam excel
        $temp_array = array_unique($id_cargo);
        // $validate_id_cargo = sizeof($temp_array) != sizeof($id_cargo);
        // dd($temp_array, sizeof($id_cargo));
        // if ($validate_id_cargo) {
        //     Session::flash('error', 'Duplicate ID Cargo..');
        //     return back();
        // }

        //validasi rules uom
        $validate_uom = array_diff($uom, $rules);
        if (count($validate_uom) > 0) {
            Session::flash('error', 'Periksa UOM, terdapat UOM yang tidak terdaftar..');
            return back();
        }
        $validate_db = DB::table('cross_inbound_detail')
            ->whereIn('id_cargo', $id_cargo)
            ->where('id_header', $this->id_header)
            ->count();

        if ($validate_db > 0) {
            Session::flash('error', 'Duplicate ID Cargo..');
            return back();
        }

        foreach ($rows as $row) {
            $cbm_per_unit = $row['p'] * $row['l'] * $row['t'] / 1000000;
            // $counting     = DB::table('cross_inbound_detail')->count();

            // $sku =  $counting == 0 ? 0 + 1 : $counting + 1;
            DB::table('cross_inbound_detail')->insert([
                'id_header' => $this->id_header,
                'unit'      => $row['uom'],
                // 'sku'       => date('dmy') . rand(1, 100) . $sku,
                'id_cargo'  => $row['id_cargo'],
                'p' => $row['p'],
                'l' => $row['l'],
                'w' => $row['weight'],
                't' => $row['t'],
                'cbm_per_unit' => $cbm_per_unit,
                'qty' => $row['qty'],
                'description' => $row['description'],
                'cbm_total' => $cbm_per_unit * $row['qty'],
                'date_in' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => Auth::user()->username,
                'status' => 2,
            ]);
        }
        Session::flash('success', 'Data has been imported successfully');
        return back();
    }
}
