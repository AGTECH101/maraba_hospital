<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $useCloudinary = filter_var(env('SWITCH_TO_CLOUDINARY', false), FILTER_VALIDATE_BOOLEAN);
            $disk = $useCloudinary ? 'cloudinary' : env('FILESYSTEM_DISK', 'local');

            config(['filesystems.default' => $disk]);

            if ($useCloudinary) {
                config(['filesystems.disks.cloudinary' => [
                    'driver' => 'cloudinary',
                    'key' => env('CLOUDINARY_API_KEY'),
                    'secret' => env('CLOUDINARY_API_SECRET'),
                    'cloud' => env('CLOUDINARY_CLOUD_NAME'),
                    'secure' => true,
                ]]);
            }
        });

        if (!$this->app->environment('local')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
