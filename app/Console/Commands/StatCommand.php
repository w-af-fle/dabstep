<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Show;
use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\StatMail;

class StatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:stat-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $article_count = Show::count();
        Show::whereNotNull('id')->delete();
        $comment_count = Comment::whereDate('created_at', Carbon::today)->count();
        Mail::to('mariaorel41@mail.ru')->send(new StatMail($article_count, $comment_count));
        //StatJob::dispatch($article_count, $content_count);
        //Log::alert($comment_count);

    }
}