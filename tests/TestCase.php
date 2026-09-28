<?php

namespace Tests;

use LibreSign\JigsawLocalization\Mocks\PageMock;
use PHPUnit\Framework\TestCase as BaseTestCase;
use TightenCo\Jigsaw\Container;

class TestCase extends BaseTestCase
{
    public PageMock $pageData;

    public $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pageData = (new PageMock)->setLocalization([
            'en' => [],
            'ar' => [],
            'es' => [],
            'fr' => [],
            'fr-CA' => [],
            'haw-US' => [],
            'en-UK' => [],
            'pt-BR' => [],
            'raw-US' => [],
        ]);
        $this->app = Container::getInstance();
    }
}
