<?php

namespace Tests;

use ElaborateCode\JigsawLocalization\Mocks\PageMock;
use PHPUnit\Framework\TestCase as BaseTestCase;
use TightenCo\Jigsaw\Container;

class TestCase extends BaseTestCase
{
    public PageMock $pageData;

    public $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pageData = new PageMock();
        $this->app = Container::getInstance();
    }
}
