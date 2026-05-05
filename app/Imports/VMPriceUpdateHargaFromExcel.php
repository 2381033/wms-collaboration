<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VMPriceUpdateHargaFromExcel implements ToCollection, WithHeadingRow
{
    protected $service = null;
    protected $mot = null;
    // protected $vendor = null;

    public function __construct($service, $mot) {
        $this->service = $service;
        $this->mot = $mot;
        // $this->vendor = $vendor;
    }

    private function objectMaster($id)
    {
        $data = DB::table('price_master')
            ->where('id', $id)
            ->first();

        return $data;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            try {
                if($this->service == 'FCL' and $this->mot == 'SEA'){
                    foreach($rows as $key => $refid) {
                        $validate = $this->objectMaster($refid['reference_id']);
                        if(is_null($validate)){
                            DB::rollBack();
                            Session::flash('error', 'Referance ID ' . $refid['reference_id'] .' Berbeda dengan data master, Periksa kembali excel yang akan di upload..');
                            return back();
                        }
                    }
                    foreach($rows as $key => $val) {
                        $old = $this->objectMaster($val['reference_id']);
                        DB::table('price_history')
                        ->insert([
                            'master_id' => $old->id,
                            'price_old' => $old->price,
                            'price_new' => $val['price'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->username
                        ]);

                        DB::table('price_fcl_sea')
                        ->where('id_master', $val['reference_id'])
                        ->update([
                            'trucking_origin' => $val['trucking_origin'],
                            'adm_bl' => $val['adm_bl'],
                            'segel' => $val['segel'],
                            'materai' => $val['materai'],
                            'apbs' => $val['apbs'],
                            'thc_lolo' => $val['thc_lolo'],
                            'ffs' => $val['ffs'],
                            'ocf' => $val['ocf'],
                            'thc_lolo_destinasi' => $val['thc_lolo_destinasi'],
                            'trucking_destinasi' => $val['trucking_destinasi'],
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::user()->username
                        ]);

                        DB::table('price_master')
                        ->where('id', $val['reference_id'])
                        ->update([
                            'price' => $val['price'],
                            'valid_untill' => '2099-02-02',
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::user()->username
                        ]);
                    }
                    DB::commit();
                    Session::flash('success', 'Data has been saved successfully');
                }else{
                    foreach($rows as $key => $refid) {
                        $validate = $this->objectMaster($refid['reference_id']);
                        if(is_null($validate)){
                            DB::rollBack();
                            Session::flash('error', 'Referance ID ' . $refid['reference_id'] .' Berbeda dengan data master, Periksa kembali excel yang akan di upload..');
                            return back();
                        }
                    }
                    foreach($rows as $key => $val_serv) {
                        $validate = $this->objectMaster($val_serv['reference_id']);
                        if($validate->service != $this->service){
                            DB::rollBack();
                            Session::flash('error', 'Data service yang ingin di update: ' . $this->service .' yang ada dalam excel: ' . $val_serv['service']);
                            return back();
                        }
                    }
                    foreach($rows as $key => $val_mot) {
                        $validate = $this->objectMaster($val_mot['reference_id']);
                        if($validate->mot != $this->mot){
                            DB::rollBack();
                            Session::flash('error', 'Data MOT yang ingin di update: ' . $this->mot .' yang ada dalam excel: ' . $val_mot['mot']);
                            return back();
                        }
                    }
                    foreach($rows as $key => $val) {
                        $old = $this->objectMaster($val['reference_id']);
                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val['valid_untill'])->format('Y-m-d');
                        DB::table('price_history')
                        ->insert([
                            'master_id' => $old->id,
                            'price_old' => $old->price,
                            'valid_untill_old' => $old->valid_untill,
                            'price_new' => $val['price'],
                            'valid_untill_new' => $date,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->username
                        ]);

                        DB::table('price_master')
                        ->where('id', $val['reference_id'])
                        ->update([
                            'price' => $val['price'],
                            'min_charge' => isset($val['min_charge']) ? $val['min_charge'] : null,
                            'valid_untill' => $date,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::user()->username
                        ]);
                    }
                    DB::commit();
                    Session::flash('success', 'Data has been updated successfully..');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('error', $e->getMessage());
            }
        });
        return back();
    }
}
