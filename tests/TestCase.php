<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Tests run without a developer's or production application's secrets.
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);
    }
}
