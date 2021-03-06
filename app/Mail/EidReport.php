<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EidReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $filePath = storage_path('exports/Negative_to_Positive.csv');
        $filePath2 = storage_path('exports/Positive_to_Negative.csv');
        return $this->view('report')->attach($filePath)->attach($filePath2);
    }
}
