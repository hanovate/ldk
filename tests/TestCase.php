<?php

namespace Unmit\Api\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Unmit\BusinessObjects\BusinessObjectsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            BusinessObjectsServiceProvider::class,
        ];
    }
    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        // Your code here
    }

}
