<?php

namespace App\Http\Controllers\NewUpdated\MNRManagement;

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

class DashboardController extends Controller
{


    public function home()
    {
        return view('new.MNRManagement.Dashboard');
    }
}
