<?php

namespace App\Http\Controllers\NewUpdated;

use App\Exports\Shad\TrackingByCarton;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Exports\Shad\TrackingBySku;

class TrackingCartonController extends Controller
{
    public function index()
    {
        return view("new.TrackingCarton.index");
    }

    public function search(Request $request)
    {
        $type = $request->type;
        if ($type == 'SKU') {
            return $this->searchBySku($request->all());
        } else {
            return $this->searchByCarton($request->all());
        }
    }

    private function searchBySku($array)
    {
        $inbound = DB::table('iv_inbound_detail as a')
            ->join("iv_inbound_job as b", "a.inbound_id", "b.id")
            ->whereIn('product_code', $array['product_code'])
            ->where('b.job_date', '>=', Carbon::now()->subMonths(3))
            ->where('b.confirmed_flag', 'Yes')
            ->whereNotNull('ean_code')
            ->orderBy('a.product_code', 'ASC')
            ->get();

        $groupedEanCodes = $inbound->groupBy('job_no')->map(function ($itemsByJobNo) {
            return $itemsByJobNo->groupBy('product_code')->map(function ($itemsByProductCode) {
                $firstItem = $itemsByProductCode->first();
                $eanCodes = $itemsByProductCode->flatMap(function ($item) {
                    return explode(',', $item->ean_code); // Pecah berdasarkan koma
                })->unique()->values()->toArray(); // Pastikan EAN unik dan urutkan
                return [
                    'product_code' => $firstItem->product_code,
                    'puom'         => $firstItem->puom,
                    'deskripsi'    => $firstItem->description,
                    'job_no'       => $firstItem->job_no,
                    'ean_codes'    => $eanCodes,
                    'created_at'   => Carbon::parse($firstItem->confirmed_date)->format('d-m-Y'),
                    'ean_count'    => count($eanCodes),
                    'vehicle'      => $firstItem->vehicle_no,
                ];
            });
        })->values();

        $outbound = DB::table("iv_outbound_batch as a")
            ->select(
                'a.outbound_id',
                'a.ean_code',
                'a.customer_id',
                'a.product_code',
                'a.qty',
                'a.puom',
                'b.id',
                'b.job_no',
                'b.confirmed_flag',
                'b.confirmed_date',
                'b.job_date',
                'c.id',
                'c.customer_name',
                'd.vehicle_no',
            )
            ->leftjoin("iv_outbound_job as b", "a.outbound_id", "b.id")
            ->leftjoin("iv_customer as c", "a.customer_id", "c.id")
            ->leftjoin("iv_outbound_despatch as d", "d.outbound_id", "b.id")
            ->whereIn('a.product_code', $array['product_code'])
            ->where("b.confirmed_flag", 'Yes')
            ->where('b.job_date', '>=', Carbon::now()->subMonths(3))
            ->whereNotNull('a.ean_code')
            ->get();

        $groupedOutbound = $outbound->groupBy('job_no')->map(function ($itemsByJobNo) {
            $first = $itemsByJobNo->first();
            $products = $itemsByJobNo->groupBy('product_code')->map(function ($productItems) {
                $firstProduct = $productItems->first();
                $eanCodes = $productItems->flatMap(function ($item) {
                    return $item->ean_code ? explode(',', $item->ean_code) : [];
                })->map(fn($code) => trim($code))->filter()->unique()->values();

                return [
                    'product_code' => $firstProduct->product_code,
                    'puom'         => $firstProduct->puom,
                    'ean_codes'    => $eanCodes,
                    'ean_code_count'  => $eanCodes->count(),
                ];
            })->values();

            return [
                'job_no'        => $first->job_no,
                'customer_name' => $first->customer_name,
                'vehicle_no'    => $first->vehicle_no,
                'job_date'      => \Carbon\Carbon::parse($first->confirmed_date)->format('d-m-Y'),
                'products'      => $products,
            ];
        })->values();

        return view("new.TrackingCarton.report_sku", compact('groupedEanCodes', 'groupedOutbound', 'array'));
    }
    private function searchByCarton($array)
    {
        $cartonID = $array['carton_id'];
        $inbound = DB::table('iv_inbound_detail as a')
            ->join("iv_inbound_job as b", "a.inbound_id", "b.id")
            ->where(function ($query) use ($cartonID) {
                foreach ($cartonID as $keyword) {
                    $query->orWhere('ean_code', 'like', "%{$keyword}%");
                }
            })
            ->where('b.job_date', '>=', Carbon::now()->subMonths(3))
            ->where('b.confirmed_flag', 'Yes')
            ->whereNotNull('ean_code')
            ->orderBy('a.product_code', 'ASC')
            ->get();
        $filteredInbound = $inbound->map(function ($item) use ($cartonID) {
            $eanList = explode(',', $item->ean_code);
            $matchingEans = array_intersect($eanList, $cartonID);

            if (count($matchingEans)) {
                $item->ean_code = implode(',', $matchingEans);
                return $item;
            }

            return null;
        })->filter(); // Hapus item yang tidak ada match

        $outbound = DB::table("iv_outbound_batch as a")
            ->select(
                'a.outbound_id',
                'a.ean_code',
                'a.customer_id',
                'a.product_code',
                'a.qty',
                'a.puom',
                'b.id',
                'b.job_no',
                'b.confirmed_flag',
                'b.confirmed_date',
                'b.job_date',
                'c.id',
                'c.customer_name',
                'd.vehicle_no',
                'b.confirmed_date',
            )
            ->leftjoin("iv_outbound_job as b", "a.outbound_id", "b.id")
            ->leftjoin("iv_customer as c", "a.customer_id", "c.id")
            ->leftjoin("iv_outbound_despatch as d", "d.outbound_id", "b.id")
            ->where(function ($query) use ($cartonID) {
                foreach ($cartonID as $keyword) {
                    $query->orWhere('ean_code', 'like', "%{$keyword}%");
                }
            })
            ->where("b.confirmed_flag", 'Yes')
            ->where('b.job_date', '>=', Carbon::now()->subMonths(3))
            ->whereNotNull('a.ean_code')
            ->groupBy('a.outbound_id')
            ->get();
        $filteredOutbound = $outbound->map(function ($item) use ($cartonID) {
            $eanList = explode(',', $item->ean_code);
            $matchingEans = array_intersect($eanList, $cartonID);

            if (count($matchingEans)) {
                $item->ean_code = implode(',', $matchingEans);
                return $item;
            }

            return null;
        })->filter(); // Hapus item yang tidak ada match
        return view("new.TrackingCarton.report_carton", compact('filteredInbound', 'filteredOutbound', 'cartonID'));
    }

    public function exportBySku(Request $request)
    {
        $inbound = DB::table('iv_inbound_detail as a')
            ->join("iv_inbound_job as b", "a.inbound_id", "b.id")
            ->whereIn('product_code', $request['product_code'])
            ->where('b.job_date', '>=', Carbon::now()->subMonths(3))
            ->where('b.confirmed_flag', 'Yes')
            ->whereNotNull('ean_code')
            ->orderBy('a.product_code', 'ASC')
            ->get();

        $groupedEanCodes = $inbound->groupBy('job_no')->map(function ($itemsByJobNo) {
            return $itemsByJobNo->groupBy('product_code')->map(function ($itemsByProductCode) {
                $firstItem = $itemsByProductCode->first();
                $eanCodes = $itemsByProductCode->flatMap(function ($item) {
                    return explode(',', $item->ean_code); // Pecah berdasarkan koma
                })->unique()->values()->toArray(); // Pastikan EAN unik dan urutkan
                return [
                    'product_code' => $firstItem->product_code,
                    'puom'         => $firstItem->puom,
                    'deskripsi'    => $firstItem->description,
                    'job_no'       => $firstItem->job_no,
                    'ean_codes'    => $eanCodes,
                    'created_at'   => Carbon::parse($firstItem->confirmed_date)->format('d-m-Y'),
                    'ean_count'    => count($eanCodes),
                    'vehicle'      => $firstItem->vehicle_no,
                ];
            });
        })->values();

        $outbound = DB::table("iv_outbound_batch as a")
            ->select(
                'a.outbound_id',
                'a.ean_code',
                'a.customer_id',
                'a.product_code',
                'a.qty',
                'a.puom',
                'b.id',
                'b.job_no',
                'b.confirmed_flag',
                'b.confirmed_date',
                'b.job_date',
                'c.id',
                'c.customer_name',
                'd.vehicle_no',
            )
            ->leftjoin("iv_outbound_job as b", "a.outbound_id", "b.id")
            ->leftjoin("iv_customer as c", "a.customer_id", "c.id")
            ->leftjoin("iv_outbound_despatch as d", "d.outbound_id", "b.id")
            ->whereIn('a.product_code', $request['product_code'])
            ->where("b.confirmed_flag", 'Yes')
            ->where('b.job_date', '>=', Carbon::now()->subMonths(3))
            ->whereNotNull('a.ean_code')
            ->get();

        $groupedOutbound = $outbound->groupBy('job_no')->map(function ($itemsByJobNo) {
            $first = $itemsByJobNo->first();
            $products = $itemsByJobNo->groupBy('product_code')->map(function ($productItems) {
                $firstProduct = $productItems->first();
                $eanCodes = $productItems->flatMap(function ($item) {
                    return $item->ean_code ? explode(',', $item->ean_code) : [];
                })->map(fn($code) => trim($code))->filter()->unique()->values();

                return [
                    'product_code' => $firstProduct->product_code,
                    'puom'         => $firstProduct->puom,
                    'ean_codes'    => $eanCodes,
                    'ean_code_count'  => $eanCodes->count(),
                ];
            })->values();

            return [
                'job_no'        => $first->job_no,
                'customer_name' => $first->customer_name,
                'vehicle_no'    => $first->vehicle_no,
                'job_date'      => \Carbon\Carbon::parse($first->confirmed_date)->format('d-m-Y'),
                'products'      => $products,
            ];
        })->values();

        return Excel::download(new TrackingBySku($groupedEanCodes, $groupedOutbound), 'Tracking-Carton-SKU.xlsx');
    }

    public function exportByCarton(Request $request)
    {
        $cartonID = $request->carton_id;
        $inbound = DB::table('iv_inbound_detail as a')
            ->join("iv_inbound_job as b", "a.inbound_id", "b.id")
            ->where(function ($query) use ($cartonID) {
                foreach ($cartonID as $keyword) {
                    $query->orWhere('ean_code', 'like', "%{$keyword}%");
                }
            })
            ->where('b.job_date', '>=', Carbon::now()->subMonths(3))
            ->where('b.confirmed_flag', 'Yes')
            ->whereNotNull('ean_code')
            ->orderBy('a.product_code', 'ASC')
            ->get();
        $filteredInbound = $inbound->map(function ($item) use ($cartonID) {
            $eanList = explode(',', $item->ean_code);
            $matchingEans = array_intersect($eanList, $cartonID);

            if (count($matchingEans)) {
                $item->ean_code = implode(',', $matchingEans);
                return $item;
            }

            return null;
        })->filter(); // Hapus item yang tidak ada match

        $outbound = DB::table("iv_outbound_batch as a")
            ->select(
                'a.outbound_id',
                'a.ean_code',
                'a.customer_id',
                'a.product_code',
                'a.qty',
                'a.puom',
                'b.id',
                'b.job_no',
                'b.confirmed_flag',
                'b.confirmed_date',
                'b.job_date',
                'c.id',
                'c.customer_name',
                'd.vehicle_no',
                'b.confirmed_date',
            )
            ->leftjoin("iv_outbound_job as b", "a.outbound_id", "b.id")
            ->leftjoin("iv_customer as c", "a.customer_id", "c.id")
            ->leftjoin("iv_outbound_despatch as d", "d.outbound_id", "b.id")
            ->where(function ($query) use ($cartonID) {
                foreach ($cartonID as $keyword) {
                    $query->orWhere('ean_code', 'like', "%{$keyword}%");
                }
            })
            ->where("b.confirmed_flag", 'Yes')
            ->where('b.job_date', '>=', Carbon::now()->subMonths(3))
            ->whereNotNull('a.ean_code')
            ->groupBy('a.outbound_id')
            ->get();
        $filteredOutbound = $outbound->map(function ($item) use ($cartonID) {
            $eanList = explode(',', $item->ean_code);
            $matchingEans = array_intersect($eanList, $cartonID);

            if (count($matchingEans)) {
                $item->ean_code = implode(',', $matchingEans);
                return $item;
            }

            return null;
        })->filter(); // Hapus item yang tidak ada match

        return Excel::download(new TrackingByCarton($filteredInbound, $filteredOutbound), 'Tracking-Carton.xlsx');
    }
}
