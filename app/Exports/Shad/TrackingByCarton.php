<?php

namespace App\Exports\Shad;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TrackingByCarton implements FromView
{
    protected $filteredInbound;
    protected $filteredOutbound;

    public function __construct($filteredInbound, $filteredOutbound)
    {
        $this->filteredInbound = $filteredInbound;
        $this->filteredOutbound = $filteredOutbound;
    }

    public function view(): View
    {
        return view('new.TrackingCarton.excel_carton', [
            'filteredInbound' => $this->filteredInbound,
            'filteredOutbound' => $this->filteredOutbound,
        ]);
    }
}
