<?php

namespace App\Console\Commands;

use App\Mail\DailyReport as MailDailyReport;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily report of new users and posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $newUsers = User::whereDate('created_at', Carbon::today())->count();
            $newPosts = Post::whereDate('created_at', Carbon::today())->count();

            Mail::to('ahmedmanaour990@gmail.com')->send(new MailDailyReport($newUsers, $newPosts));

            $this->info('Daily report sent successfully!');
        } catch (\Exception $e) {
            $this->error('Failed to send daily report: ' . $e->getMessage());
        }
    }

}
