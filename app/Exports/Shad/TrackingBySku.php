<?php

namespace App\Exports\Shad;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TrackingBySku implements FromView
{
    protected $groupedEanCodes;
    protected $groupedOutbound;

    public function __construct($groupedEanCodes, $groupedOutbound)
    {
        $this->groupedEanCodes = $groupedEanCodes;
        $this->groupedOutbound = $groupedOutbound;
    }

    public function view(): View
    {
        return view('new.TrackingCarton.excel_sku', [
            'groupedEanCodes' => $this->groupedEanCodes,
            'groupedOutbound' => $this->groupedOutbound,
        ]);
    }
}
