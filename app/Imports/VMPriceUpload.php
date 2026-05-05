<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class VMPriceUpload implements ToCollection, WithHeadingRow
{
    protected $service = null;
    protected $mot = null;

    public function __construct($service, $mot)
    {
        $this->service = $service;
        $this->mot = $mot;
    }

    private function validateDouble($origin, $destination, $prod_type, $vehicle_type, $vendor)
    {
        $data = DB::table('price_master')
            ->orderBy('price', 'ASC')
            ->where('service', $this->service)
            ->where('mot', $this->mot)
            ->where('origin', $origin)
            ->where('vendor', $vendor)
            ->where('destination', $destination)
            ->where('product_type', $prod_type)
            ->where('vehicle_type', $vehicle_type)
            ->where('active', 'Yes')
            ->first();
        return $data;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            try {
                foreach ($rows as $k_ => $v_) {
                    $validate = $this->validateDouble($v_['origin'], $v_['destination'], $v_['product_type'], $v_['vehicle_type'], $v_['vendor']);
                    if (!is_null($validate)) {
                        $number = $k_ + 1;
                        DB::rollBack();
                        Session::flash('error', 'Data already exists! line:' . $number . '');
                        return back();
                    }
                }
                foreach ($rows as $ky => $vl) {
                    if ($this->service != $vl['service']) {
                        DB::rollBack();
                        Session::flash('error', 'in excel service: ' . $vl['service'] . ' But You choose: ' . $this->service);
                        return back();
                    }
                }
                if ($this->service == 'FCL' and $this->mot == 'SEA') {
                    foreach ($rows as $key => $value) {
                        $counting = count($rows);
                        DB::table('price_master')->insert([
                            'service' => Str::upper($this->service),
                            'mot' => Str::upper($this->mot),
                            'vendor' => isset($value['vendor']) ? Str::upper($value['vendor']) : '-',
                            'origin' => Str::upper($value['origin']),
                            'destination' => Str::upper($value['destination']),
                            'product_type' => Str::upper($value['product_type']),
                            'vehicle_type' => isset($value['vehicle_type']) ? Str::upper($value['vehicle_type']) : null,
                            'price' => $value['price'],
                            'kota_kab' => $value['kota_kab'],
                            'uom' => isset($value['uom']) ? Str::upper($value['uom']) : null,
                            'min_charge' => isset($value['min_charge']) ? $value['min_charge'] : null,
                            'valid_untill' => '2099-02-02',
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->username
                        ]);
                    }
                    DB::commit();
                    $hourNow = Carbon::now()->format('Y-m-d H:00:00');
                    $latest = DB::table('price_master')
                        ->where('created_by', Auth::user()->username)
                        ->where('created_at', '>=', $hourNow)
                        ->get();
                    if ($counting != $latest->count()) {
                        DB::rollBack();
                        Session::flash('error', 'limited time, Please try again in the next 1 hour.');
                    } else {
                        $latest = $latest->pluck('id')->toArray();
                        foreach ($rows as $key => $values) {
                            DB::table('price_fcl_sea')->insert([
                                'id_master' => $latest[$key],
                                'shipping_line' => Str::upper($values['shipping_line']),
                                'trucking_origin' => $values['trucking_origin'],
                                'adm_bl' => $values['adm_bl'],
                                'segel' => $values['segel'],
                                'materai' => $values['materai'],
                                'apbs' => $values['apbs'],
                                'thc_lolo' => $values['thc_lolo'],
                                'ffs' => $values['ffs'],
                                'ocf' => $values['ocf'],
                                'thc_lolo_destinasi' => $values['thc_lolo_destinasi'],
                                'trucking_destinasi' => $values['trucking_destinasi'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => Auth::user()->username
                            ]);
                        }
                        DB::commit();
                        Session::flash('success', 'Data has been saved successfully');
                    }
                } else {
                    foreach ($rows as $key => $value) {
                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['valid_untill'])->format('Y-m-d');
                        DB::table('price_master')->insert([
                            'service' => Str::upper($this->service),
                            'mot' => Str::upper($this->mot),
                            'kota_kab' => $value['kota_kab'],
                            'vendor' => isset($value['vendor']) ? Str::upper($value['vendor']) : NULL,
                            'origin' => Str::upper($value['origin']),
                            'destination' => Str::upper($value['destination']),
                            'product_type' => Str::upper($value['product_type']),
                            'vehicle_type' => isset($value['vehicle_type']) ? Str::upper($value['vehicle_type']) : null,
                            'price' => $value['price'],
                            'uom' => isset($value['uom']) ? Str::upper($value['uom']) : null,
                            'min_charge' => isset($value['min_charge']) ? $value['min_charge'] : null,
                            'valid_untill' => $date,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->username
                        ]);
                    }
                    DB::commit();
                    Session::flash('success', 'Data has been saved successfully');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('error', $e->getMessage());
            }
        });
        return back();
    }
}
