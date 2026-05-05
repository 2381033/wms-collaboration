<?php

namespace App\Http\Controllers\NewUpdated\CrossdockNew;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function myBranch()
    {
        $branch = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('branch_id')->toArray();

        return $branch;
    }

    private function getCustomer()
    {
        $customer = DB::table('cross_mt_customer')
            ->whereIn('id_branch', $this->myBranch())
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();

        return $customer;
    }

    private function whereCustomer($id)
    {
        $customer = DB::table('cross_mt_customer')
            ->where('id', $id)
            ->value('name');

        return $customer;
    }

    private function getBranch()
    {
        $branch = DB::table('mt_branch')
            ->whereIn('id', $this->myBranch())
            ->get();

        return $branch;
    }

    private function getWarehouse()
    {
        $data = DB::table('cross_user_warehouse')
            ->where('id_user', Auth::user()->id)
            ->get()->pluck('id_warehouse')->toArray();
        $data = DB::table('cross_mt_warehouse')
            ->where('status', 1)
            ->whereIn('id', $data)
            ->get();

        return $data;
    }

    private function whereWarehouse($id)
    {
        $data = DB::table('cross_mt_warehouse')
            ->where('id', $id)
            ->get();

        return $data;
    }


    public function index()
    {
        $customer       = $this->getCustomer();
        $warehouse      = $this->getWarehouse();
        $branch         = $this->getBranch();

        $permissions = DB::table('auth_group_permission')
            ->where('auth_group_id', Auth::user()->auth_group_id)
            ->pluck('auth_permission_id')
            ->toArray();

        $permissions = DB::table('auth_permission')
            ->whereIn('id', $permissions)
            ->pluck('name')
            ->toArray();

        return view('new.CrossDock.dashboard', compact('customer', 'warehouse', 'branch', 'permissions'));
    }

    public function getListJob($startDate, $endDate, $jobType, $statusJob)
    {
        $warehouse = $this->getWarehouse()->pluck('id')->toArray();
        $data = DB::table('cross_' . $jobType . '_header as a')
            ->leftJoin('cross_mt_warehouse as b', 'a.id_warehouse', 'b.id')
            ->leftJoin('cross_mt_customer as c', 'a.id_customer', 'c.id')
            ->select('a.*', 'b.name as warehouse_name', 'c.name as customer_name')
            ->whereBetween(\DB::raw('DATE(a.created_at)'), [$startDate, $endDate])
            ->whereIn('a.id_warehouse', $warehouse)
            ->where('a.confirmed_flag', $statusJob)
            ->get();
        // dd($data);
        // $data->map(function ($value) {
        //     $value->warehouse = $this->whereWarehouse($value->id_warehouse)->first()->name ?? '-';
        //     $value->customer = $this->whereCustomer($value->id_customer);
        // });

        return datatables()->of($data)->make(true);
    }
}
