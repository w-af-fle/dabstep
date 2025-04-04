<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate; 
use App\Models\User;
use App\Models\Comment;
use Illuminate\Auth\Access\Response;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        Gate::before(function(User $user){
            if ($user->role === 'moderator') return true;
        });

        Gate::define('comment', function(User $user, Comment $comment)
        {
            return ($user->id === $comment->user_id) ?
                Response::allow():
                Response::deny('You don`t author this comment');
        });

        Gate::define('article', function(User $user)
        {
            return ($user->role == 'moderator') ?
                Response::allow():
                Response::deny('You don`t author this comment');
        });
    }
}
