<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Role\RoleRepository;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\Course\CourseRepository;
use App\Repositories\Group\GroupRepositoryInterface;
use App\Repositories\Group\GroupRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            \App\Repositories\Task\TaskRepositoryInterface::class,
            \App\Repositories\Task\TaskRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\TaskList\TaskListRepositoryInterface::class,
            \App\Repositories\TaskList\TaskListRepository::class,
        );
        $this->app->singleton(
            RoleRepositoryInterface::class,
            RoleRepository::class,
        );
        $this->app->singleton(
            CourseRepositoryInterface::class,
            CourseRepository::class,
        );
        $this->app->singleton(
            UserRepositoryInterface::class,
            UserRepository::class,
        );
        $this->app->singleton(
            GroupRepositoryInterface::class,
            GroupRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
