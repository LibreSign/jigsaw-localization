<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use TightenCo\Jigsaw\Container;

// use TightenCo\Jigsaw\File\ConfigFile;

class TestCase extends BaseTestCase
{
    public $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new Container;

        // $this->app->bind('config', function ($c) use ($cachePath) {
        //     $config = (new ConfigFile($c['cwd'] . '/config.php', $c['cwd'] . '/helpers.php'))->config;
        //     $config->put('view.compiled', $cachePath);
        //     return $config;
        // });
    }
}
