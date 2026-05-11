<?php

namespace App\Http\Controllers\NewUpdated\MNRManagement\Master;

use App\Http\Controllers\Controller;
use App\Models\Transaction\Stock\Ledger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Session;
use DataTables;
use Illuminate\Support\Carbon;

class LocationMasterController extends Controller
{
  

    public function index()
    {
        return view('new.MNRManagement.Master.Location.index');
    }

   
}
