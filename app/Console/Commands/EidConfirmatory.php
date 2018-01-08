<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Eid;

class EidConfirmatory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:confirmatory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export reports concerning eid confirmatory tests.';

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
        $eid = new Eid;
        $output = $eid->confirmatory_report();
        $output .= $eid->confirmatory_report_two();
        $output .= $eid->confirmatory_positives_report();
        $output .= $eid->confirmatory_multiple();

        $this->info($output);
        $eid->send_confirm();   

    }
}
