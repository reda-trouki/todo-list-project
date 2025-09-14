<?php

namespace App\Providers;

use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\TaskRepository;
use App\Services\Contracts\TaskServiceInterface;
use App\Services\TaskService;
use Illuminate\Support\ServiceProvider;

/**
 * Service Layer Service Provider
 * 
 * This provider binds interfaces to their concrete implementations,
 * following the Dependency Inversion Principle (DIP) from SOLID.
 * 
 * This allows for easy testing, maintainability, and follows clean
 * architecture principles.
 */
class ServiceLayerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * 
     * Bind interfaces to concrete implementations in the service container.
     * This enables dependency injection and makes testing easier.
     */
    public function register(): void
    {
        // Bind Repository Interface to Repository Implementation
        $this->app->bind(
            TaskRepositoryInterface::class,
            TaskRepository::class
        );

        // Bind Service Interface to Service Implementation
        $this->app->bind(
            TaskServiceInterface::class,
            TaskService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
