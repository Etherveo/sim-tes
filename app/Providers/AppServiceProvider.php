<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Theme; // Gunakan Model Theme
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (Schema::hasTable('themes')) {
            $activeTheme = Theme::where('is_active', true)->first();
            $themeSettings = $activeTheme ? $activeTheme->toArray() : [];
            View::share('themeSettings', $themeSettings);
        }
    }
}