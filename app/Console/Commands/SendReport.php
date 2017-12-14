<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Eid;

class SendReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:send {type=EID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the report. Default is EID. Pass VL for viralload.';

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
     * @return mixed
     */
    public function handle()
    {
        //
        $type = $this->argument('type');

        if($type == "EID"){
            $eid = new Eid;
            $eid->send_report();
        }

        if($type == "VL"){
            $vl = new Vl;
            $vl->send_report();

        }


    }
}
