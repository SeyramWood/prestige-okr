<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    private array $repositories = [
        'ObjectiveInterface' => 'ObjectiveRepository',
        'KeyResultInterface' => 'KeyResultRepository',
    ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->bindRepositories();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    private function bindRepositories(): void
    {
        foreach ($this->repositories as $interface => $concrete) {

            $this->app->bind("App\\Contracts\\{$interface}", "App\\Repositories\\{$concrete}");
        }
    }
}
