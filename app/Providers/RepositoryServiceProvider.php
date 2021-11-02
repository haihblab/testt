<?php

namespace App\Providers;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\CommentHistoryRepositoryInterface;
use App\Contracts\Repositories\CommentRepositoryInterface;
use App\Contracts\Repositories\DepartmentRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\ForgotPasswordRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\CommentHistoryRepository;
use App\Repositories\CommentRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\RequestRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\ForgotPasswordRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    protected static $repositories = [
        'user' => [
            UserRepositoryInterface::class,
            UserRepository::class,
        ],
        'request' => [
            RequestRepositoryInterface::class,
            RequestRepository::class,
        ],
        'category' => [
            CategoryRepositoryInterface::class,
            CategoryRepository::class,
        ],
        'commenthistory' => [
            CommentHistoryRepositoryInterface::class,
            CommentHistoryRepository::class,
        ],
        'comment' => [
            CommentRepositoryInterface::class,
            CommentRepository::class,
        ],
        'department' => [
            DepartmentRepositoryInterface::class,
            DepartmentRepository::class,
        ],
        'role' => [
            RoleRepositoryInterface::class,
            RoleRepository::class,
        ],
        'forgotPassword' => [
            ForgotPasswordRepositoryInterface::class,
            ForgotPasswordRepository::class,
        ],
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (static::$repositories as $repository) {
            $this->app->singleton(
                $repository[0],
                $repository[1]
            );
        }
    }
}
