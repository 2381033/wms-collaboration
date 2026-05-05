<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScanCartonEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $shipper;
    protected $customer;
    protected $booking;
    protected $actual;
    protected $checker;
    protected $jobDate;
    protected $po;
    protected $type;

    public function __construct($shipper, $customer, $booking, $actual, $checker, $jobDate, $po, $type)
    {
        $this->shipper = $shipper;
        $this->customer = $customer;
        $this->booking = $booking;
        $this->actual = $actual;
        $this->checker = $checker;
        $this->jobDate = $jobDate;
        $this->po = $po;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'shipper' => $this->shipper,
            'customer' => $this->customer,
            'booking' => $this->booking,
            'actual' => $this->actual,
            'checker' => $this->checker,
            'jobDate' => $this->jobDate,
            'po' => $this->po,
            'type' => $this->type,
        ];
        return $this->view('transaction.inbound.mail', ['data' => $data]);
    }
}
