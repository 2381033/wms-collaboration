<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Inbound\Job as InboundJob;

class AutoCompleteController extends Controller
{
    public $page = 20;

    public function getInboundVehicle(Request $request)
    {
        $company_id = Auth::user()->company_id;

        $job = InboundJob::find($request->inbound_id);

        $vehicle = DB::table("iv_inbound_vehicle")
            ->where("company_id", $company_id)
            ->where("inbound_id", $request->inbound_id)
            ->get();

        $stock_status = DB::table("iv_stock_status")
            ->where("company_id", $company_id)
            ->where("principal_id", $job->principal_id)
            ->where("active", "Yes")
            ->get();

        $manufactur = DB::table("iv_manufactur")
            ->where("company_id", $company_id)
            ->where("principal_id", $job->principal_id)
            ->where("active", "Yes")
            ->get();

        $vehicle_list = array();

        foreach ($vehicle as $item) {
            $vehicle_list[] = array(
                "vehicle_no" => $item->vehicle_no
            );
        }

        $stock_status_list = array();

        foreach ($stock_status as $item) {
            $stock_status_list[] = array(
                "id" => $item->id,
                "status_name" => $item->status_name
            );
        }

        $manufactur_list = array();

        foreach ($manufactur as $item) {
            $manufactur_list[] = array(
                "id" => $item->id,
                "manufactur_name" => $item->manufactur_name
            );
        }

        $response = [
            "vehicle_list" => $vehicle_list,
            "stock_status_list" => $stock_status_list,
            "manufactur_list" => $manufactur_list,
        ];

        echo json_encode($response);
        exit;
    }

    public function getOutboundDetail(Request $request)
    {
        $company_id = Auth::user()->company_id;

        $job = InboundJob::find($request->inbound_id);

        $vehicle = DB::table("iv_outbound_detail")
            ->select("*")
            ->where("company_id", $company_id)
            ->where("inbound_id", $request->inbound_id)
            ->get();

        $stock_status = DB::table("iv_stock_status")
            ->where("company_id", $company_id)
            ->where("principal_id", $job->principal_id)
            ->where("active", "Yes")
            ->get();

        $manufactur = DB::table("iv_manufactur")
            ->where("company_id", $company_id)
            ->where("principal_id", $job->principal_id)
            ->where("active", "Yes")
            ->get();

        $vehicle_list = array();

        foreach ($vehicle as $item) {
            $vehicle_list[] = array(
                "vehicle_no" => $item->vehicle_no
            );
        }

        $stock_status_list = array();

        foreach ($stock_status as $item) {
            $stock_status_list[] = array(
                "id" => $item->id,
                "status_name" => $item->status_name
            );
        }

        $manufactur_list = array();

        foreach ($manufactur as $item) {
            $manufactur_list[] = array(
                "id" => $item->id,
                "manufactur_name" => $item->manufactur_name
            );
        }

        $response = [
            "vehicle_list" => $vehicle_list,
            "stock_status_list" => $stock_status_list,
            "manufactur_list" => $manufactur_list,
        ];

        echo json_encode($response);
        exit;
    }

    public function getStockProduct(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table("iv_stock_ledger as a")
                ->select(
                    "a.product_id",
                    "a.product_code",
                    "b.product_name",
                    "b.unit_level",
                    "b.puom",
                    "b.muom",
                    "b.buom",
                    "b.uppp",
                    "b.muppp",
                    DB::raw("sum(a.qtya) as qtya")
                )
                ->join("iv_product as b", "a.product_id", "b.id")
                ->join("users_principal as c", "a.principal_id", "c.principal_id")
                ->where("a.company_id", $company_id)
                ->where("c.user_id", $user_id)
                ->where("a.qtya", ">", 0)
                ->where("a.freeze_flag", "No")
                ->where("b.active", "Yes")
                ->where("a.principal_id", $request->principal_id)
                ->where(function ($query) use ($search_text) {
                    $query->where("a.product_code", "LIKE", $search_text)
                        ->orWhere("b.product_name", "LIKE", $search_text);
                })
                ->take($this->page)
                ->groupBy("a.product_id", "a.product_code", "b.product_name", "b.unit_level", "b.puom", "b.muom", "b.buom", "b.uppp", "b.muppp")
                ->get();

            foreach ($list as $k) {
                $pqty = ($k->qtya  - ($k->qtya % $k->uppp)) / $k->uppp;
                $mqty = (($k->qtya % $k->uppp) - (($k->qtya % $k->uppp) % $k->muppp)) / $k->muppp;
                $bqty = $k->qtya % $k->uppp % $k->muppp;

                $response[] = array(
                    "product_id" => $k->product_id,
                    "product_code" => $k->product_code,
                    "product_name" => $k->product_name,
                    "puom" => $k->puom,
                    "muom" => $k->muom,
                    "buom" => $k->buom,
                    "uppp" => $k->uppp,
                    "muppp" => $k->muppp,
                    "unit_level" => $k->unit_level,
                    "pqty" => number_format($pqty, 0, ',', '.'),
                    "mqty" => number_format($mqty, 0, ',', '.'),
                    "bqty" => number_format($bqty, 0, ',', '.')
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getOutboundOrder(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table('iv_outbound_order as a')
                ->select("a.id", "a.customer_id", "b.customer_name", "a.order_no", "a.order_date", "a.due_date")
                ->join('iv_customer as b', 'a.customer_id', 'b.id')
                ->join("users_principal as c", "a.principal_id", "c.principal_id")
                ->where("a.company_id", $company_id)
                ->where("c.user_id", $user_id)
                ->where("a.principal_id", $request->principal_id)
                ->where("a.outbound_id", $request->outbound_id)
                ->where(function ($query) use ($search_text) {
                    $query->Where('a.order_no', 'LIKE', $search_text)
                        ->orWhere("b.customer_name", "LIKE", $search_text);
                })
                ->take($this->page)
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "order_id" => $k->id,
                    "customer_id" => $k->customer_id,
                    "customer_name" => $k->customer_name,
                    "order_no" => $k->order_no,
                    "order_date" => $k->order_date,
                    "due_date" => $k->due_date,
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getStockSite(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table("iv_stock_ledger as a")
                ->select("a.site_id", "b.site_name")
                ->leftjoin("iv_site as b", "a.site_id", "b.id")
                ->join("users_site as c", "a.site_id", "c.site_id")
                ->where("a.company_id", $company_id)
                ->where("c.user_id", $user_id)
                ->where("a.qtya", ">", 0)
                ->where("a.freeze_flag", "No")
                ->where("a.principal_id", $request->principal_id)
                ->where('b.site_name', 'LIKE', $search_text)
                ->groupBy("a.site_id", "b.site_name")
                ->take($this->page)
                ->get();
        }

        $response = array();
        foreach ($list as $k) {
            $response[] = array(
                "site_id" => $k->site_id,
                "site_name" => $k->site_name,
            );
        }

        return response()->json($response);
        exit;
    }

    public function getStockLocation(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        $product_id = '%';
        $site_id = '%';
        $area_id = '%';
        if (!empty($request->product_id) && isset($request->product_id)) {
            $product_id = $request->product_id;
        }
        if (!empty($request->site_id) && isset($request->site_id)) {
            $site_id = $request->site_id;
        }
        if (!empty($request->area_id) && isset($request->area_id)) {
            $area_id = $request->area_id;
        }

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table("iv_stock_ledger as a")
                ->select("a.site_id", "b.site_name", "a.area_id", "c.area_name", "a.location_id", "a.location_code")
                ->leftjoin("iv_site as b", "a.site_id", "b.id")
                ->leftjoin("iv_site_area as c", "a.area_id", "c.id")
                ->join("users_site as d", "a.site_id", "d.site_id")
                ->where("a.company_id", $company_id)
                ->where("d.user_id", $user_id)
                ->where("a.qtya", ">", 0)
                ->where("a.freeze_flag", "No")
                ->where("a.principal_id", $request->principal_id)
                ->where("a.product_id", 'like', $product_id)
                ->where("a.site_id", 'like', $site_id)
                ->where("a.area_id", 'like', $area_id)
                ->where(function ($query) use ($search_text) {
                    $query->where("b.site_name", "LIKE", $search_text)
                        ->orWhere("c.area_name", "LIKE", $search_text)
                        ->orWhere("a.location_code", "LIKE", $search_text);
                })
                ->take($this->page)
                ->groupBy("a.site_id", "b.site_name", "a.area_id", "c.area_name", "a.location_id", "a.location_code")
                ->get();
        }

        $response = array();
        foreach ($list as $k) {
            $response[] = array(
                "site_id" => $k->site_id,
                "site_name" => $k->site_name,
                "area_id" => $k->area_id,
                "area_name" => $k->area_name,
                "location_id" => $k->location_id,
                "location_code" => $k->location_code,
            );
        }

        return response()->json($response);
        exit;
    }

    public function getStockBatch(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $response = array();

        $list = DB::table("iv_stock_ledger as a")
            ->select("a.lot_no", "a.document_ref")
            ->join("iv_product as b", "a.product_id", "b.id")
            ->join("users_principal as c", "a.principal_id", "c.principal_id")
            ->where("a.company_id", $company_id)
            ->where("c.user_id", $user_id)
            ->where("a.qtya", ">", 0)
            ->where("a.freeze_flag", "No")
            ->where("a.principal_id", $request->principal_id)
            ->where("a.product_id", $request->product_id)
            ->take($this->page)
            ->groupBy("a.lot_no", "a.document_ref")
            ->get();

        foreach ($list as $k) {
            $response[] = array(
                "lot_no" => $k->lot_no,
                "document_ref" => $k->document_ref,
            );
        }

        echo json_encode($response);
        exit;
    }

    public function getOutboundOrderCrossDock(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table('iv_outbound_order as a')
                ->select("a.id", "a.outbound_id", "a.job_no", "a.customer_id", "b.customer_code", "b.customer_name", "a.order_no", "a.order_date", "a.due_date")
                ->join('iv_customer as b', 'a.customer_id', 'b.id')
                ->join("users_principal as c", "a.principal_id", "c.principal_id")
                ->where("a.company_id", $company_id)
                ->where("c.user_id", $user_id)
                ->where("a.principal_id", $request->principal_id)
                ->where(function ($query) use ($search_text) {
                    $query->where("a.order_no", "LIKE", $search_text)
                        ->orWhere("a.job_no", "LIKE", $search_text)
                        ->orWhere("b.customer_name", "LIKE", $search_text);
                })
                ->take($this->page)
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "order_id" => $k->id,
                    "outbound_id" => $k->outbound_id,
                    "job_no" => $k->job_no,
                    "customer_id" => $k->customer_id,
                    "customer_code" => $k->customer_code,
                    "customer_name" => $k->customer_name,
                    "order_no" => $k->order_no,
                    "order_date" => \Carbon\Carbon::parse($k->order_date)->format("d/m/Y"),
                    "due_date" => \Carbon\Carbon::parse($k->due_date)->format("d/m/Y"),
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getContainerSize(Request $request)
    {
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table('iv_container_size as a')
                ->where('a.size_name', 'LIKE', $search_text)
                ->take($this->page)
                ->orderBy("a.size_name")
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "size_id" => $k->id,
                    "size_name" => $k->size_name,
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getConsignee(Request $request)
    {
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $branch_id = $this->getBranch();

            $list = DB::table('mt_consignee as a')
                ->where('a.consignee_name', 'LIKE', $search_text)
                ->whereIn('a.branch_id', $branch_id)
                ->take($this->page)
                ->orderBy("a.consignee_name")
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "consignee_id" => $k->id,
                    "consignee_name" => $k->consignee_name,
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getBranch()
    {
        $branch_id = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('branch_id')->toArray();
        return $branch_id;
    }

    public function getForwarder(Request $request)
    {
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }
            $branch_id = $this->getBranch();

            $list = DB::table('mt_forwarder as a')
                ->select("a.*")
                ->join("mt_forwarder_service as b", "a.id", "b.forwarder_id")
                ->join("mt_service as c", "b.service_id", "c.id")
                ->where('a.forwarder_name', 'LIKE', $search_text)
                ->where("c.service_name", $request->service_name)
                ->take($this->page)
                ->orderBy("a.forwarder_name")
                ->whereIn('a.branch_id', $branch_id)
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "forwarder_id" => $k->id,
                    "forwarder_name" => $k->forwarder_name,
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getShipper(Request $request)
    {
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $branch_id = $this->getBranch();

            $list = DB::table('mt_shipper as a')
                ->where('a.shipper_name', 'LIKE', $search_text)
                ->take($this->page)
                ->whereIn('a.branch_id', $branch_id)
                ->orderBy("a.shipper_name")
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "shipper_id" => $k->id,
                    "shipper_name" => $k->shipper_name,
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getForwarderStock(Request $request)
    {
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table('mt_forwarder as a')
                ->select("a.id", "a.forwarder_name", "a.storage_amount", "a.adm_amount")
                ->join("cy_stock_ledger as b", "a.id", "b.forwarder_id")
                ->where('a.forwarder_name', 'LIKE', $search_text)
                ->where("b.qtya", 1)
                ->where("b.branch_id", $request->branch_id)
                ->take($this->page)
                ->groupBy("a.id", "a.forwarder_name", "a.storage_amount", "a.adm_amount")
                ->orderBy("a.forwarder_name")
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "forwarder_id" => $k->id,
                    "forwarder_name" => $k->forwarder_name,
                    "storage_amount" => $k->storage_amount,
                    "adm_amount" => $k->adm_amount,
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getForwarderStockExport(Request $request)
    {
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table('mt_forwarder as a')
                ->select("a.id", "a.forwarder_name")
                ->join("ex_stock_ledger as b", "a.id", "b.forwarder_id")
                ->where('a.forwarder_name', 'LIKE', $search_text)
                ->where("b.branch_id", $request->branch_id)
                ->where("b.status_flag", "Inbound")
                ->take($this->page)
                ->groupBy("a.id", "a.forwarder_name")
                ->orderBy("a.forwarder_name")
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "forwarder_id" => $k->id,
                    "forwarder_name" => $k->forwarder_name,
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getForwarderInvoice(Request $request)
    {
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table('mt_forwarder as a')
                ->select("a.id", "a.forwarder_name")
                ->join("cy_invoice_header as b", "a.id", "b.forwarder_id")
                ->where('a.forwarder_name', 'LIKE', $search_text)
                ->where("b.confirmed_flag", "Confirmed")
                ->where("b.branch_id", $request->branch_id)
                ->take($this->page)
                ->groupBy("a.id", "a.forwarder_name")
                ->orderBy("a.forwarder_name")
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "forwarder_id" => $k->id,
                    "forwarder_name" => $k->forwarder_name,
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getForwarderOutbound(Request $request)
    {
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table('mt_forwarder as a')
                ->select("a.id", "a.forwarder_name", "a.storage_amount", "a.adm_amount")
                ->join("cy_outbound as b", "a.id", "b.forwarder_id")
                ->where('a.forwarder_name', 'LIKE', $search_text)
                ->where("b.confirmed_flag", "Confirmed")
                ->where("b.invoice_flag", "No")
                ->where("b.branch_id", $request->branch_id)
                ->take($this->page)
                ->groupBy("a.id", "a.forwarder_name", "a.storage_amount", "a.adm_amount")
                ->orderBy("a.forwarder_name")
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "forwarder_id" => $k->id,
                    "forwarder_name" => $k->forwarder_name,
                    "storage_amount" => $k->storage_amount,
                    "adm_amount" => $k->adm_amount,
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getContainer(Request $request)
    {
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table('cy_stock_ledger as a')
                ->select("a.*", "b.rate_amount", "c.free_storage")
                ->join("mt_forwarder_size as b", function ($join) {
                    $join->on("a.forwarder_id", "b.forwarder_id")
                        ->on("a.size_id", "b.size_id");
                })
                ->join("cy_invoice_type as c", "a.invoice_type", "c.id")
                ->where("a.forwarder_id", $request->forwarder_id)
                ->where('a.container_no', 'LIKE', $search_text)
                ->where("a.qtya", 1)
                ->take($this->page)
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "serial_id" => $k->id,
                    "serial_no" => $k->serial_no,
                    "container_no" => $k->container_no,
                    "job_date" => \Carbon\Carbon::parse($k->job_date)->format("d/m/Y"),
                    "rate_amount" => number_format($k->rate_amount, 0, ",", ""),
                    "free_storage" => $k->free_storage
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getInvoice(Request $request)
    {
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table('cy_invoice_header as a')
                ->select("a.*")
                ->where("a.forwarder_payment", $request->forwarder_id)
                ->where('a.job_no', 'LIKE', $search_text)
                ->where(DB::raw("CASE WHEN a.invoice_amount - a.payment_amount > 0 THEN 1 ELSE 0 END"), 1)
                ->where("a.payment_flag", "No")
                ->where("a.confirmed_flag", "Confirmed")
                ->take($this->page)
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "invoice_id" => $k->id,
                    "invoice_no" => $k->job_no,
                    "invoice_amount" => number_format($k->invoice_amount, 2, ",", "."),
                    "payment_amount" => number_format($k->invoice_amount, 2, ".", "")
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getContainerExport(Request $request)
    {
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $forwarder_id = $request->forwarder_id == "All" ? "%" : $request->forwarder_id;

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $list = DB::table('ex_outbound_header as a')
                ->select("a.*")
                ->where(DB::raw("COALESCE(a.forwarder_id, 0)"), "LIKE", $forwarder_id)
                ->where('a.container_no', 'LIKE', $search_text)
                ->whereBetween('a.job_date', [$date_from, $date_to])
                ->take($this->page)
                ->orderBy("a.container_no", "ASC")
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "container_no" => $k->container_no
                );
            }
        }

        echo json_encode($response);
        exit;
    }

    public function getOutboundOrderIssue(Request $request)
    {
        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $cut_out = '2022-04-01';

            $list = DB::table("iv_outbound_job as a")
                ->select(
                    "a.id",
                    "a.job_no",
                    "a.job_date",
                    "a.description",
                    "c.customer_name",
                    "b.order_no",
                    "b.po_number"
                )
                ->join("iv_outbound_order as b", "a.id", "b.outbound_id")
                ->join("iv_customer as c", "b.customer_id", "c.id")
                ->where("a.principal_id", $request->principal_id)
                ->whereDate("a.job_date", ">", $cut_out)
                ->whereNotIn("a.id", function ($query) {
                    $query->select("c.outbound_id")
                        ->from("iv_issue_reason as c");
                })
                ->where(function ($query) use ($search_text) {
                    $query->where("b.po_number", "LIKE", $search_text)
                        ->orWhere("b.order_no", "LIKE", $search_text);
                })
                ->where("a.confirmed_flag", "Yes")
                ->groupBy(
                    "a.id",
                    "a.job_no",
                    "a.job_date",
                    "a.description",
                    "c.customer_name",
                    "b.order_no",
                    "b.po_number"
                )
                ->get();

            foreach ($list as $k) {
                $response[] = array(
                    "outbound_id" => $k->id,
                    "order_no" => $k->order_no,
                    "po_number" => $k->po_number,
                    "customer_name" => $k->customer_name,
                    "description" => $k->description,
                );
            }
        }

        echo json_encode($response);
        exit;
    }
}
