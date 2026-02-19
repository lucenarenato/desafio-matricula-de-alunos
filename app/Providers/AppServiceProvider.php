<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\{CursoRepository, StudentRepository, RegistrationRepository};
use App\Services\{CursoService, StudentService, RegistrationService};
use App\Managers\{AuthManager, MailManager, CacheManager};
use App\Adapters\NotificationManager;
use App\Models\{Curso, Student, Registration};
use App\Observers\{CursoObserver, StudentObserver, RegistrationObserver};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repository Singleton Bindings
        $this->app->singleton(CursoRepository::class, function () {
            return new CursoRepository(new Curso());
        });

        $this->app->singleton(StudentRepository::class, function () {
            return new StudentRepository(new Student());
        });

        $this->app->singleton(RegistrationRepository::class, function () {
            return new RegistrationRepository(new Registration());
        });

        // Register Service Singleton Bindings
        $this->app->singleton(CursoService::class, function () {
            return new CursoService(
                $this->app->make(CursoRepository::class),
                new NotificationManager(),
                CacheManager::getInstance()
            );
        });

        $this->app->singleton(StudentService::class, function () {
            return new StudentService(
                $this->app->make(StudentRepository::class),
                new NotificationManager(),
                CacheManager::getInstance()
            );
        });

        $this->app->singleton(RegistrationService::class, function () {
            return new RegistrationService(
                $this->app->make(RegistrationRepository::class),
                $this->app->make(CursoRepository::class),
                new NotificationManager(),
                CacheManager::getInstance()
            );
        });

        // Register Manager Singletons
        $this->app->singleton(AuthManager::class, function () {
            return AuthManager::getInstance();
        });

        $this->app->singleton(MailManager::class, function () {
            return MailManager::getInstance();
        });

        $this->app->singleton(CacheManager::class, function () {
            return CacheManager::getInstance();
        });

        $this->app->singleton(NotificationManager::class, function () {
            return new NotificationManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Model Observers
        Curso::observe(CursoObserver::class);
        Student::observe(StudentObserver::class);
        Registration::observe(RegistrationObserver::class);
    }
}
