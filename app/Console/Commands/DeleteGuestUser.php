<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeleteGuestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delete-guest-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command is use to delete the guest user from database';

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
        $users= User::where('email', 'like', '%' . '@example'. '%')
        ->where('is_guest', 1)->doesntHave('questionnaireStatesSummary')
       ->doesntHave('usersBookedConsultation')->doesntHave('userBySubscriptions')->delete();
       createDebugLogFile("Delete Guest User :",'delete-guest-user', $users);
    }
}
