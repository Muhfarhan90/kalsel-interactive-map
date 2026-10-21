<?php

namespace App\Providers;

use App\Models\Homepage;
use App\Models\Menu;
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
        //
        $homepage = Homepage::first();
        $sidebarMenus = Menu::with('categories')->where('is_active', true)->get();
        view()->share(
            ['sidebarMenus' => $sidebarMenus, 'homepage' => $homepage]);
    }
}
