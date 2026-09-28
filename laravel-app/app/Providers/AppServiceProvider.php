<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
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
        // Every public-site view (and the layout it renders into) needs the
        // singleton settings row for theme colours/fonts + header/footer
        // content. Sharing it here means controllers only need to pass
        // $settings explicitly when they also use it for their own logic.
        View::composer(['components.layouts.site', 'partials.site.*', 'site.*'], function ($view) {
            if (! array_key_exists('settings', $view->getData())) {
                $view->with('settings', Setting::current());
            }
        });
    }
}
