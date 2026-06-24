<?php

namespace App\Console\Commands;

use App\Providers\ListsHelperServiceProvider;
use App\User;
use Illuminate\Console\Command;

class CreateDefaultLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create-default-lists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default lists (following and blocked) for all users who don\'t have them';

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
        $users = User::all();
        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        foreach ($users as $user) {
            ListsHelperServiceProvider::createUserDefaultLists($user->id);
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nDefault lists created for all users.");
        return 0;
    }
} 