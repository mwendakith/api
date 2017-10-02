<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Eid;

class EidPositives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:positives {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export list of negatives to positives.';

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
        $eid->positives_report($year);
    }
}
