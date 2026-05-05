<?php

namespace App\Imports;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Models\Master\Location as MasterLocation;

class LocationImport implements ToModel, WithHeadingRow
{
    use Importable;
    protected $site = null;
    protected $area = null;

    public function __construct($site, $area) {
        $this->site = $site;
        $this->area = $area;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $company_id = Auth::user()->company_id;

        return new MasterLocation([
            "company_id" => $company_id,
            "site_id" => $this->site,
            "area_id" => $this->area,
            "location_code" => $row['location_code'],
            "location_name" => $row['location_name'],
            "status_code" => $row['location_status'],
            "type_id" => $row['location_type'],
            "location_aisle" => $row['location_aisle'],
            "location_column" => $row['location_column'],
            "location_level" => $row['location_level'],
        ]);
    }
}
