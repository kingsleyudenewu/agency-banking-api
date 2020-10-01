<?php

namespace App\Console\Commands;

use App\Events\SendMessage;
use App\Events\SweepSaving;
use App\Koloo\SavingManagement;
use App\Koloo\User;
use App\Message;
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

      $infoMessage = '';

      if(!$savings->count())
      {
          $infoMessage = 'Nothing to sweep to day ' . now();
          $this->info($infoMessage);
      }

      foreach($savings as $saving)
      {
            if(is_null($saving->owner)) 
            {
                $infoMessage .= "Something is wrong with savings - " . $saving->id . " Could not be swept\n";
                $this->info($infoMessage);
                
                continue;
            }
                
            $infoMessage .= 'Sweeping saving ' . $saving->id  . ' total saved ' . $saving->amount_saved . ' owner: ' . $saving->owner->name . ' matured on: ' . $saving->maturity . "\n";
            $this->info($infoMessage);

            event(new SweepSaving($saving));
      }

      if($infoMessage)
      {
          $user = User::rootUser();
          $message = Message::create([
              'message' => $infoMessage,
              'message_type' => 'email',
              'user_id' => $user->getId(),
              'sender' => $user->getId(),
              'subject' => 'Sweep notification at ' . now()
          ]);

          event(new SendMessage($message, 'email'));
      }

    }
}
