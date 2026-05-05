<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class OutboundOrderDetailImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use Importable, SkipsErrors;
    protected $id = null;
    public $error = [];
    
    public function collection(Collection $rows)
    {

    }

    public function withValidator($validator)
    {
    }

    public function onError(Throwable $e)
    {  
    }
}