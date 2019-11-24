<?php

namespace Vipertecpro\Laravel\VisitorTracker\Test;

use Illuminate\Foundation\Application;
use Vipertecpro\Laravel\VisitorTracker\VisitorTrackerServiceProvider;
use Vipertecpro\Laravel\VisitorTracker\Tracker;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [VisitorTrackerServiceProvider::class];
    }

    /**
     * Load package alias
     * @param  Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'VisitorTracker' => Tracker::class,
        ];
    }
}
