<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\LedgerExport;

class StockLedgerExportSendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:stock_ledger_export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notification file stock ledger export.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = DB::table('ex_stock_ledger as stock')
            ->join('mt_forwarder as fwd', 'stock.shipper_id', '=', 'fwd.id')
            ->where('stock.status_flag', 'Inbound')
            ->select(
                'fwd.forwarder_name',
                'stock.peb_no',
                'stock.aju_no',
                'stock.po_number',
                'stock.destination',
                'stock.qty_cargo',
                'stock.pallet_id',
                'stock.created_at',
            )
            ->get();

        $list = $data;

        // Mengelompokkan data berdasarkan 'peb_no' dan 'po_number' yang sama
        $grouped = collect($list)->groupBy(function ($item) {
            return $item->peb_no . '-' . $item->po_number;  // Menggabungkan peb_no dan po_number sebagai kunci grup
        });

        // Menjumlahkan qty_cargo dan menghitung pallet_id yang unik
        $merged = $grouped->map(function ($group, $key) {
            $firstItem = $group->first();  // Mengambil item pertama sebagai representasi grup

            // Menetapkan qty_cargo hanya dari entri pertama dalam grup
            $firstItem->qty_cargo = $firstItem->qty_cargo;

            // Menghitung jumlah pallet_id yang unik dalam grup
            $firstItem->pallet_id = $group->pluck('pallet_id')->unique()->count();  // Jumlahkan jumlah pallet_id unik

            return $firstItem;  // Kembalikan item pertama yang sudah dihitung qty_cargo dan pallet_id-nya
        });

        if ($merged->count() > 0) {
            $this->sendEmail($merged);
        }

        return 0;
    }

    private function sendEmail($data)
    {
        $sendData = DB::table('ex_email')
            ->where("branch_id", 1)
            ->where("description", "Stock Ledger Export")
            ->first();

        if (isset($sendData)) {
            $list_to = explode(";", $sendData->email_to);
            $list_cc = explode(";", $sendData->email_cc);
            $list_bcc = explode(";", $sendData->email_bcc);

            $email_to = [];
            for ($i = 0; $i < count($list_to); $i++) {
                if (!empty($list_to[$i]) && $list_to[$i] !== "") {
                    $email_to[] = $list_to[$i];
                }
            }

            $email_cc = [];
            for ($i = 0; $i < count($list_cc); $i++) {
                if (!empty($list_cc[$i]) && $list_cc[$i] !== "") {
                    $email_cc[] = $list_cc[$i];
                }
            }

            $email_bcc = [];
            for ($i = 0; $i < count($list_bcc); $i++) {
                if (!empty($list_bcc[$i]) && $list_bcc[$i] !== "") {
                    $email_bcc[] = $list_bcc[$i];
                }
            }

            Mail::to($email_to)
                ->cc($email_cc)
                ->bcc($email_bcc)
                ->send(new LedgerExport($data));
        }
    }
}
