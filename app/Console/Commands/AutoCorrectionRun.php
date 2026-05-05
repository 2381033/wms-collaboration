<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Stock\Ledger as stockLedger;

class AutoCorrectionRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoRun:Correction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Auto Correction Ledger stock by comparing to the movement transaction';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $exception = DB::transaction(function () {
            try {
                $BaseData = DB::table('iv_principal as a')
                    ->select('b.principal_id', 'b.branch_id', 'a.company_id', 'c.id as product_id', 'c.product_code')
                    ->join('iv_principal_branch as b', 'b.principal_id', 'a.id')
                    ->join('iv_product as c', 'c.principal_id', 'a.id')
                    ->get();

                $qtyCorrection = array();
                foreach ($BaseData as $key => $Base) {
                    $correction_remark = '';
                    $action_remark = '';
                    $stockPerMinQty = DB::select("CALL sp_stock_per_location_min_qty(?,?,?,?)", array($Base->company_id, $Base->branch_id, $Base->principal_id, $Base->product_id));
                    if (sizeof($stockPerMinQty) > 0) {
                        $stockPerMinQty = $stockPerMinQty[0];
                        if ($stockPerMinQty->qty >= 0) {
                            $serial = DB::table('iv_stock_ledger')
                                ->select(DB::raw('MAX(id) as id'), 'company_id', 'branch_id', 'principal_id', 'product_id', 'location_id', 'location_code','product_code','lot_no','mfg_date','exp_date','qtys','qtyp','qtya','site_id','area_id')
                                ->where('company_id', $Base->company_id)
                                ->where('branch_id', $Base->branch_id)
                                ->where('principal_id', $Base->principal_id)
                                ->where('product_id', $Base->product_id)
                                ->groupBy('product_id', 'location_id')
                                ->orderByDesc('id')
                                ->get();
                            // dd($serial);
                            foreach ($serial as $key => $serialvalue) {
                                if ($serialvalue->product_code == $Base->product_code) {
                                    $ledger = stockLedger::find($serialvalue->id);
                                    $data_process = stockLedger::find($serialvalue->id);
                                    $locationdata = DB::table('iv_location')
                                        ->where('id', $serialvalue->location_id)
                                        ->where('location_code', $serialvalue->location_code)
                                        ->first();
                                    $lewat = 0;
                                    if (isset($locationdata) || $lewat == 0) {
                                        if ($ledger->qtys != ($ledger->qtyp + $ledger->qtya)) {
                                            $selisih = 0;
                                            if ($ledger->qtys > ($ledger->qtyp + $ledger->qtya)) {
                                                $selisih = $ledger->qtys - ($ledger->qtyp + $ledger->qtya);
                                                $ledger->qtya = $ledger->qtya + $selisih;
                                                $ledger->save();
                                            } else if ($ledger->qtys < ($ledger->qtyp + $ledger->qtya)) {
                                                $nilaiqtyp = $ledger->qtys - $ledger->qtyp;
                                                $selisih = ($ledger->qtyp + $ledger->qtya) - $ledger->qtys;
                                                $ledger->qtys = $ledger->qtys + $selisih;
                                                $ledger->qtya = $nilaiqtyp;
                                                $ledger->save();
                                            }
                                        }
                                        $stockPerLocation = DB::select("CALL sp_stock_per_location_transaction(?,?,?,?,?)", array($ledger->company_id, $ledger->branch_id, $ledger->principal_id, $ledger->product_id, $ledger->location_id));
                                        if (sizeof($stockPerLocation) > 0) {
                                            $stockPerLocation = $stockPerLocation[0];

                                            if ($ledger->qtys != $stockPerLocation->qty) {
                                                $selisih = $stockPerLocation->qty - $data_process->qtys;
                                                $correction_remark = '';
                                                $action_remark = '';

                                                if ($ledger->qtya + $selisih < 0) {
                                                    $correction_remark = 'gagal update karena qty actual hasil pengurangan < 0';
                                                    $action_remark = "gagal koreksi data";
                                                } else if ($stockPerLocation->qty < 0) {
                                                    $correction_remark = 'gagal update karena qty transaction < 0';
                                                    $action_remark = "gagal koreksi data";
                                                } else if ($ledger->qtys + $selisih < 0) {
                                                    $correction_remark = 'gagal update karena qty onhand hasil pengurangan < 0';
                                                    $action_remark = "gagal koreksi data";
                                                } else {
                                                    $ledger->qtya = $ledger->qtya + $selisih;
                                                    $ledger->qtys = $stockPerLocation->qty;
                                                    $ledger->save();
                                                    $action_remark = "Adjust Ledger OnHand $selisih";
                                                }
                                                // $insertTabel = [
                                                //     'action' => "$action_remark",
                                                //     'correction_remark' => "$correction_remark"
                                                // ];
                                                // array_push($qtyCorrection, $insertTabel);
                                                DB::table('iv_stock_auto_adjustment_log')->insert([
                                                    'branch_id' => "$data_process->branch_id",
                                                    'company_id' => "$data_process->company_id",
                                                    'principal_id' => "$data_process->principal_id",
                                                    'product_id' => "$data_process->product_id",
                                                    'product_code' => "$stockPerLocation->product_code",
                                                    'lot_no' => "$data_process->lot_no",
                                                    'mfg_date' => "$data_process->mfg_date",
                                                    'exp_date' => "$data_process->exp_date",
                                                    'ledger_onhand' => "$data_process->qtys",
                                                    'ledger_booking' => "$data_process->qtyp",
                                                    'ledger_available' => "$data_process->qtya",
                                                    'transaction_onhand' => "$stockPerLocation->qty",
                                                    'variance' => "$selisih",
                                                    'site_id' => "$data_process->site_id",
                                                    'area_id' => "$data_process->area_id",
                                                    'location_id' => "$data_process->location_id",
                                                    'action' => "$action_remark",
                                                    'correction_date' => \Carbon\Carbon::now(),
                                                    'ledger_id' => "$data_process->id",
                                                    'correction_remark' => "$correction_remark"
                                                ]);
                                            }
                                        }
                                    } else {
                                        $correction_remark = 'data Location di Ledger berbeda dengan Data Master';
                                        $action_remark = 'gagal koreksi data';
                                        // $insertTabel = [
                                        //     'action' => "$action_remark",
                                        //     'correction_remark' => "$correction_remark"
                                        // ];
                                        // array_push($qtyCorrection, $insertTabel);
                                        DB::table('iv_stock_auto_adjustment_log')->insert([
                                            'branch_id' => "$serial->branch_id",
                                            'company_id' => "$serial->company_id",
                                            'principal_id' => "$serial->principal_id",
                                            'product_id' => "$serial->product_id",
                                            'product_code' => "$serial->product_code",
                                            'lot_no' => "$serial->lot_no",
                                            'mfg_date' => "$serial->mfg_date",
                                            'exp_date' => "$serial->exp_date",
                                            'ledger_onhand' => "$serial->qtys",
                                            'ledger_booking' => "$serial->qtyp",
                                            'ledger_available' => "$serial->qtya",
                                            'transaction_onhand' => "0",
                                            'variance' => "0",
                                            'site_id' => "$serial->site_id",
                                            'area_id' => "$serial->area_id",
                                            'location_id' => "$serial->location_id",
                                            'action' => "$action_remark",
                                            'ledger_id' => "$serial->id",
                                            'correction_date' => \Carbon\Carbon::now(),
                                            'correction_remark' => "$correction_remark"
                                        ]);
                                    }
                                } else {
                                    $correction_remark = 'Product Code di Ledger berbeda dengan Data Master';
                                    $action_remark = 'gagal koreksi data';
                                    // $insertTabel = [
                                    //     'action' => "$action_remark",
                                    //     'correction_remark' => "$correction_remark"
                                    // ];
                                    // array_push($qtyCorrection, $insertTabel);
                                    DB::table('iv_stock_auto_adjustment_log')->insert([
                                        'branch_id' => "$serialvalue->branch_id",
                                        'company_id' => "$serialvalue->company_id",
                                        'principal_id' => "$serialvalue->principal_id",
                                        'product_id' => "$serialvalue->product_id",
                                        'product_code' => "$serialvalue->product_code",
                                        'lot_no' => "$serialvalue->lot_no",
                                        'mfg_date' => "$serialvalue->mfg_date",
                                        'exp_date' => "$serialvalue->exp_date",
                                        'ledger_onhand' => "$serialvalue->qtys",
                                        'ledger_booking' => "$serialvalue->qtyp",
                                        'ledger_available' => "$serialvalue->qtya",
                                        'transaction_onhand' => "0",
                                        'variance' => "0",
                                        'site_id' => "$serialvalue->site_id",
                                        'area_id' => "$serialvalue->area_id",
                                        'location_id' => "$serialvalue->location_id",
                                        'action' => "$action_remark",
                                        'correction_date' => \Carbon\Carbon::now(),
                                        'correction_remark' => "$correction_remark"
                                    ]);
                                }
                            }
                        } else {
                            $correction_remark = 'terdapat nilai transaction dibawah 0';
                            $action_remark = 'gagal koreksi data';
                            // $insertTabel = [
                            //     'action' => "$action_remark",
                            //     'correction_remark' => "$correction_remark"
                            // ];
                            // array_push($qtyCorrection, $insertTabel);

                            DB::table('iv_stock_auto_adjustment_log')->insert([
                                'branch_id' => "$stockPerMinQty->branch_id",
                                'company_id' => "1",
                                'principal_id' => "$stockPerMinQty->principal_id",
                                'product_id' => "$stockPerMinQty->product_id",
                                'product_code' => "$stockPerMinQty->product_code",
                                'lot_no' => "",
                                'mfg_date' => "",
                                'exp_date' => "",
                                'ledger_onhand' => "0",
                                'ledger_booking' => "0",
                                'ledger_available' => "0",
                                'transaction_onhand' => "$stockPerMinQty->qty",
                                'variance' => "0",
                                'site_id' => "0",
                                'area_id' => "0",
                                'location_id' => "$stockPerMinQty->location_id",
                                'action' => "$action_remark",
                                'correction_date' => \Carbon\Carbon::now(),
                                'correction_remark' => "$correction_remark"
                            ]);
                        }
                    }
                }

                DB::commit();
                // dd($qtyCorrection);
                $message = ["success" => "Data successfully processed."];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                dd($e);
                $message = ["error" => $e->getMessage()];
                return $message;
            }
        });
        return $exception;
    }
}
