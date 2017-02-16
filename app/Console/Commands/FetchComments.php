<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LiveVideo;
use App\Models\Comment;
use App\Models\User;

class FetchComments extends Command
{
    private $fb;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will fetch comments from facebook for a all live vidoes with status of LIVE';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
    {
        parent::__construct();
        $this->fb = $fb;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
