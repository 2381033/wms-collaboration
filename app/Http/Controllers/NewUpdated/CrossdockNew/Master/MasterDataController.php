<?php

namespace App\Http\Controllers\NewUpdated\CrossdockNew\Master;

use App\Http\Controllers\Controller;
use App\Imports\CustCrossDockImports;
use Illuminate\Http\Request;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UploadCustomerImport;
use App\Models\User;
use Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class MasterDataController extends Controller
{

    private function getMappingSite()
    {
        $data = DB::table('cross_user_warehouse')->get();
        $data->map(function ($value) {
            $value->user = DB::table('users')->where('id', $value->id_user)->first();
            $value->warehouse = DB::table('cross_mt_warehouse')->where('id', $value->id_warehouse)->first();
        });

        return $data;
    }

    private function getBranch()
    {
        $branch = DB::table('mt_branch')
            ->get();

        return $branch;
    }

    public function getListWarehouse()
    {
        $data = DB::table('cross_mt_warehouse')->where('status', 1)->get();
        $data->map(function ($value) {
            $value->branch = $this->getBranch()->where('id', $value->id_branch)->first()->branch_name ?? '-';
        });
        return datatables()->of($data)->make(true);
    }

    public function getListCustomer()
    {
        $data = DB::table('cross_mt_customer')->where('status', 1)->get();
        $data->map(function ($value) {
            $value->branch = $this->getBranch()->where('id', $value->id_branch)->first()->branch_name ?? '-';
        });
        return datatables()->of($data)->make(true);
    }

    public function index()
    {
        $branch  = $this->getBranch();
        $mapping = $this->getMappingSite();
        $group = $mapping->groupBy('id_user');
        $data_mapping = [];

        foreach ($group as $key => $value) {
            $data_mapping[$key] = [
                'name'  => $mapping->where('id_user', $key)->first()->user->name,
                'username'  => $mapping->where('id_user', $key)->first()->user->username,
                'warehouse' => $mapping->where('id_user', $key)->pluck('warehouse.name')->toArray(),
            ];
        }
        $users = DB::table('users')->orderBy('name', 'ASC')->where('active', 'Yes')->get();
        return view('new.CrossDock.MasterData.index', compact('branch', 'data_mapping', 'users'));
    }

    public function deleteWarehouse($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                DB::table('cross_mt_warehouse')->where('id', $id)
                    ->update([
                        'status' => 0
                    ]);
                DB::commit();
                $message = ['message' => 'Data Successfully Saved'];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function editWarehouse($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $message = DB::table('cross_mt_warehouse as a')
                    ->join('mt_branch as b', 'a.id_branch', '=', 'b.id')
                    ->where('a.id', $id)
                    ->first();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
    public function updateWarehouse(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $master = DB::table('cross_mt_warehouse')
                    ->where('id_branch', $request->id_branch)
                    ->where('name', $request->warehouse)
                    ->where('id', '!=', $request->id_warehouse)
                    ->count();

                if ($master > 0) {
                    DB::rollBack();
                    $message = ['message' => 'duplicate'];
                } else {
                    DB::table('cross_mt_warehouse')->where('id', $request->id_warehouse)
                        ->update([
                            'id_branch' =>  $request->id_branch,
                            'name'  => $request->warehouse,
                            'capacity'  => $request->capacity,
                        ]);
                    DB::commit();
                    $message = ['message' => 'Data Successfully Saved'];
                }

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function addWarehouse(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $master = DB::table('cross_mt_warehouse')
                    ->where('id_branch', $request->id_branch)
                    ->where('name', $request->warehouse)
                    ->count();
                if ($master > 0) {
                    DB::rollBack();
                    $message = ['message' => 'duplicate'];
                } else {
                    DB::table('cross_mt_warehouse')->insert([
                        'id_branch' =>  $request->id_branch,
                        'name'  => $request->warehouse,
                        'capacity'  => $request->capacity,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::user()->username
                    ]);
                    DB::commit();

                    $message = ['message' => 'Data Successfully Saved'];
                }

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function importCustomer(Request $request)
    {
        $this->validate($request, [
            'excel' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('excel');
        Excel::import(new CustCrossDockImports(), $file);
        return back();
    }

    public function addCustomer(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $master = DB::table('cross_mt_customer')
                    ->where('id_branch', $request->id_branch)
                    ->where('name', $request->customer)
                    ->count();
                if ($master > 0) {
                    DB::rollBack();
                    $message = ['message' => 'duplicate'];
                } else {
                    DB::table('cross_mt_customer')->insert([
                        'id_branch' =>  $request->id_branch,
                        'name'  => $request->customer,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::user()->username
                    ]);
                    DB::commit();

                    $message = ['message' => 'Data Successfully Saved'];
                }

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function deleteCustomer($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                DB::table('cross_mt_customer')->where('id', $id)
                    ->update([
                        'status' => 0
                    ]);
                DB::commit();
                $message = ['message' => 'Data Successfully Saved'];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function editCustomer($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $message = DB::table('cross_mt_customer as a')
                    ->join('mt_branch as b', 'a.id_branch', '=', 'b.id')
                    ->where('a.id', $id)
                    ->first();
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
    public function updateCustomer(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $master = DB::table('cross_mt_customer')
                    ->where('id_branch', $request->id_branch)
                    ->where('name', $request->customer)
                    ->count();
                if ($master > 0) {
                    DB::rollBack();
                    $message = ['message' => 'duplicate'];
                } else {
                    DB::table('cross_mt_customer')->where('id', $request->id_customer)
                        ->update([
                            'id_branch' =>  $request->id_branch,
                            'name'  => $request->customer,
                        ]);
                    DB::commit();
                    $message = ['message' => 'Data Successfully Saved'];
                }

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function getListMapping($id_user)
    {
        $master = $this->getMappingSite()->where('id_user', $id_user)->pluck('id_warehouse')->toArray();
        $data = DB::table('cross_mt_warehouse')->get();
        $access = $data->whereIn('id', $master);
        $unaccess = $data->whereNotIn('id', $master);
        $list = [
            'access' => $access,
            'unaccess' => $unaccess
        ];
        return response()->json($list);
    }

    public function addMapping(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                if (!$request->has('id_warehouse')) {
                    DB::rollBack();
                    $message = ['message' => 'null'];
                } else {
                    //hapus dulu
                    DB::table('cross_user_warehouse')->where('id_user', $request->id_user)->delete();
                    for ($i = 0; $i < count($request->id_warehouse); $i++) {
                        DB::table('cross_user_warehouse')->insert([
                            'id_user' => $request->id_user,
                            'id_warehouse' => $request->id_warehouse[$i],
                        ]);
                    }
                    DB::commit();
                    $message = ['message' => 'ok'];
                }

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function deleteMapping($id_user)
    {
        $exception = DB::transaction(function () use ($id_user) {
            try {
                DB::table('cross_user_warehouse')
                    ->where('id_user', $id_user)->delete();
                DB::commit();
                $message = ['message' => 'Data Successfully Saved'];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
