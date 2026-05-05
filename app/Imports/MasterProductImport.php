<?php

namespace App\Imports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class MasterProductImport implements ToCollection, WithHeadingRow
{
    public function  __construct($principal_id)
    {
        $this->principal_id = $principal_id;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $item) {
            if ($item['product_code']) {
                $master_principal = DB::table('iv_principal')->where('id', $this->principal_id)->count();
                $category         = DB::table('iv_product_category')->where('category_name', $item['category_code'])->where('principal_id', $this->principal_id)->count();
                $group_code       = DB::table('iv_product_group')->where('principal_id', $this->principal_id)->where('group_code', $item['group_code'])->count();
                $brand_code       = DB::table('iv_product_brand')->where('principal_id', $this->principal_id)->where('brand_code', $item['brand_code'])->count();
                $product_code     = DB::table('iv_product')->where('principal_id', $this->principal_id)->where('product_code', $item['product_code'])->count();


                // dd($category, $group_code, $brand_code, $collection,$this->principal_id,$item['category_code']);
                // print_r($item);

                if ($master_principal == 0) {
                    Session::flash('error', 'Principal does not exist');
                    return back();
                }
                if ($category == 0) {
                    Session::flash('error', 'Category Code does not exist');
                    return back();
                }
                if ($group_code == 0) {
                    Session::flash('error', 'Group Code does not exist');
                    return back();
                }
                if ($brand_code == 0) {
                    Session::flash('error', 'Brand Code does not exist');
                    return back();
                }
                if ($product_code > 0) {
                    Session::flash('error', 'Product code already exists');
                    return back();
                }
            }
        }

        foreach ($collection as $list) {
            if ($list['product_code']) {
                $master_principal = DB::table('iv_principal')->where('id', $this->principal_id)->first();
                $category         = DB::table('iv_product_category')->where('category_name', $list['category_code'])->where('principal_id', $this->principal_id)->first();
                $group_code       = DB::table('iv_product_group')->where('principal_id', $this->principal_id)->where('group_code', $list['group_code'])->first();
                $brand_code       = DB::table('iv_product_brand')->where('principal_id', $this->principal_id)->where('brand_code', $list['brand_code'])->first();
                DB::table('iv_product')->insert([
                    'company_id'    => '1',
                    'principal_id'  => $master_principal->id,
                    'product_code'  => $list['product_code'],
                    'product_name'  => $list['product_name'],
                    'category_id'   => $category->id,
                    'group_id' => $group_code->id,
                    'brand_id' => $brand_code->id,
                    'pick_criteria'  => $list['pick_criteria'],
                    'unit_level'  => '1',
                    'puom'  => $list['puom'],
                    'muom'  => $list['muom'],
                    'buom'  => $list['buom'],
                    'uppp'  => $list['uppp'],
                    'muppp'  => '1',
                    'manufactur_id'  => null,
                    'batch_flag'  => 'No',
                    'expired_flag'  => 'No',
                    'freeze_flag'  => 'No',
                    'length'  => $list['length'],
                    'width'  => $list['width'],
                    'dimensions_unit'  => $list['unit_dimensi'],
                    'volume'  => $list['volume'],
                    'volume_unit'  =>  $list['unit_volume'],
                    'gross_weight'  =>  $list['gross_weight'],
                    'net_weight'  =>  $list['net_weight'],
                    'weight_unit'  =>  $list['unit_weight'],
                    'temperature'  =>  $list['temperature'],
                    'shelf_life'  =>  $list['shelf_life'],
                    'freeze_day'  =>  0,
                    'base_price'  =>  0,
                    'active'  =>  'Yes',
                    'user_id'  =>  Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        Session::flash('success', 'Import Data berhasil di lakukan..');
    }
}
