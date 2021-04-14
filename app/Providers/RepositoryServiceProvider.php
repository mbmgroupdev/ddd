<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Merch\PoBomInterface;
use App\Repository\Merch\PoBomRepository;
use App\Contracts\Hr\EmployeeInterface;
use App\Repository\Hr\EmployeeRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() 
    { 
        $this->app->bind(PoBomInterface::class, PoBomRepository::class);
        $this->app->bind(EmployeeInterface::class, EmployeeRepository::class);
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
