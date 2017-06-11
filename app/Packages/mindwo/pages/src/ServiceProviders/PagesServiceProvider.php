<?php

namespace mindwo\pages\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Log;

class PagesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(dirname(__DIR__). DIRECTORY_SEPARATOR . 'views', 'mindwo/pages');
        
        $this->loadTranslationsFrom(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lang', 'mindwo/pages');
               
        // Replace PHPOffice file to get correct date formats in CSV file
        $num_format_path = base_path('vendor' . DIRECTORY_SEPARATOR . 'phpoffice' . DIRECTORY_SEPARATOR . 'phpexcel' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'Style' . DIRECTORY_SEPARATOR . 'NumberFormat.php');
        if (File::isFile($num_format_path)) {
            $this->publishes([ dirname(__DIR__). DIRECTORY_SEPARATOR . 'vendor_replace' . DIRECTORY_SEPARATOR . 'PHPOffice' . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'NumberFormat.php' => $num_format_path,], 'setup');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom( dirname(__DIR__).'/config/mindwo.php', 'mindwo');
        $this->mergeConfigFrom( dirname(__DIR__).'/config/assets.php', 'assets');
        $this->mergeConfigFrom( dirname(__DIR__).'/config/dx.php', 'dx');        
        
        $this->app->make('mindwo\pages\Controllers\PagesController');
        $this->app->make('mindwo\pages\Controllers\BlockAjaxController');
        $this->app->make('mindwo\pages\Controllers\CalendarController');
        $this->app->make('mindwo\pages\Controllers\ImageController');
        $this->app->make('mindwo\pages\Controllers\ArticlesController');
    }
}
