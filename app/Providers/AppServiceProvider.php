<?php

namespace App\Providers;

use App\Contracts\Services\Api\CategoryServiceInterface;
use App\Contracts\Services\Api\CommentHistoryServiceInterface;
use App\Contracts\Services\Api\CommentServiceInterface;
use App\Contracts\Services\Api\DepartmentServiceInterface;
use App\Contracts\Services\Api\RequestServiceInterface;
use App\Contracts\Services\Api\UserServiceInterface;
use App\Contracts\Services\Api\ForgotPasswordServiceInterface;
use App\Services\Api\CategoryService;
use App\Services\Api\CommentHistoryService;
use App\Services\Api\CommentService;
use App\Services\Api\DepartmentService;
use App\Services\Api\RequestService;
use App\Services\Api\UserService;
use Illuminate\Support\ServiceProvider;
use App\Services\Api\ForgotPasswordService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $services = [
            [
                UserServiceInterface::class,
                UserService::class
            ],
            [
                RequestServiceInterface::class,
                RequestService::class
            ],
            [
                CategoryServiceInterface::class,
                CategoryService::class
            ],
            [
                CommentHistoryServiceInterface::class,
                CommentHistoryService::class
            ],
            [
                CommentServiceInterface::class,
                CommentService::class
            ],
            [
                ForgotPasswordServiceInterface::class,
                ForgotPasswordService::class,
            ],
            [
               DepartmentServiceInterface::class,
                DepartmentService::class,
            ],
        ];
        foreach ($services as $service) {
            $this->app->bind(
                $service[0],
                $service[1]
            );
        }
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
