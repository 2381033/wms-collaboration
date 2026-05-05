<?php

namespace App\Http\Controllers\Transaction\Issue;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Outbound\IssueReason as OutboundIssueReason;

class JobController extends Controller
{
    public $menu_name = "issue-reason";

    public function index(Request $request) {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $list_data = DB::table("iv_issue_reason as a")
                            ->select('a.*',  "b.class_name", "e.customer_name" )
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
                ->editColumn('job_date', function ($data)
                {
                    return date('d/m/Y', strtotime($data->job_date) );
                })
                ->addColumn('job_no', function($data){
                    $button = "";
                    $button .= '<a href="' . URL("/issue-reason/create/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                    return $button;
                })
                ->editColumn('rating', function ($data)
                {
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

    public function create($id = "") {
        // if (!GlobalHelpers::isAccess($this->menu_name)) {
        //     abort(403);
        // }
        $view = DB::table('iv_issue_reason as a')
                    ->select('a.*',  "b.class_name", "d.customer_name", "e.description" )
                    ->leftjoin("rs_classification  as b", "a.issue_id", "b.id")
                    ->join("iv_outbound_order as c", "a.outbound_id", "c.outbound_id")
                    ->join("iv_customer as d", "c.customer_id", "d.id")
                    ->join("iv_outbound_job as e", "a.outbound_id", "e.id")
                    ->where('a.id', $id)
                    ->first();

        $class_list = DB::table("rs_classification")
                        ->get();

        $data = [
             'view' => $view,
            'class_list' => $class_list,
        ];

        return view('transaction.issue.create', $data);
    }

    public function store(Request $request) {
        $rules = array(
            'principal_id' => 'required',
            'order_no' => 'required',
            'rate' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        if ($request->rate < 4) {
            $rules = array(
                'issue_id' => 'required',
                'notes' => 'required',
            );

            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }

        $exception = DB::transaction(function () use ($request) {
            try {
                $user_name = Auth::user()->username;
                $id = $request->id;

                $job = OutboundIssueReason::find($id);

                if (!isset($job)) {
                    $job = new OutboundIssueReason();

                    $job_no = $this->getJob($request->principal_id);

                    $job->job_no = $job_no;
                    $job->job_date = \Carbon\Carbon::today();
                } else {
                    $job_no = $job->job_no;
                }

                $job->principal_id = $request->principal_id;
                $job->outbound_id = $request->outbound_id;
                $job->order_no = $request->order_no;
                $job->rating = $request->rate;
                $job->issue_id = $request->issue_id;
                $job->notes = $request->notes;
                $job->user_id = $user_name;
                $job->save();

                DB::commit();

                $message = ['success'=>url('/issue-reason/create/' . $job->id)];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    private function getJob($principal_id) {
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
