<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class SalutTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSalut()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }
}
