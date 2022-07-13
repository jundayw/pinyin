<?php

namespace Jundayw\PinYin;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Jundayw\PinYin\Support\FileDictLoader;

class PinYinServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/pinyin.php', 'pinyin');

        $this->app->singleton(PinYin::class, function($app) {
            return new PinYin($app->get('config')->get('pinyin'));
        });

        $this->app->alias(PinYin::class, 'pinyin');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/pinyin.php' => config_path('pinyin.php'),
            ], 'pinyin-config');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [PinYin::class, 'pinyin'];
    }
}
