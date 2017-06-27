<?php

namespace mindwo\pages\ServiceProviders;

use Config;
use Illuminate\Support\ServiceProvider;

/**
 * Helper funkciju inicializēšanas klase
 */
class HelpersServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        foreach (Config::get('mindwo.helpers') as $helper) {
            $helper_path = dirname(__DIR__) . '/Helpers/' . $helper;

            if (\File::isFile($helper_path)) {
                require_once $helper_path;
            }
        }
    }

}
