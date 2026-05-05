<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AuditPutawayDailyMail;
use Carbon\Carbon;

class AuditPutawayDailyCommand extends Command
{
    protected $signature = 'audit:putaway-daily';
    protected $description = 'Send daily audit putaway';

    public function handle()
    {

        $jobsGroup = DB::table('iv_inbound_job as a')
            ->select(
                'a.id',
                'a.job_no',
                'a.job_date',
                'a.branch_id',
                'b.principal_name',
                'b.multi_level',
                'br.branch_name',
            )
            ->join('iv_principal as b', 'a.principal_id', 'b.id')
            ->join('mt_branch as br', 'a.branch_id', 'br.id')
            ->whereDate('a.job_date', date('Y-m-d'))
            ->where('a.confirmed_flag', 'Yes')
            ->get()
            ->groupBy([
                'branch_id',
                fn($i) => date('Y-m-d', strtotime($i->job_date))
            ]);

        $email = DB::table('iv_email')->get();
        $email_to = $email->where('subject', 'Audit Putaway')
            ->pluck('email_to')
            ->flatMap(function ($item) {
                return array_map('trim', explode(';', $item));
            })
            ->toArray();

        $email_cc = $email->where('subject', 'IT')
            ->pluck('email_to')
            ->flatMap(function ($item) {
                return array_map('trim', explode(';', $item));
            })
            ->toArray();

        foreach ($jobsGroup as $branchId => $dates) {
            foreach ($dates as $jobDate => $jobs) {
                $jobIds = $jobs->pluck('id');
                $stocks = DB::table("iv_inbound_batch as a")
                    ->select(
                        "a.inbound_id",
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
                        "c.site_name",
                        "d.area_name",
                        "a.location_code",
                        "a.qty",
                        "a.mqty",
                        "a.bqty",
                        "b.puom",
                        "b.muom",
                        "b.buom"
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftJoin("iv_site as c", "a.site_id", "c.id")
                    ->leftJoin("iv_site_area as d", "a.area_id", "d.id")
                    ->whereIn("a.inbound_id", $jobIds)
                    ->get()
                    ->groupBy('inbound_id');
                try {
                    Mail::to($email_to)
                        ->cc($email_cc)
                        ->send(new AuditPutawayDailyMail($jobs, $stocks, $jobDate));
                } catch (\Throwable $e) {
                    \Log::error('MAIL FAILED', [
                        'error' => $e->getMessage()
                    ]);
                }


                // \Log::info('AUDIT PUTAWAY SENT', [
                //     'email' => 'ahmad.zakaria@samudera.id'
                // ]);
            }
        }

        $this->info('Daily audit putaway sent.');
        return Command::SUCCESS;
    }
}
