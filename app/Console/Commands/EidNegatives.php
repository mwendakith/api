<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Eid;

class EidNegatives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:negatives {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export list of positives to negatives.';

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
        $year = $this->argument('year');
        $eid = new Eid;
        $eid->negatives_report($year);
    }
}
