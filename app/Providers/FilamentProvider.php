<?php

namespace App\Providers;

use App\Filament\Pages\Profile as FilamentProfile;
use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
class FilamentProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        Filament::serving(function () {
            Filament::registerUserMenuItems([
                //'logout' => UserMenuItem::make()->label('Log Out')->url(route('manager-panel.logout')),
                'account' => UserMenuItem::make()->url(FilamentProfile::getUrl())->label(__('My Profile')),
            ]);
        });





    }
}
