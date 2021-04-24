<?php

namespace App\Providers;

use App\Contracts\Hr\EmployeeInterface;
use App\Contracts\Hr\SalaryInterface;
use App\Contracts\Merch\PoBomInterface;
use App\Repository\Hr\EmployeeRepository;
use App\Repository\Hr\SalaryRepository;
use App\Repository\Merch\PoBomRepository;
use Illuminate\Support\ServiceProvider;

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
        $this->app->bind(SalaryInterface::class, SalaryRepository::class);
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
