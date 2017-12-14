<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Vl;

class VlReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:vl {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export vl standard report.';

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
        $vl = new VL;
        $output = $vl->new_report($year);

        $this->info($output);
    }
}
