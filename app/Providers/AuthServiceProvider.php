<?php

namespace App\Providers;

use App\Link;
use App\Policies\LinkPolicy;
use App\ReadingList;
use App\Policies\ReadingListPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        ReadingList::class => ReadingListPolicy::class,
        Link::class        => LinkPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
