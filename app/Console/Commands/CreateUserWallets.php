<?php

namespace App\Console\Commands;

use App\Providers\GenericHelperServiceProvider;
use App\User;
use Illuminate\Console\Command;

class CreateUserWallets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create-wallets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create wallets for all users who do not have one';

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
        $users = User::all();
        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        foreach ($users as $user) {
            GenericHelperServiceProvider::createUserWallet($user);
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nWallets created successfully!");
    }
} 