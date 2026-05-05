<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\DB;

class TallySheetDetailExport implements FromView, WithDrawings
{
    protected $type;
    protected $data;
    // protected $data;
    // protected $summary;

    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function view(): View
    {
        return view('transaction.export.inbound.tally_sheet_download', [
            'data' => $this->data,
        ]);
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Samudera');
        $drawing->setPath(public_path('images/logos.png'));
        $drawing->setHeight(70);
        $drawing->setCoordinates('A1');
        return [$drawing];
    }
}
