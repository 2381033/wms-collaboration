<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AuditPutawayDailyExport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelExcel;

class AuditPutawayDailyMail extends Mailable
{
    public $jobs;
    public $stocks;
    public $jobDate;

    public function __construct($jobs, $stocks, $jobDate)
    {
        $this->jobs = $jobs;
        $this->stocks = $stocks;
        $this->jobDate = $jobDate;
    }

    public function build()
    {
        $branchName = $this->jobs->first()->branch_name;
        $branchId   = $this->jobs->first()->branch_id;

        $fileName = 'Inbound_Putaway_Audit_' .
            'Branch-' . $branchId . '_' .
            $this->jobDate . '.xlsx';

        $path = 'audit/' . $fileName;

        Excel::store(
            new AuditPutawayDailyExport($this->jobs, $this->stocks),
            $path,
            'local',
            ExcelExcel::XLSX
        );

        return $this->subject(
            'Daily Inbound Putaway Audit - ' . $branchName . ' - ' . $this->jobDate
        )
            ->view('email.auditPutawayDaily')
            ->attach(storage_path('app/' . $path));
    }
}
