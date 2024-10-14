<?php

namespace BahasTech\SizeBasedRotationLogging;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
    }

    /**
     * @throws BindingResolutionException
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/bt-rotation-logging.php',
            'bt-rotation-logging'
        );

        $config = $this->app->make('config');
        $config->set('logging.channels', array_merge(
            $config->get('bt-rotation-logging.channels', []),
            $config->get('logging.channels', [])
        ));
    }
};
