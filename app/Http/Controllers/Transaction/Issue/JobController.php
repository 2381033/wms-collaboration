<?php

namespace App\Http\Controllers\Transaction\Issue;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;


use App\Models\Transaction\Outbound\IssueReason as OutboundIssueReason;

class JobController extends Controller
{
    public $menu_name = "issue-reason";

    public function index(Request $request)
    {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $list_data = DB::table("iv_issue_reason as a")
                ->select('a.*',  "b.class_name", "e.customer_name")
                ->leftjoin("rs_classification  as b", "a.issue_id", "b.id")
                ->join("iv_outbound_job as c", "a.outbound_id", "c.id")
                ->join("iv_outbound_order as d", "c.id", "d.outbound_id")
                ->join("iv_customer as e", "d.customer_id", "e.id")
                ->where('a.principal_id', $request->principal_id)
                ->whereBetween('a.job_date', [$date_from, $date_to])
                ->orderBy("a.job_no", "desc")
                ->distinct()
                ->get();

            return datatables()->of($list_data)
                ->editColumn('job_date', function ($data) {
                    return date('d/m/Y', strtotime($data->job_date));
                })
                ->addColumn('job_no', function ($data) {
                    $button = "";
                    $button .= '<a href="' . URL("/issue-reason/create/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                    return $button;
                })
                ->editColumn('rating', function ($data) {
                    $star1 = "";
                    $star2 = "";
                    $star3 = "";
                    $star4 = "";
                    $star5 = "";
                    if ($data->rating == 5) {
                        $rate = 'Excellent';
                        $star5 = 'checked';
                        $star4 = 'checked';
                        $star3 = 'checked';
                        $star2 = 'checked';
                        $star1 = 'checked';
                    } else if ($data->rating == 4) {
                        $rate = 'Good';
                        $star4 = 'checked';
                        $star3 = 'checked';
                        $star2 = 'checked';
                        $star1 = 'checked';
                    } else if ($data->rating == 3) {
                        $rate = 'Fair';
                        $star3 = 'checked';
                        $star2 = 'checked';
                        $star1 = 'checked';
                    } else if ($data->rating == 2) {
                        $rate = 'Poor';
                        $star2 = 'checked';
                        $star1 = 'checked';
                    } else if ($data->rating == 1) {
                        $rate = 'Bad';
                        $star1 = 'checked';
                    }

                    // $rate = "
                    //     <div class='rate'>
                    //     <input type='radio' id='star5' name='rate' value='5' $star5  />
                    //     <label for='star5' title='Excellent'>5 stars</label>
                    //     <input type='radio' id='star4' name='rate' value='4' $star4 />
                    //     <label for='star4' title='Good'>4 stars</label>
                    //     <input type='radio' id='star3' name='rate' value='3' $star3 />
                    //     <label for='star3' title='Fair'>3 stars</label>
                    //     <input type='radio' id='star2' name='rate' value='2' $star2 />
                    //     <label for='star2' title='Poor'>2 stars</label>
                    //     <input type='radio' id='star1' name='rate' value='1' $star1 />
                    //     <label for='star1' title='Bad'>1 stars</label>
                    // </div>";

                    return $rate;
                })
                ->rawColumns(['job_no'])
                ->addIndexColumn()
                ->make(true);
        }

        return view("transaction.issue.index");
    }

    public function create($id = "")
    {
        return view('transaction.issue.create');
    }

    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');
        return $data;
    }

    public function myPrincipal()
    {
        $data = DB::table('users_principal')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('principal_id')->toArray();
        return $data;
    }

    public function getList()
    {
        $query = DB::table("iv_outbound_job as a")
            ->select(
                "a.id",
                "a.job_no",
                "a.job_date",
                "a.description",
                DB::raw("GROUP_CONCAT(b.order_no SEPARATOR ', ') as order_no"),
                DB::raw("GROUP_CONCAT(c.customer_name SEPARATOR ', ') as customer_name"),
                "b.po_number"
            )
            ->join("iv_outbound_order as b", "a.id", "b.outbound_id")
            ->join("iv_customer as c", "b.customer_id", "c.id")
            ->where("a.branch_id", $this->myBranch())
            ->whereIn("a.principal_id", $this->myPrincipal())
            ->whereYear("a.job_date", date('Y'))
            // ->whereYear("a.job_date", date('Y') - 1)
            ->whereNotIn("a.id", function ($query) {
                $query->select("c.outbound_id")
                    ->from("iv_issue_reason as c");
            })
            ->where("a.confirmed_flag", "Yes")
            ->groupBy("a.id", "a.job_no", "a.job_date", "a.description");

        return DataTables::of($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="row-check" value="' . $row->id . '">';
            })
            ->addColumn('rating', function ($row) {
                return '
                    <div class="rate">
                        <input type="radio" id="star5_' . $row->id . '" name="rating_' . $row->id . '" value="5"><label for="star5_' . $row->id . '"></label>
                        <input type="radio" id="star4_' . $row->id . '" name="rating_' . $row->id . '" value="4"><label for="star4_' . $row->id . '"></label>
                        <input type="radio" id="star3_' . $row->id . '" name="rating_' . $row->id . '" value="3"><label for="star3_' . $row->id . '"></label>
                        <input type="radio" id="star2_' . $row->id . '" name="rating_' . $row->id . '" value="2"><label for="star2_' . $row->id . '"></label>
                        <input type="radio" id="star1_' . $row->id . '" name="rating_' . $row->id . '" value="1"><label for="star1_' . $row->id . '"></label>
                    </div>
                ';
            })
            ->addColumn('notes', function ($row) {
                return '<input type="text" class="form-control form-control-sm" name="notes_' . $row->id . '">';
            })
            ->rawColumns(['check', 'rating', 'notes'])
            ->make(true);
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.id'        => 'required',
            'items.*.rating'    => 'required|integer|min:1|max:5',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user_name = Auth::user()->username;
                $principal_id = $this->myBranch();
                $job_no = $this->getJob($principal_id);
                foreach ($request->items as $item) {
                    if ($item['rating'] < 4 && empty($item['notes'])) {
                        throw new \Exception("Notes wajib diisi jika rating kurang dari 4.");
                    }
                    $order = DB::table('iv_outbound_order')
                        ->where('outbound_id', $item['id'])
                        ->first();

                    if (!$order) {
                        throw new \Exception("Order tidak ditemukan untuk outbound_id {$item['id']}");
                    }
                    OutboundIssueReason::updateOrCreate(
                        [
                            'outbound_id' => $item['id'],
                            'order_no'    => $order->order_no,
                        ],
                        [
                            'job_no'       => $job_no,
                            'job_date'     => now(),
                            'principal_id' => $principal_id,
                            'outbound_id'  => $item['id'],
                            'order_no'     => $order->order_no,
                            'rating'       => $item['rating'],
                            'issue_id'     => $item['issue_id'] ?? null,
                            'notes'        => $item['notes'] ?? null,
                            'user_id'      => $user_name,
                        ]
                    );
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    private function getJob($principal_id)
    {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = OutboundIssueReason::where('principal_id', $principal_id)
            ->whereYear('job_date', $year)
            ->whereMonth('job_date', $month)
            ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 6, 4) + 1;
        }

        $job_no = $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $job_no;
    }
}
