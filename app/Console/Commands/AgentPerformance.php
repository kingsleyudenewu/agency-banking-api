<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AgentPerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:populate-performance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the agent performance table';

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
        \App\Koloo\Stats\AgentPerformance::populateTable();
        return 0;
    }
}
