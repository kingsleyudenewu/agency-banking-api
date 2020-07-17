<?php

namespace App\Console\Commands;

use App\Events\SweepSaving;
use App\Koloo\SavingManagement;
use Illuminate\Console\Command;

class Sweeper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sweeper:sweep';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute sweeping operation';

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
      $savings = SavingManagement::getMaturedSavings();

      if(!$savings->count())
      {
          $this->info('Nothing to sweep to day ' . now());
          return;
      }

      foreach($savings as $saving)
      {

         $this->info('Sweeping saving ' . $saving->id  . ' total saved ' . $saving->amount_saved . ' owner: ' . $saving->owner->name . ' matured on: ' . $saving->maturity);

          event(new SweepSaving($saving));
      }

    }
}
