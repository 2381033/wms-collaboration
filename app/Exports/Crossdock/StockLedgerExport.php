<?php

namespace App\Exports\Crossdock;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockLedgerExport implements FromCollection, WithHeadings
{
    protected $id_branch = null;
    protected $id_warehouse = null;
    protected $id_customer = null;

    public function __construct($id_branch, $id_customer, $id_warehouse)
    {
        $this->id_branch = $id_branch;
        $this->id_warehouse = $id_warehouse;
        $this->id_customer = $id_customer;
    }

    private function getHeaderInbound($id)
    {
        $data = DB::table('cross_inbound_header')
            ->where('id', $id)
            ->first();

        return $data;
    }

    private function getCustomer($id)
    {
        $customer = DB::table('cross_mt_customer')
            ->where('id', $id)
            ->value('name');

        return $customer;
    }


    private function getWarehouse($id)
    {
        $warehouse = DB::table('cross_mt_warehouse')
            ->where('id', $id)
            ->value('name');

        return $warehouse;
    }

    public function collection()
    {
        $data = DB::table('cross_stock_ledger')
            ->where('id_branch', $this->id_branch)
            ->where('id_warehouse', $this->id_warehouse)
            ->where('on_hand', '>', 0)
            ->get();
        if ($this->id_customer > 0) {
            $data = $data->where('id_customer', $this->id_customer);
        } else {
            $data = $data;
        }

        $data->map(function ($value) {
            $value->header = $this->getHeaderInbound($value->id_inbound);
            $value->warehouse = $this->getWarehouse($value->id_warehouse);
            $value->customer = $this->getCustomer($value->id_customer);
        });


        if ($data->count() > 0) {
            $groupBy = $data->groupBy('id_customer');
            //DETAIL
            if ($this->id_customer > 0) {
                foreach ($data as $value) {
                    $weight_total[] = $value->on_hand * $value->w;
                    $cbm_total[] = $value->on_hand * $value->cbm_per_unit;

                    $list[] = [
                        "warehouse" => $value->warehouse,
                        "job_no" => $value->header->job_no,
                        "customer" => $value->customer,
                        "description" => $value->description,
                        "id_cargo" => $value->id_cargo,
                        "sku" => "'" . $value->sku,
                        "created_at" => formatTanggalIndonesia2($value->header->created_at),
                        "p" => $value->p,
                        "l" => $value->l,
                        "t" => $value->t,
                        "w" => $value->w,
                        "cbm_per_unit" => $value->cbm_per_unit,
                        "on_hand" => $value->on_hand,
                        "on_booking" => $value->on_booking == 0 ? '0' : $value->on_booking,
                        "on_actual" => $value->on_actual == 0 ? '0' : $value->on_actual,
                        "unit" => $value->unit,
                        "weight_total" => number_format(array_sum($weight_total), 1, '.', ''),
                        "cbm_total" => number_format(array_sum($cbm_total), 3, '.', ''),
                    ];
                }
            }
            //SUMMARY
            else {
                foreach ($groupBy as $key => $value) {
                    $list[] = [
                        "warehouse" => $data->where('id_customer', $key)->first()->warehouse ?? '-',
                        "customer" => $data->where('id_customer', $key)->first()->customer ?? '-',
                        "total_sku" =>  array_sum($data->where('id_customer', $key)->pluck('on_hand')->toArray()),
                        "weight_total" =>  array_sum($data->where('id_customer', $key)->pluck('w')->toArray()),
                        "cbm_total" =>  array_sum($data->where('id_customer', $key)->pluck('cbm_per_unit')->toArray()),
                    ];
                }
            }
            return new Collection($list);
        } else {
            Session::flash('warning', 'Data Not Found..');
            return back();
        }
    }

    public function headings(): array
    {
        if ($this->id_customer > 0) {
            return [
                "Warehouse",
                "Job No",
                "Customer",
                'Description',
                'Cargo ID',
                "SKU",
                "Date In",
                "P",
                "L",
                "T",
                "Weight Per Unit (Kg)",
                "Vol. Per Unit (Cbm)",
                "SOH",
                "SOB",
                "SOA",
                "UOM",
                "Weight Total (Kg)",
                "Vol. Total (Cbm)"
            ];
        } else {
            return [
                'Warehouse',
                'Customer',
                'Stock SKU',
                'Weight Total (Kg)',
                'Vol. Total (Cbm)',
            ];
        }
    }
}
