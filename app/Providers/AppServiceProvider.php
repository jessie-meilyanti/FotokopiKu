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
        // Naikkan batas waktu eksekusi supaya proses berat (seed, upload besar) tidak timeout.
        if (function_exists('set_time_limit')) {
            @set_time_limit(0); // 0 = unlimited
        }

        // Cadangan: set juga konfigurasi PHP bila diperbolehkan oleh hosting.
        if (function_exists('ini_set')) {
            @ini_set('max_execution_time', '0'); // unlimited
        }
    }
}
