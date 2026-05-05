<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Transfer\Job as TransferJob;
use App\Models\Transaction\Transfer\Detail as TransferDetail;

class ExportLocationImport implements ToCollection, WithHeadingRow
{
    protected $branch_id = null;
    public function __construct($branch_id)
    {
        $this->branch_id = $branch_id;
    }

    public function collection(Collection $rows)
    {
       DB::transaction(function () use ($rows) {
            try {
                foreach ($rows as $val) {
                    DB::table('ex_location')->insert([
                        'branch_id'     => $this->branch_id,
                        'location_code' => $val['location_code'],
                        'location_name' => $val['location_name'],
                        'location_aisle' => $val['location_aisle'],
                        'location_column' => $val['location_column'],
                        'location_level' => $val['location_level'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                DB::commit();
                Session::flash('success', 'Good Job, Data has been saved successfully..');
            }
            catch(\Exception $e) {
                DB::rollBack();
                Session::flash('error', $e->getMessage() . ' -> Netwok Connection Failed ');
            }
        });
        return back();
    }
}
