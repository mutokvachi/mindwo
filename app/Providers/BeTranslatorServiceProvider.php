<?php

namespace App\Providers;

use Illuminate\Translation\TranslationServiceProvider;

class BeTranslatorServiceProvider extends TranslationServiceProvider
{
    public function boot()
    {
        $this->app->singleton('translator', function($app)
        {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];

            $trans = new \App\Libraries\BeTranslator($loader, $locale);

            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });

        parent::boot();
    }
}