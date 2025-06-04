<?php

namespace App\Providers;

use App\Http\View\Composers\HeaderComposer;
use Illuminate\Pagination\Paginator;
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
		Paginator::useBootstrapFive(); // Use Bootstrap 5 for pagination views

		View::composer('partials._header', HeaderComposer::class);
		View::composer('partials.home._category_nav_partial', HeaderComposer::class); // If you have a separate category nav partial for home
	}
}
