<?php

namespace App\Exports\BeaCukai;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MonthlyExport implements FromView
{
    protected $start = null;
    protected $end = null;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function view(): View
    {
        $data = DB::table("ex_bea_cukai as a")
            ->join('ex_bea_cukai_detail as b', 'a.id', 'b.id_header_bc')
            ->whereBetween('a.pkbe_date', [$this->start . ' 00:00:00', $this->end . ' 23:59:00'])
            ->get();

            $data = $data->map(function($value){
                $value->forwarder_name = $this->detailForwarder($value->forwarder_id);
                return $value;
          });

        return view('new.Export.BeaCukai.Report.monthly_excel', compact('data'));
    }

    private function detailForwarder($forwarder_id)
      {
            $data = DB::table("mt_forwarder")
                  ->where('id', $forwarder_id)
                  ->value('forwarder_name');
            return $data;
      }
}
