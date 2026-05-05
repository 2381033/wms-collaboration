<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Stock\Occupancy as StockOccupancy;

class OccupancyDailyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'occupancy:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Occupancy Daily';

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
        $transaction_date = \Carbon\Carbon::today();
        $principal_list = DB::table("iv_principal")
            ->where("active", 'Yes')
            // ->where("id", 32)
            ->get();
        $pluck = $principal_list->pluck('id')->toArray();
        $transaction = DB::table('iv_stock_transaction')
            ->select('job_type', 'principal_id')
            ->whereIn('principal_id', $pluck)
            ->whereDate('created_at', date('Y-m-d'))
            ->get();
        $occupancy = [];
        foreach ($principal_list as $principal) {
            $location = DB::table("iv_principal_location as a")
                ->where("a.principal_id", $principal->id)
                ->count();
            $out = $transaction->where('job_type', 'EXP')->where('principal_id', $principal->id)->count();
            $in = $transaction->where('job_type', 'IMP')->where('principal_id', $principal->id)->count();
            if ($location > 0) {
                $location_count = DB::table("iv_stock_ledger as a")
                    ->select("a.location_code")
                    ->join("iv_principal_location as b", function ($query) {
                        $query->on("a.principal_id", "b.principal_id")
                            ->on("a.site_id", "b.site_id")
                            ->on("a.area_id", "b.area_id")
                            ->on("a.location_id", "b.location_id");
                    })
                    ->where("a.principal_id", $principal->id)
                    ->where("a.qtys", ">", 0)
                    ->groupBy("a.location_id")
                    ->get()
                    ->count();
            } else {
                $location_count = DB::table("iv_stock_ledger as a")
                    ->select("a.location_id")
                    ->where("a.principal_id", $principal->id)
                    ->where("a.qtys", ">", 0)
                    ->groupBy("a.location_id")
                    ->get()
                    ->count();
            }

            $occupancy[] = [
                'company_id' => $principal->company_id,
                'principal_id' => $principal->id,
                "transaction_date" => $transaction_date,
                "status_code" => "F",
                "qty" => $location_count,
                "in" => $in,
                "out" => $out,
            ];
        }

        StockOccupancy::insert($occupancy);

        return 0;
    }
}
