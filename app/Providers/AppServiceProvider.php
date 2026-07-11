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
            $disk = env('SWITCH_TO_CLOUDINARY') === 'true' ? 'cloudinary' : env('FILESYSTEM_DISK', 'local');
            config(['filesystems.default' => $disk]);
            config(['filesystems.disks.cloudinary' => [
                'driver' => 'cloudinary',
                'key' => env('CLOUDINARY_API_KEY'),
                'secret' => env('CLOUDINARY_API_SECRET'),
                'cloud' => env('CLOUDINARY_CLOUD_NAME'),
                'url' => env('CLOUDINARY_URL'),
                'secure' => true,
            ]]);
        });
    }
}
