<?php

namespace App\Http\Controllers\NewUpdated\DashboardDODC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\FreezeStockDCMail as FreezeStockDCMail;
use App\Mail\UnfreezeStockDCMail;
use App\Models\Master\Principal as MasterPrincipal;
use Illuminate\Support\Facades\Session;

class DashboardDODCController extends Controller
{
    public function index()
    {
        return view('new.DashboardDODC.index');
    }

    private function pendingCard()
    {
        return DB::table('iv_outbound_job as j')
            ->leftJoin('iv_outbound_despatch as d', 'd.job_no', '=', 'j.job_no')
            ->leftJoin('iv_outbound_order as o', 'o.job_no', '=', 'j.job_no')
            ->leftJoin('iv_customer as c', 'c.id', '=', 'o.customer_id')
            ->where('j.principal_id', 3)
            ->where('j.confirmed_flag', 'No')
            ->whereNull('d.job_no')
            ->whereBetween('j.created_at', [
                now()->subMonths(2)->startOfDay(),
                now()->endOfDay(),
            ])
            ->select([
                'j.job_no',
                'o.order_no',
                'o.order_date',
                'o.due_date',
                'c.customer_name',
                'j.description',
            ])
            ->orderByDesc('j.created_at')
            ->get();
    }

    private function finishPreparedCard()
    {
        $data = DB::table('iv_outbound_job')
            ->whereBetween('created_at', [
                now()->subMonths(2)->startOfDay(),
                now()->endOfDay(),
            ])
            ->where('confirmed_flag', 'Yes')
            ->get()
            ->pluck('job_no')
            ->toArray();

        $results = DB::table('iv_outbound_despatch as a')
            ->join('iv_customer as b', 'b.id', '=', 'a.customer_id')
            ->leftJoin('iv_container_size as c', 'c.id', '=', 'a.size_id')
            ->leftJoin('iv_outbound_job as d', 'd.id', '=', 'a.outbound_id')
            ->where('a.principal_id', 3)
            ->whereBetween('a.created_at', [
                now()->subMonths(2)->startOfDay(),
                now()->endOfDay(),
            ])
            ->whereNotIn('a.job_no', $data)
            ->select([
                'a.job_no',
                'a.price',
                'a.reference_no',
                'a.created_at',
                'a.vehicle_no',
                'a.etd',
                'c.size_name',
                'b.customer_name',
                'd.description',
            ])
            ->orderBy('a.vehicle_no')
            ->orderByDesc('a.id')
            ->get()
            ->groupBy('vehicle_no')
            ->map(function ($items) {
                $latest = $items->first();
                $uniqueCustomers = $items->pluck('customer_name')->unique();
                $customers = $uniqueCustomers->implode(', ');
                $uniqueCustomerCount = $uniqueCustomers->count();
                $dropType = $uniqueCustomerCount > 1 ? 'Multi Drop' : 'Single Drop';
                $latest->drop_type = $dropType;
                $latest->customer_name = $customers;
                $latest->price = $latest->price ?? 0;
                $uniquePI = $items->pluck('reference_no')->unique();
                $PInumbers = $uniquePI->implode(', ');
                $latest->order_no = $PInumbers;

                return $latest;
            })
            ->values();
        return $results;
    }

    private function shippingCard()
    {
        $results = DB::table('iv_outbound_job as a')
            ->leftJoin('iv_outbound_despatch as b', 'b.job_no', '=', 'a.job_no')
            ->leftJoin('iv_customer as c', 'c.id', '=', 'b.customer_id')
            ->leftJoin('iv_container_size as d', 'd.id', '=', 'b.size_id')
            ->where('a.principal_id', 3)
            ->where('a.confirmed_flag', 'Yes')
            ->where('b.return', 'No')
            ->whereBetween('a.created_at', [
                now()->subWeek()->startOfDay(),
                now()->endOfDay(),
            ])
            ->select([
                'a.description',
                'a.job_no',
                'a.job_date',
                'a.confirmed_flag',
                'c.customer_name',
                'b.reference_no',
                'b.price',
                'b.vehicle_no',
                'b.created_at',
                'b.etd',
                'd.size_name',
            ])
            ->orderBy('b.vehicle_no')
            ->orderByDesc('b.created_at')
            ->get()
            ->groupBy('vehicle_no')
            ->map(function ($items) {
                $latest = $items->first();
                $uniquePrices = $items->pluck('price')
                    ->filter(fn($p) => $p !== null && $p !== '')
                    ->unique()
                    ->map(fn($p) => (int) $p)
                    ->values();

                if ($uniquePrices->count() === 1) {
                    $sumPrice = $uniquePrices->first();
                } else {
                    $sumPrice = $uniquePrices->sum();
                }

                $latest->total_price = number_format($sumPrice, 0, ',', '.');

                $uniqueCustomers = $items->pluck('customer_name')->unique();
                $customers = $uniqueCustomers->implode(', ');
                $uniqueCustomerCount = $uniqueCustomers->count();
                $uniquePI = $items->pluck('reference_no')->unique();
                $PInumbers = $uniquePI->implode(', ');

                // Tentukan drop type
                $latest->drop_type = $uniqueCustomerCount > 1 ? 'Multi Drop' : 'Single Drop';
                $latest->customer_name = $customers;
                $latest->order_no = $PInumbers;

                return $latest;
            })
            ->values();

        return $results;
    }

    private function doneCard()
    {
        $results = DB::table('iv_outbound_job as a')
            ->leftJoin('iv_outbound_despatch as b', 'b.job_no', '=', 'a.job_no')
            ->leftJoin('iv_customer as c', 'c.id', '=', 'b.customer_id')
            ->leftJoin('iv_container_size as d', 'd.id', '=', 'b.size_id')
            ->where('a.principal_id', 3)
            ->where('a.confirmed_flag', 'Yes')
            ->where('b.return', 'Yes')
            ->whereBetween('a.created_at', [
                now()->subWeek()->startOfDay(),
                now()->endOfDay(),
            ])
            ->select([
                'a.description',
                'a.job_no',
                'a.job_date',
                'a.confirmed_flag',
                'c.customer_name',
                'b.reference_no',
                'b.price',
                'b.vehicle_no',
                'b.created_at',
                'b.etd',
                'd.size_name',
            ])
            ->orderBy('b.vehicle_no')
            ->orderByDesc('b.created_at')
            ->get()
            ->groupBy('vehicle_no')
            ->map(function ($items) {
                $latest = $items->first();

                // Hitung harga unik
                $uniquePrices = $items->pluck('price')
                    ->filter(fn($p) => $p !== null && $p !== '')
                    ->unique()
                    ->map(fn($p) => (int) $p)
                    ->values();

                $sumPrice = $uniquePrices->count() === 1
                    ? $uniquePrices->first()
                    : $uniquePrices->sum();

                $latest->total_price = number_format($sumPrice, 0, ',', '.');

                // Customer & PI unik
                $uniqueCustomers = $items->pluck('customer_name')->unique();
                $uniquePI = $items->pluck('reference_no')->unique();

                $latest->customer_name = $uniqueCustomers->implode(', ');
                $latest->order_no = $uniquePI->implode(', ');
                $latest->drop_type = $uniqueCustomers->count() > 1 ? 'Multi Drop' : 'Single Drop';
                $latest->status = 'Done';

                return $latest;
            })
            ->values();

        return $results;
    }

    public function outstanding()
    {
        return view('new.DashboardDODC.outstanding');
    }

    public function getListOutstanding()
    {
        $results = DB::table('iv_outbound_job as a')
            ->leftJoin('iv_outbound_despatch as b', 'b.job_no', '=', 'a.job_no')
            ->leftJoin('iv_customer as c', 'c.id', '=', 'b.customer_id')
            ->leftJoin('iv_container_size as d', 'd.id', '=', 'b.size_id')
            ->where('a.principal_id', 3)
            ->where('a.confirmed_flag', 'Yes')
            ->where(function ($q) {
                $q->whereNull('b.return')
                    ->orWhere('b.return', '!=', 'Yes');
            })
            ->whereNotNull('b.vehicle_no')
            ->whereBetween('a.created_at', [
                now()->subWeek()->startOfDay(),
                now()->endOfDay(),
            ])
            ->select([
                'a.description',
                'a.job_no',
                'a.job_date',
                'a.confirmed_flag',
                'c.customer_name',
                'b.reference_no',
                'b.price',
                'b.vehicle_no',
                'b.created_at',
                'b.etd',
                'd.size_name',
            ])
            ->orderBy('b.vehicle_no')
            ->orderByDesc('b.created_at')
            ->get()
            ->groupBy('vehicle_no')
            ->map(function ($items) {
                $latest = $items->first();

                // Ambil semua job_no untuk kendaraan ini
                $jobNos = $items->pluck('job_no')->unique()->values();

                // Hitung harga total
                $uniquePrices = $items->pluck('price')
                    ->filter(fn($p) => $p !== null && $p !== '')
                    ->unique()
                    ->map(fn($p) => (int) $p)
                    ->values();

                $sumPrice = $uniquePrices->count() === 1
                    ? $uniquePrices->first()
                    : $uniquePrices->sum();

                // Buat format data
                $latest->total_price = number_format($sumPrice, 0, ',', '.');
                $latest->job_nos = $jobNos;
                $uniqueCustomers = $items->pluck('customer_name')->unique();
                $uniquePI = $items->pluck('reference_no')->unique();

                $latest->customer_name = $uniqueCustomers->implode(', ');
                $latest->order_no = $uniquePI->implode(', ');
                $latest->drop_type = $uniqueCustomers->count() > 1 ? 'Multi Drop' : 'Single Drop';
                $latest->status = 'Outstanding';

                return $latest;
            })
            ->values();

        return $results;
    }

    public function markAsDone(Request $request)
    {
        $jobNos = $request->input('job_nos');
        $price = $request->input('price');

        if (!is_array($jobNos) || count($jobNos) === 0) {
            return response()->json(['success' => false, 'message' => 'No job numbers provided.'], 400);
        }

        $jobNos = array_values(array_unique(array_map('strval', $jobNos)));

        $updateData = ['return' => 'Yes', 'updated_at' => now()];

        if ($price !== null) {
            if (!is_numeric($price)) {
                return response()->json(['success' => false, 'message' => 'Invalid price provided.'], 400);
            }
            // simpan price sebagai integer/string sesuai skema DB
            $updateData['price'] = (string) intval($price);
        }

        try {
            $affected = 0;

            DB::transaction(function () use ($jobNos, $updateData, &$affected) {
                $affected = DB::table('iv_outbound_despatch')
                    ->whereIn('job_no', $jobNos)
                    ->update($updateData);
            });

            if ($affected > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "{$affected} job(s) updated to Done.",
                    'updated_count' => $affected,
                ]);
            }

            return response()->json(['success' => false, 'message' => 'No matching jobs found or already updated.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update jobs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getData()
    {
        $pendingCard = $this->pendingCard();
        // dd($pendingCard);
        $finishPreparedCard = $this->finishPreparedCard();
        $shippingCard = $this->shippingCard();
        $doneCard = $this->doneCard();

        return response()->json([
            'pendingCard' => $pendingCard,
            'finishPreparedCard' => $finishPreparedCard,
            'shippingCard' => $shippingCard,
            'doneCard' => $doneCard,
        ]);
    }
}
